<?php

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Models\Aluno;
use PlanetaTreinamentos\Models\Professor;
use PlanetaTreinamentos\Helpers\EmailHelper;

/**
 * PreCadastroController
 * Gerencia sistema de pré-cadastro de alunos e professores
 */
class PreCadastroController extends Controller
{
    private Aluno $alunoModel;
    private Professor $professorModel;
    private EmailHelper $emailHelper;

    public function __construct()
    {
        parent::__construct();
        $this->alunoModel = new Aluno($this->db);
        $this->professorModel = new Professor($this->db);
        $this->emailHelper = new EmailHelper();
    }

    /**
     * Listar pré-cadastros de alunos
     */
    public function indexAlunos(): void
    {
        $this->requireAuth();

        // Buscar pré-cadastros pendentes
        $stmt = $this->db->query("
            SELECT a.*, c.nome as curso_nome
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.senha IS NULL OR a.senha = ''
            ORDER BY a.created_at DESC
        ");
        $preCadastros = $stmt->fetchAll();

        $this->render('admin/pre-cadastro/alunos', [
            'preCadastros' => $preCadastros,
            'title' => 'Pré-Cadastro de Alunos'
        ]);
    }

    /**
     * Criar pré-cadastros em lote (CSV/Excel)
     */
    public function createBatchAlunos(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/pre-cadastro/alunos');
            return;
        }

        // Validar CSRF
        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Token de segurança inválido';
            $this->redirect('/admin/pre-cadastro/alunos');
            return;
        }

        // Processar arquivo CSV
        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erro ao fazer upload do arquivo';
            $this->redirect('/admin/pre-cadastro/alunos');
            return;
        }

        $file = $_FILES['arquivo']['tmp_name'];
        $handle = fopen($file, 'r');
        
        if (!$handle) {
            $_SESSION['error'] = 'Não foi possível ler o arquivo';
            $this->redirect('/admin/pre-cadastro/alunos');
            return;
        }

        $created = 0;
        $errors = [];
        $firstLine = true;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Pular cabeçalho
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            // Formato esperado: nome, email, whatsapp, curso_id, data_inicio, data_fim, carga_horaria_id
            if (count($data) < 7) {
                $errors[] = "Linha inválida: " . implode(',', $data);
                continue;
            }

            try {
                // Gerar token único
                $token = bin2hex(random_bytes(32));
                
                // Inserir aluno
                $stmt = $this->db->prepare("
                    INSERT INTO alunos (
                        nome, email, whatsapp, curso_id, 
                        data_inicio, data_fim, carga_horaria_id,
                        token_pre_cadastro, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pre-cadastro', NOW())
                ");

                $stmt->execute([
                    trim($data[0]), // nome
                    trim($data[1]), // email
                    trim($data[2]), // whatsapp
                    intval($data[3]), // curso_id
                    $data[4], // data_inicio
                    $data[5], // data_fim
                    intval($data[6]), // carga_horaria_id
                    $token
                ]);

                // Enviar email com link de pré-cadastro
                $linkPreCadastro = ($_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br') . '/cadastro/aluno/' . $token;
                
                $this->emailHelper->sendPreCadastro(
                    trim($data[1]),
                    trim($data[0]),
                    $linkPreCadastro,
                    'aluno'
                );

                $created++;

            } catch (\Exception $e) {
                $errors[] = "Erro ao criar " . trim($data[0]) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        if ($created > 0) {
            $_SESSION['success'] = "$created pré-cadastros criados com sucesso!";
        }

        if (!empty($errors)) {
            $_SESSION['warning'] = "Alguns erros ocorreram: " . implode(', ', $errors);
        }

        $this->redirect('/admin/pre-cadastro/alunos');
    }

    /**
     * Mostrar formulário de pré-cadastro do aluno
     */
    public function showAluno(string $token): void
    {
        // Buscar aluno pelo token
        $stmt = $this->db->prepare("
            SELECT a.*, c.nome as curso_nome
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.token_pre_cadastro = ? AND (a.senha IS NULL OR a.senha = '')
        ");
        $stmt->execute([$token]);
        $aluno = $stmt->fetch();

        if (!$aluno) {
            $this->render('public/erro', [
                'mensagem' => 'Link de pré-cadastro inválido ou expirado'
            ], null);
            return;
        }

        $this->render('public/pre-cadastro-aluno', [
            'aluno' => $aluno,
            'token' => $token
        ], null);
    }

    /**
     * Completar pré-cadastro do aluno
     */
    public function completeAluno(string $token): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cadastro/aluno/' . $token);
            return;
        }

        // Buscar aluno
        $stmt = $this->db->prepare("SELECT * FROM alunos WHERE token_pre_cadastro = ?");
        $stmt->execute([$token]);
        $aluno = $stmt->fetch();

        if (!$aluno) {
            $_SESSION['error'] = 'Link inválido';
            $this->redirect('/');
            return;
        }

        // Validar dados
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $rg = $_POST['rg'] ?? '';
        $dataNascimento = $_POST['data_nascimento'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $cep = $_POST['cep'] ?? '';

        // Validações
        $errors = [];

        if (strlen($senha) < 6) {
            $errors[] = 'Senha deve ter no mínimo 6 caracteres';
        }

        if ($senha !== $confirmarSenha) {
            $errors[] = 'Senhas não conferem';
        }

        if (empty($cpf)) {
            $errors[] = 'CPF é obrigatório';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            $this->redirect('/cadastro/aluno/' . $token);
            return;
        }

        try {
            // Gerar aluno_id único
            $alunoId = $this->generateUniqueAlunoId();

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Atualizar aluno
            $stmt = $this->db->prepare("
                UPDATE alunos SET
                    alunoid = ?,
                    senha = ?,
                    cpf = ?,
                    rg = ?,
                    data_nascimento = ?,
                    endereco = ?,
                    cidade = ?,
                    estado = ?,
                    cep = ?,
                    status = 'ativo',
                    token_pre_cadastro = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $alunoId,
                $senhaHash,
                $cpf,
                $rg,
                $dataNascimento,
                $endereco,
                $cidade,
                $estado,
                $cep,
                $aluno['id']
            ]);

            // Enviar email de boas-vindas
            $this->emailHelper->sendWelcome(
                $aluno['email'],
                $aluno['nome'],
                $senha
            );

            $_SESSION['success'] = 'Cadastro completado com sucesso! Você já pode fazer login.';
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log("Erro ao completar pré-cadastro: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao completar cadastro. Tente novamente.';
            $this->redirect('/cadastro/aluno/' . $token);
        }
    }

    /**
     * Gerar aluno_id único
     */
    private function generateUniqueAlunoId(): string
    {
        do {
            $alunoId = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM alunos WHERE alunoid = ?");
            $stmt->execute([$alunoId]);
            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        return $alunoId;
    }

    /**
     * Reenviar email de pré-cadastro
     */
    public function resendAluno(int $id): void
    {
        $this->requireAuth();

        $stmt = $this->db->prepare("SELECT * FROM alunos WHERE id = ?");
        $stmt->execute([$id]);
        $aluno = $stmt->fetch();

        if (!$aluno || !$aluno['token_pre_cadastro']) {
            $_SESSION['error'] = 'Aluno não encontrado ou já completou o cadastro';
            $this->redirect('/admin/pre-cadastro/alunos');
            return;
        }

        $linkPreCadastro = ($_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br') . '/cadastro/aluno/' . $aluno['token_pre_cadastro'];

        if ($this->emailHelper->sendPreCadastro($aluno['email'], $aluno['nome'], $linkPreCadastro, 'aluno')) {
            $_SESSION['success'] = 'Email reenviado com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao reenviar email';
        }

        $this->redirect('/admin/pre-cadastro/alunos');
    }

    /**
     * Cancelar pré-cadastro
     */
    public function cancelAluno(int $id): void
    {
        $this->requireAuth();

        try {
            $stmt = $this->db->prepare("DELETE FROM alunos WHERE id = ? AND (senha IS NULL OR senha = '')");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Pré-cadastro cancelado';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cancelar pré-cadastro';
        }

        $this->redirect('/admin/pre-cadastro/alunos');
    }

    /**
     * Listar pré-cadastros de professores
     */
    public function indexProfessores(): void
    {
        $this->requireAuth();

        $stmt = $this->db->query("
            SELECT * FROM professores
            WHERE senha IS NULL OR senha = ''
            ORDER BY created_at DESC
        ");
        $preCadastros = $stmt->fetchAll();

        $this->render('admin/pre-cadastro/professores', [
            'preCadastros' => $preCadastros,
            'title' => 'Pré-Cadastro de Professores'
        ]);
    }

    /**
     * Criar pré-cadastro de professor
     */
    public function createProfessor(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/pre-cadastro/professores');
            return;
        }

        if (!$this->validateCSRF()) {
            $_SESSION['error'] = 'Token de segurança inválido';
            $this->redirect('/admin/pre-cadastro/professores');
            return;
        }

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($nome) || empty($email)) {
            $_SESSION['error'] = 'Nome e email são obrigatórios';
            $this->redirect('/admin/pre-cadastro/professores');
            return;
        }

        try {
            // Gerar token
            $token = bin2hex(random_bytes(32));

            // Inserir professor
            $stmt = $this->db->prepare("
                INSERT INTO professores (nome, email, token_pre_cadastro, status, created_at)
                VALUES (?, ?, ?, 'pre-cadastro', NOW())
            ");
            $stmt->execute([$nome, $email, $token]);

            // Enviar email
            $linkPreCadastro = ($_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br') . '/cadastro/professor/' . $token;
            
            $this->emailHelper->sendPreCadastro($email, $nome, $linkPreCadastro, 'professor');

            $_SESSION['success'] = 'Pré-cadastro criado e email enviado!';

        } catch (\Exception $e) {
            error_log("Erro ao criar pré-cadastro professor: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao criar pré-cadastro';
        }

        $this->redirect('/admin/pre-cadastro/professores');
    }

    /**
     * Mostrar formulário de pré-cadastro do professor
     */
    public function showProfessor(string $token): void
    {
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE token_pre_cadastro = ? AND (senha IS NULL OR senha = '')");
        $stmt->execute([$token]);
        $professor = $stmt->fetch();

        if (!$professor) {
            $this->render('public/erro', [
                'mensagem' => 'Link de pré-cadastro inválido ou expirado'
            ], null);
            return;
        }

        $this->render('public/pre-cadastro-professor', [
            'professor' => $professor,
            'token' => $token
        ], null);
    }

    /**
     * Completar pré-cadastro do professor
     */
    public function completeProfessor(string $token): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cadastro/professor/' . $token);
            return;
        }

        // Buscar professor
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE token_pre_cadastro = ?");
        $stmt->execute([$token]);
        $professor = $stmt->fetch();

        if (!$professor) {
            $_SESSION['error'] = 'Link inválido';
            $this->redirect('/');
            return;
        }

        // Validar e processar dados
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (strlen($senha) < 6 || $senha !== $confirmarSenha) {
            $_SESSION['error'] = 'Senha inválida ou não confere';
            $this->redirect('/cadastro/professor/' . $token);
            return;
        }

        try {
            // Gerar professor_id único
            $professorId = $this->generateUniqueProfessorId();

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Atualizar professor
            $stmt = $this->db->prepare("
                UPDATE professores SET
                    professorid = ?,
                    senha = ?,
                    status = 'ativo',
                    token_pre_cadastro = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$professorId, $senhaHash, $professor['id']]);

            $_SESSION['success'] = 'Cadastro completado! Você já pode fazer login.';
            $this->redirect('/login');

        } catch (\Exception $e) {
            error_log("Erro ao completar pré-cadastro professor: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao completar cadastro';
            $this->redirect('/cadastro/professor/' . $token);
        }
    }

    /**
     * Gerar professor_id único
     */
    private function generateUniqueProfessorId(): string
    {
        do {
            $professorId = 'PROF' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM professores WHERE professorid = ?");
            $stmt->execute([$professorId]);
            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        return $professorId;
    }

    /**
     * Reenviar email de pré-cadastro professor
     */
    public function resendProfessor(int $id): void
    {
        $this->requireAuth();

        $stmt = $this->db->prepare("SELECT * FROM professores WHERE id = ?");
        $stmt->execute([$id]);
        $professor = $stmt->fetch();

        if (!$professor || !$professor['token_pre_cadastro']) {
            $_SESSION['error'] = 'Professor não encontrado';
            $this->redirect('/admin/pre-cadastro/professores');
            return;
        }

        $linkPreCadastro = ($_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br') . '/cadastro/professor/' . $professor['token_pre_cadastro'];

        if ($this->emailHelper->sendPreCadastro($professor['email'], $professor['nome'], $linkPreCadastro, 'professor')) {
            $_SESSION['success'] = 'Email reenviado!';
        } else {
            $_SESSION['error'] = 'Erro ao reenviar email';
        }

        $this->redirect('/admin/pre-cadastro/professores');
    }

    /**
     * Cancelar pré-cadastro professor
     */
    public function cancelProfessor(int $id): void
    {
        $this->requireAuth();

        try {
            $stmt = $this->db->prepare("DELETE FROM professores WHERE id = ? AND (senha IS NULL OR senha = '')");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Pré-cadastro cancelado';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cancelar';
        }

        $this->redirect('/admin/pre-cadastro/professores');
    }
}
