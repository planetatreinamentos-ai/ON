<?php

namespace PlanetaTreinamentos\Controllers;

/**
 * Controller para formulário de contato
 */
class ContatoController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exibir página de contato
     */
    public function show(): void
    {
        $config = $this->config;

        $data = [
            'pageTitle' => 'Contato | ' . ($config['nome_empresa'] ?? 'Planeta Treinamentos'),
            'metaDescription' => 'Entre em contato com ' . ($config['nome_empresa'] ?? 'Planeta Treinamentos'),
            'config' => $config,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null,
            'old' => $_SESSION['old'] ?? []
        ];

        // Limpar sessão
        unset($_SESSION['success'], $_SESSION['error'], $_SESSION['old']);

        $this->render('public/contato', $data, null);
    }

    /**
     * Enviar formulário de contato
     */
    public function send(): void
    {
        // Verificar CSRF
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Token inválido. Tente novamente.';
            $this->redirect('/contato');
            return;
        }

        // Sanitizar dados
        $nome = trim(filter_var($_POST['nome'] ?? '', FILTER_SANITIZE_STRING));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $telefone = trim(filter_var($_POST['telefone'] ?? '', FILTER_SANITIZE_STRING));
        $assunto = trim(filter_var($_POST['assunto'] ?? '', FILTER_SANITIZE_STRING));
        $mensagem = trim(filter_var($_POST['mensagem'] ?? '', FILTER_SANITIZE_STRING));

        // Validação simples
        $errors = [];

        if (empty($nome) || strlen($nome) < 3) {
            $errors[] = 'Nome deve ter no mínimo 3 caracteres.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }

        if (empty($assunto)) {
            $errors[] = 'Assunto é obrigatório.';
        }

        if (empty($mensagem) || strlen($mensagem) < 10) {
            $errors[] = 'Mensagem deve ter no mínimo 10 caracteres.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('/contato');
            return;
        }

        // Proteção anti-spam (Honeypot)
        if (!empty($_POST['website'])) {
            $this->log('Tentativa de spam detectada no formulário de contato', 'warning', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            $_SESSION['success'] = 'Mensagem enviada com sucesso!';
            $this->redirect('/contato');
            return;
        }

        // Rate limiting (máximo 3 envios por hora por IP)
        if (!$this->checkRateLimit('contato', 3, 60)) {
            $_SESSION['error'] = 'Você atingiu o limite de envios. Tente novamente mais tarde.';
            $this->redirect('/contato');
            return;
        }

        // Obter configurações
        $config = $this->config;

        // Salvar no banco de dados primeiro
        $saved = $this->saveContactMessage([
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        if (!$saved) {
            $_SESSION['error'] = 'Erro ao processar sua mensagem. Tente novamente.';
            $this->redirect('/contato');
            return;
        }

        // Tentar enviar email (não crítico se falhar)
        try {
            $this->sendEmailToAdmin($nome, $email, $telefone, $assunto, $mensagem, $config);
        } catch (\Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
        }

        // Log
        $this->log('Mensagem de contato recebida', 'info', [
            'nome' => $nome,
            'email' => $email,
            'assunto' => $assunto
        ]);

        $_SESSION['success'] = 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
        $this->redirect('/contato');
    }

    /**
     * Salvar mensagem de contato no banco
     */
    private function saveContactMessage(array $data): bool
    {
        try {
            $sql = "INSERT INTO mensagens_contato (
                nome, email, telefone, assunto, mensagem, 
                ip_address, created_at
            ) VALUES (
                :nome, :email, :telefone, :assunto, :mensagem,
                :ip_address, NOW()
            )";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            error_log("Erro ao salvar mensagem de contato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email para admin usando mail() nativo
     */
    private function sendEmailToAdmin(string $nome, string $email, string $telefone, string $assunto, string $mensagem, array $config): void
    {
        $to = $config['email_contato'] ?? $config['email_admin'] ?? '';
        
        if (empty($to)) {
            error_log("Email admin não configurado");
            return;
        }

        $subject = "Contato do site: {$assunto}";
        
        $message = "Nova mensagem de contato\n\n";
        $message .= "Nome: {$nome}\n";
        $message .= "Email: {$email}\n";
        $message .= "Telefone: " . ($telefone ?: 'Não informado') . "\n";
        $message .= "Assunto: {$assunto}\n\n";
        $message .= "Mensagem:\n{$mensagem}\n\n";
        $message .= "---\n";
        $message .= "Enviado em: " . date('d/m/Y H:i:s') . "\n";
        $message .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

        $headers = "From: " . ($config['email_contato'] ?? 'noreply@planetatreinamentos.com.br') . "\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        @mail($to, $subject, $message, $headers);
    }
}
