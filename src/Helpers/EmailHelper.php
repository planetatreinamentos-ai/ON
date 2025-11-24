<?php

namespace PlanetaTreinamentos\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

/**
 * EmailHelper
 * Helper para envio de emails via SMTP
 */
class EmailHelper
{
    private PHPMailer $mailer;
    private array $config;
    private PDO $db;

    public function __construct()
    {
        // Obter configurações
        $this->config = $this->getEmailConfig();
        
        // Configurar PHPMailer
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    /**
     * Obter configurações de email
     */
    private function getEmailConfig(): array
    {
        // Tentar obter do .env
        $config = [
            'host' => $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: 'smtp.hostinger.com',
            'port' => $_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT') ?: 587,
            'username' => $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME') ?: '',
            'password' => $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD') ?: '',
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS') ?: '',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME') ?: 'Planeta Treinamentos',
        ];

        return $config;
    }

    /**
     * Configurar PHPMailer
     */
    private function setupMailer(): void
    {
        try {
            // Configuração SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['encryption'];
            $this->mailer->Port = $this->config['port'];
            
            // Configurações gerais
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            
            // Debug (desabilitar em produção)
            $this->mailer->SMTPDebug = 0;
            
        } catch (Exception $e) {
            error_log("Erro ao configurar mailer: " . $e->getMessage());
        }
    }

    /**
     * Enviar email simples
     */
    public function send(string $to, string $subject, string $body, string $toName = ''): bool
    {
        try {
            // Limpar destinatários anteriores
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Configurar destinatário
            $this->mailer->addAddress($to, $toName);
            
            // Configurar conteúdo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            // Enviar
            $result = $this->mailer->send();
            
            // Log de sucesso
            $this->logEmail($to, $subject, 'sent');
            
            return $result;
            
        } catch (Exception $e) {
            // Log de erro
            $this->logEmail($to, $subject, 'failed', $e->getMessage());
            error_log("Erro ao enviar email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email com template
     */
    public function sendTemplate(string $to, string $subject, string $template, array $data = []): bool
    {
        $body = $this->renderTemplate($template, $data);
        return $this->send($to, $subject, $body);
    }

    /**
     * Renderizar template de email
     */
    private function renderTemplate(string $template, array $data): string
    {
        $templatePath = __DIR__ . '/../../views/email/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            error_log("Template de email não encontrado: $template");
            return '';
        }

        // Extrair variáveis
        extract($data);
        
        // Capturar output
        ob_start();
        require $templatePath;
        $html = ob_get_clean();
        
        return $html;
    }

    /**
     * Enviar email de boas-vindas
     */
    public function sendWelcome(string $email, string $nome, string $senha): bool
    {
        $data = [
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'login_url' => $_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br'
        ];

        return $this->sendTemplate(
            $email,
            'Bem-vindo ao Planeta Treinamentos',
            'welcome',
            $data
        );
    }

    /**
     * Enviar email de pré-cadastro
     */
    public function sendPreCadastro(string $email, string $nome, string $link, string $tipo = 'aluno'): bool
    {
        $data = [
            'nome' => $nome,
            'link' => $link,
            'tipo' => $tipo
        ];

        $subject = $tipo === 'aluno' ? 
            'Complete seu cadastro - Aluno' : 
            'Complete seu cadastro - Professor';

        return $this->sendTemplate(
            $email,
            $subject,
            'pre-cadastro',
            $data
        );
    }

    /**
     * Enviar certificado
     */
    public function sendCertificado(string $email, string $nome, string $curso, string $linkCertificado): bool
    {
        $data = [
            'nome' => $nome,
            'curso' => $curso,
            'link_certificado' => $linkCertificado,
            'link_verificacao' => $_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br'
        ];

        return $this->sendTemplate(
            $email,
            'Seu certificado está pronto!',
            'certificado',
            $data
        );
    }

    /**
     * Enviar contato do formulário público
     */
    public function sendContato(string $nomeRemetente, string $emailRemetente, string $assunto, string $mensagem): bool
    {
        $emailAdmin = $this->config['from_address'];
        
        $body = "
        <h2>Nova mensagem de contato</h2>
        <p><strong>Nome:</strong> $nomeRemetente</p>
        <p><strong>Email:</strong> $emailRemetente</p>
        <p><strong>Assunto:</strong> $assunto</p>
        <p><strong>Mensagem:</strong></p>
        <p>" . nl2br(htmlspecialchars($mensagem)) . "</p>
        ";

        return $this->send($emailAdmin, "Contato: $assunto", $body);
    }

    /**
     * Enviar email de interesse (formulário homepage)
     */
    public function sendInteresse(string $nome, string $email, string $curso, string $whatsapp = ''): bool
    {
        $emailAdmin = $this->config['from_address'];
        
        $body = "
        <h2>Novo interessado em curso</h2>
        <p><strong>Nome:</strong> $nome</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>WhatsApp:</strong> $whatsapp</p>
        <p><strong>Curso de interesse:</strong> $curso</p>
        ";

        return $this->send($emailAdmin, "Novo interessado: $nome", $body);
    }

    /**
     * Adicionar anexo
     */
    public function addAttachment(string $path, string $name = ''): void
    {
        try {
            $this->mailer->addAttachment($path, $name);
        } catch (Exception $e) {
            error_log("Erro ao adicionar anexo: " . $e->getMessage());
        }
    }

    /**
     * Adicionar CC
     */
    public function addCC(string $email, string $name = ''): void
    {
        try {
            $this->mailer->addCC($email, $name);
        } catch (Exception $e) {
            error_log("Erro ao adicionar CC: " . $e->getMessage());
        }
    }

    /**
     * Adicionar BCC
     */
    public function addBCC(string $email, string $name = ''): void
    {
        try {
            $this->mailer->addBCC($email, $name);
        } catch (Exception $e) {
            error_log("Erro ao adicionar BCC: " . $e->getMessage());
        }
    }

    /**
     * Testar configuração de email
     */
    public function test(): array
    {
        try {
            // Testar conexão SMTP
            $this->mailer->SMTPDebug = 0;
            $result = $this->mailer->smtpConnect();
            
            if ($result) {
                $this->mailer->smtpClose();
                return [
                    'success' => true,
                    'message' => 'Conexão SMTP OK'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha na conexão SMTP'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Log de emails
     */
    private function logEmail(string $to, string $subject, string $status, string $error = ''): void
    {
        $logFile = __DIR__ . '/../../storage/logs/email.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$status] To: $to | Subject: $subject";
        
        if ($error) {
            $logMessage .= " | Error: $error";
        }
        
        $logMessage .= "\n";
        
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Enviar email em lote (queue)
     */
    public function sendBatch(array $recipients, string $subject, string $body): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($recipients as $recipient) {
            $email = is_array($recipient) ? $recipient['email'] : $recipient;
            $name = is_array($recipient) ? ($recipient['name'] ?? '') : '';

            if ($this->send($email, $subject, $body, $name)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $email;
            }
        }

        return $results;
    }
}
