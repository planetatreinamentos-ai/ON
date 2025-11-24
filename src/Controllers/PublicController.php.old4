<?php
/**
 * Controller Público - VERSÃO DEFINITIVA CORRIGIDA
 * 
 * ✅ Modelos instanciados CORRETAMENTE com Database
 * ✅ Carrega configurações white-label
 * ✅ Página de contato
 * ✅ Compatibilidade QR codes
 * 
 * @package PlanetaTreinamentos\Controllers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Core\Database;
use PlanetaTreinamentos\Models\Config;
use PlanetaTreinamentos\Helpers\Validator;
use PlanetaTreinamentos\Helpers\Logger;

class PublicController
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Carrega configurações white-label do banco
     */
    private function loadConfig(): array
    {
        try {
            // Busca todas as configurações do banco
            $sql = "SELECT chave, valor FROM configuracoes WHERE status = 1";
            $configs = $this->db->query($sql)->fetchAll();
            
            // Converte array de configs para array associativo
            $configArray = [];
            foreach ($configs as $config) {
                $configArray[$config['chave']] = $config['valor'];
            }
            
            // Valores padrão se não existir no banco
            return [
                'nome_empresa' => $configArray['nome_empresa'] ?? 'Planeta Treinamentos',
                'email' => $configArray['email'] ?? 'contato@planetatreinamentos.com.br',
                'whatsapp' => $configArray['whatsapp'] ?? '',
                'telefone' => $configArray['telefone'] ?? '',
                'endereco' => $configArray['endereco'] ?? '',
                'cep' => $configArray['cep'] ?? '',
                'cidade' => $configArray['cidade'] ?? '',
                'estado' => $configArray['estado'] ?? '',
                'descricao' => $configArray['descricao'] ?? 'Cursos profissionalizantes de qualidade',
                'facebook' => $configArray['facebook'] ?? '',
                'instagram' => $configArray['instagram'] ?? '',
                'linkedin' => $configArray['linkedin'] ?? '',
                'youtube' => $configArray['youtube'] ?? '',
                'logo' => $configArray['logo'] ?? '/assets/img/logo.png',
                'favicon' => $configArray['favicon'] ?? '/assets/img/favicon.ico',
                'cor_primaria' => $configArray['cor_primaria'] ?? '#667eea',
                'cor_secundaria' => $configArray['cor_secundaria'] ?? '#764ba2',
            ];
        } catch (\Exception $e) {
            Logger::error('Erro ao carregar configurações: ' . $e->getMessage());
            
            // Retorna valores padrão em caso de erro
            return [
                'nome_empresa' => 'Planeta Treinamentos',
                'email' => 'contato@planetatreinamentos.com.br',
                'whatsapp' => '',
                'telefone' => '',
                'endereco' => '',
                'cep' => '',
                'cidade' => '',
                'estado' => '',
                'descricao' => 'Cursos profissionalizantes de qualidade',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'youtube' => '',
                'logo' => '/assets/img/logo.png',
                'favicon' => '/assets/img/favicon.ico',
                'cor_primaria' => '#667eea',
                'cor_secundaria' => '#764ba2',
            ];
        }
    }
    
    /**
     * Homepage pública
     */
    public function home(): void
    {
        // Carrega configurações white-label
        $config = $this->loadConfig();
        
        // Busca cursos ativos (usando query direta)
        $sqlCursos = "SELECT * FROM cursos WHERE status = 1 ORDER BY ordem_exibicao, nome";
        $cursos = $this->db->query($sqlCursos)->fetchAll();
        
        // Busca professores ativos
        $sqlProfessores = "SELECT * FROM professores WHERE status = 1 ORDER BY nome";
        $professores = $this->db->query($sqlProfessores)->fetchAll();
        
        // Busca alunos em destaque
        $sqlAlunos = "SELECT a.*, c.nome as curso_nome 
                      FROM alunos a 
                      LEFT JOIN cursos c ON a.curso_id = c.id 
                      WHERE a.status = 1 
                      ORDER BY a.melhor_aluno DESC, RAND() 
                      LIMIT 12";
        
        $alunosDestaque = $this->db->query($sqlAlunos)->fetchAll();
        
        // Estatísticas
        $estatisticas = [
            'total_alunos' => $this->db->fetchColumn("SELECT COUNT(*) FROM alunos WHERE status = 1"),
            'total_turmas' => $this->db->fetchColumn("SELECT COUNT(DISTINCT curso_id) FROM alunos WHERE status = 1"),
            'anos_experiencia' => date('Y') - 2018
        ];
        
        View::make('public/home', [
            'config' => $config,
            'cursos' => $cursos,
            'professores' => $professores,
            'alunosDestaque' => $alunosDestaque,
            'estatisticas' => $estatisticas
        ], null);
    }
    
    /**
     * Página sobre
     */
    public function sobre(): void
    {
        $config = $this->loadConfig();
        
        View::make('public/sobre', [
            'config' => $config,
            'pageTitle' => 'Sobre ' . $config['nome_empresa']
        ], null);
    }
    
    /**
     * Página de contato
     */
    public function contato(): void
    {
        $config = $this->loadConfig();
        
        View::make('public/contato', [
            'config' => $config,
            'pageTitle' => 'Contato'
        ], null);
    }
    
    /**
     * Envia formulário de contato
     */
    public function enviarContato(): void
    {
        CSRF::check();
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido')
            ->required('mensagem', 'Mensagem é obrigatória');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        try {
            $data = [
                'nome' => Validator::sanitizeString($_POST['nome']),
                'email' => Validator::sanitizeEmail($_POST['email']),
                'telefone' => Validator::sanitizeString($_POST['telefone'] ?? ''),
                'assunto' => Validator::sanitizeString($_POST['assunto'] ?? 'Contato do site'),
                'mensagem' => Validator::sanitizeString($_POST['mensagem'])
            ];
            
            // Salva no banco
            $sql = "INSERT INTO contatos (nome, email, telefone, assunto, mensagem, created_at) 
                    VALUES (:nome, :email, :telefone, :assunto, :mensagem, NOW())";
            
            $this->db->query($sql, $data);
            
            Logger::info('Contato recebido', $data);
            
            Session::success('Mensagem enviada com sucesso! Retornaremos em breve.');
            redirect('/contato');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao enviar contato: ' . $e->getMessage());
            Session::error('Erro ao enviar mensagem. Tente novamente.');
            back();
        }
    }
    
    /**
     * Cria registro de interessado
     */
    public function criarInteressado(): void
    {
        CSRF::check();
        
        // Honeypot check
        if (!empty($_POST['website'])) {
            redirect('/');
        }
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        try {
            $data = [
                'nome' => Validator::sanitizeString($_POST['nome']),
                'email' => Validator::sanitizeEmail($_POST['email']),
                'whatsapp' => Validator::sanitizeString($_POST['whatsapp'] ?? ''),
                'curso_interesse' => isset($_POST['curso_interesse']) && !empty($_POST['curso_interesse']) ? (int)$_POST['curso_interesse'] : null,
                'mensagem' => Validator::sanitizeString($_POST['mensagem'] ?? '')
            ];
            
            $sql = "INSERT INTO interessados (nome, email, whatsapp, curso_id, mensagem, created_at) 
                    VALUES (:nome, :email, :whatsapp, :curso_interesse, :mensagem, NOW())";
            
            $this->db->query($sql, $data);
            
            Logger::info('Interessado cadastrado', $data);
            
            Session::success('Obrigado pelo interesse! Entraremos em contato em breve.');
            redirect('/#interesse');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao cadastrar interessado: ' . $e->getMessage());
            Session::error('Erro ao enviar formulário. Tente novamente.');
            back();
        }
    }
    
    /**
     * Verificação de certificado
     */
    public function verificarCertificado(string $alunoid): void
    {
        $config = $this->loadConfig();
        
        // Busca aluno pelo alunoid
        $sql = "SELECT a.*, c.nome as curso_nome, p.nome as professor_nome, ch.horas
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
                WHERE a.alunoid = :alunoid AND a.status = 1";
        
        $aluno = $this->db->query($sql, ['alunoid' => $alunoid])->fetch();
        
        if (!$aluno) {
            Session::error('Certificado não encontrado.');
            redirect('/');
        }
        
        View::make('public/verificar', [
            'config' => $config,
            'aluno' => $aluno,
            'pageTitle' => 'Certificado - ' . $aluno['nome']
        ], null);
    }
    
    /**
     * Compatibilidade com QR codes antigos (aluno.php?id=xxx)
     */
    public function alunoLegacy(): void
    {
        if (isset($_GET['id'])) {
            $alunoid = Validator::sanitizeString($_GET['id']);
            redirect('/verificar/' . $alunoid);
        } else {
            redirect('/');
        }
    }
}