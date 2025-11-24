<?php
/**
 * Backend de Migração de Dados - Planeta Treinamentos
 * Arquivo: migrate_backend.php
 */

// Headers CORS e Content-Type
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratar requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuração de erro - ATIVAR para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/migration_errors.log');

// Aumentar timeout
set_time_limit(300);

class DatabaseConnection {
    private $conn;
    private $error;
    
    public function __construct($config) {
        try {
            // Validar configuração
            if (empty($config['host']) || empty($config['database']) || empty($config['username'])) {
                throw new Exception("Configuração incompleta");
            }
            
            // Montar DSN
            $port = !empty($config['port']) ? $config['port'] : '3306';
            $dsn = "mysql:host={$config['host']};port={$port};dbname={$config['database']};charset=utf8mb4";
            
            // Opções de conexão
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            // Conectar
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
            
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            throw new Exception("Erro na conexão: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function testConnection() {
        try {
            if (!$this->conn) {
                return [
                    'success' => false,
                    'error' => $this->error ?? 'Conexão não estabelecida'
                ];
            }
            
            $stmt = $this->conn->query("SELECT 1 as test");
            $result = $stmt->fetch();
            
            if ($result && $result['test'] == 1) {
                return [
                    'success' => true,
                    'error' => null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Falha ao executar query de teste'
            ];
            
        } catch(PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function getError() {
        return $this->error;
    }
}

class MigrationManager {
    private $oldDb;
    private $newDb;
    private $errors = [];
    
    public function __construct($oldDbConfig, $newDbConfig) {
        try {
            $this->oldDb = new DatabaseConnection($oldDbConfig);
        } catch(Exception $e) {
            $this->errors['old_db'] = $e->getMessage();
        }
        
        try {
            $this->newDb = new DatabaseConnection($newDbConfig);
        } catch(Exception $e) {
            $this->errors['new_db'] = $e->getMessage();
        }
    }
    
    public function testConnections() {
        $result = [
            'old_db' => [
                'success' => false,
                'error' => 'Não inicializado'
            ],
            'new_db' => [
                'success' => false,
                'error' => 'Não inicializado'
            ]
        ];
        
        // Testar banco antigo
        if (isset($this->errors['old_db'])) {
            $result['old_db']['error'] = $this->errors['old_db'];
        } else if ($this->oldDb) {
            $result['old_db'] = $this->oldDb->testConnection();
        }
        
        // Testar banco novo
        if (isset($this->errors['new_db'])) {
            $result['new_db']['error'] = $this->errors['new_db'];
        } else if ($this->newDb) {
            $result['new_db'] = $this->newDb->testConnection();
        }
        
        return $result;
    }
    
    public function getStats() {
        $oldConn = $this->oldDb->getConnection();
        
        $stats = [];
        
        // Professores (ministrante)
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM ministrante");
        $stats['professores'] = $stmt->fetch()['total'];
        
        // Cursos
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM curso");
        $stats['cursos'] = $stmt->fetch()['total'];
        
        // Cargas horárias
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM carga");
        $stats['cargas'] = $stmt->fetch()['total'];
        
        // Alunos
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM aluno");
        $stats['alunos'] = $stmt->fetch()['total'];
        
        // Histórico
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM aluno_historico");
        $stats['historico'] = $stmt->fetch()['total'];
        
        // Pré-cadastros
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM precertificado");
        $stats['precadastros'] = $stmt->fetch()['total'];
        
        return $stats;
    }
    
    public function getTableTotal($table) {
        $oldConn = $this->oldDb->getConnection();
        $tableMap = [
            'professores' => 'ministrante',
            'cursos' => 'curso',
            'cargas' => 'carga',
            'alunos' => 'aluno',
            'historico' => 'aluno_historico',
            'precadastros' => 'precertificado'
        ];
        
        $oldTable = $tableMap[$table];
        $stmt = $oldConn->query("SELECT COUNT(*) as total FROM {$oldTable}");
        return $stmt->fetch()['total'];
    }
    
    public function migrateRecord($table, $index) {
        $oldConn = $this->oldDb->getConnection();
        $newConn = $this->newDb->getConnection();
        
        switch($table) {
            case 'professores':
                return $this->migrateProfessor($oldConn, $newConn, $index);
            case 'cursos':
                return $this->migrateCurso($oldConn, $newConn, $index);
            case 'cargas':
                return $this->migrateCarga($oldConn, $newConn, $index);
            case 'alunos':
                return $this->migrateAluno($oldConn, $newConn, $index);
            case 'historico':
                return $this->migrateHistorico($oldConn, $newConn, $index);
            case 'precadastros':
                return $this->migratePrecadastro($oldConn, $newConn, $index);
            default:
                throw new Exception("Tabela desconhecida: {$table}");
        }
    }
    
    private function migrateProfessor($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM ministrante LIMIT {$index}, 1");
        $professor = $stmt->fetch();
        
        if (!$professor) {
            throw new Exception("Professor não encontrado no índice {$index}");
        }
        
        $professorid = 'prof_' . strtolower(str_replace(' ', '_', $professor['nome']));
        $email = strtolower(str_replace(' ', '.', $professor['nome'])) . '@planetatreinamentos.com.br';
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM professores WHERE id = :id");
        $checkStmt->execute(['id' => $professor['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE professores SET 
                    nome = :nome,
                    email = :email,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $professor['id'],
                'nome' => $professor['nome'],
                'email' => $email
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO professores (id, professorid, nome, email, status, ordem_exibicao, created_at, updated_at) 
                    VALUES (:id, :professorid, :nome, :email, 1, 0, NOW(), NOW())";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $professor['id'],
                'professorid' => $professorid,
                'nome' => $professor['nome'],
                'email' => $email
            ]);
        }
        
        return "Professor '{$professor['nome']}' migrado com sucesso";
    }
    
    private function migrateCurso($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM curso LIMIT {$index}, 1");
        $curso = $stmt->fetch();
        
        if (!$curso) {
            throw new Exception("Curso não encontrado no índice {$index}");
        }
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM cursos WHERE id = :id");
        $checkStmt->execute(['id' => $curso['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE cursos SET 
                    nome = :nome, 
                    descricao = :descricao, 
                    frase_certificado = :frase,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $curso['id'],
                'nome' => $curso['curso'],
                'descricao' => $curso['pseudo'],
                'frase' => $curso['texto']
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO cursos (id, nome, descricao, frase_certificado, ordem_exibicao, status, created_at, updated_at) 
                    VALUES (:id, :nome, :descricao, :frase, 0, 1, NOW(), NOW())";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $curso['id'],
                'nome' => $curso['curso'],
                'descricao' => $curso['pseudo'],
                'frase' => $curso['texto']
            ]);
        }
        
        return "Curso '{$curso['curso']}' migrado com sucesso";
    }
    
    private function migrateCarga($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM carga LIMIT {$index}, 1");
        $carga = $stmt->fetch();
        
        if (!$carga) {
            throw new Exception("Carga horária não encontrada no índice {$index}");
        }
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM cargas_horarias WHERE id = :id");
        $checkStmt->execute(['id' => $carga['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE cargas_horarias SET 
                    horas = :horas,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $carga['id'],
                'horas' => $carga['carga']
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO cargas_horarias (id, horas, status, created_at, updated_at) 
                    VALUES (:id, :horas, 1, NOW(), NOW())";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $carga['id'],
                'horas' => $carga['carga']
            ]);
        }
        
        return "Carga horária de {$carga['carga']}h migrada com sucesso";
    }
    
    private function migrateAluno($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM aluno LIMIT {$index}, 1");
        $aluno = $stmt->fetch();
        
        if (!$aluno) {
            throw new Exception("Aluno não encontrado no índice {$index}");
        }
        
        // Buscar IDs das tabelas relacionadas
        $stmt = $newConn->prepare("SELECT id FROM cursos WHERE nome = :nome LIMIT 1");
        $stmt->execute(['nome' => $aluno['curso']]);
        $curso = $stmt->fetch();
        $curso_id = $curso ? $curso['id'] : 1;
        
        $stmt = $newConn->prepare("SELECT id FROM professores WHERE nome = :nome LIMIT 1");
        $stmt->execute(['nome' => $aluno['instrutor']]);
        $professor = $stmt->fetch();
        $professor_id = $professor ? $professor['id'] : 1;
        
        $stmt = $newConn->prepare("SELECT id FROM cargas_horarias WHERE horas = :horas LIMIT 1");
        $stmt->execute(['horas' => $aluno['carga']]);
        $carga = $stmt->fetch();
        $carga_id = $carga ? $carga['id'] : 1;
        
        $status = $aluno['ativo'] == 1 ? 1 : 2; // 1=ativo, 2=cancelado
        $deleted_at = $aluno['ativo'] == 0 ? date('Y-m-d H:i:s') : null;
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM alunos WHERE id = :id");
        $checkStmt->execute(['id' => $aluno['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE alunos SET 
                    nome = :nome,
                    email = :email,
                    whatsapp = :whatsapp,
                    curso_id = :curso_id,
                    professor_id = :professor_id,
                    carga_horaria_id = :carga_id,
                    data_inicio = :data_inicio,
                    data_fim = :data_fim,
                    nota = :nota,
                    melhor_aluno = :melhor_aluno,
                    foto_principal = :foto,
                    status = :status,
                    updated_at = :updated,
                    deleted_at = :deleted
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $aluno['id'],
                'nome' => $aluno['nome'],
                'email' => $aluno['email'],
                'whatsapp' => $aluno['zap'],
                'curso_id' => $curso_id,
                'professor_id' => $professor_id,
                'carga_id' => $carga_id,
                'data_inicio' => $aluno['inicio'],
                'data_fim' => $aluno['fim'],
                'nota' => $aluno['nota'],
                'melhor_aluno' => $aluno['vencedor'],
                'foto' => $aluno['foto'],
                'status' => $status,
                'updated' => $aluno['datahora'],
                'deleted' => $deleted_at
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO alunos (
                        id, alunoid, nome, email, whatsapp, curso_id, professor_id, 
                        carga_horaria_id, data_inicio, data_fim, nota, melhor_aluno, 
                        foto_principal, certificado_gerado, status, created_at, updated_at, deleted_at
                    ) VALUES (
                        :id, :alunoid, :nome, :email, :whatsapp, :curso_id, :professor_id,
                        :carga_id, :data_inicio, :data_fim, :nota, :melhor_aluno,
                        :foto, 1, :status, :created, :updated, :deleted
                    )";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $aluno['id'],
                'alunoid' => $aluno['alunoid'],
                'nome' => $aluno['nome'],
                'email' => $aluno['email'],
                'whatsapp' => $aluno['zap'],
                'curso_id' => $curso_id,
                'professor_id' => $professor_id,
                'carga_id' => $carga_id,
                'data_inicio' => $aluno['inicio'],
                'data_fim' => $aluno['fim'],
                'nota' => $aluno['nota'],
                'melhor_aluno' => $aluno['vencedor'],
                'foto' => $aluno['foto'],
                'status' => $status,
                'created' => $aluno['datahora'],
                'updated' => $aluno['datahora'],
                'deleted' => $deleted_at
            ]);
        }
        
        return "Aluno '{$aluno['nome']}' migrado com sucesso";
    }
    
    private function migrateHistorico($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM aluno_historico LIMIT {$index}, 1");
        $historico = $stmt->fetch();
        
        if (!$historico) {
            throw new Exception("Histórico não encontrado no índice {$index}");
        }
        
        // Criar/buscar usuário "Sistema Legado"
        $stmt = $newConn->query("SELECT id FROM usuarios WHERE email = 'sistema@legado.local' LIMIT 1");
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            $stmt = $newConn->prepare("INSERT INTO usuarios (nome, email, senha, status) VALUES ('Sistema Legado', 'sistema@legado.local', '', 0)");
            $stmt->execute();
            $usuario_id = $newConn->lastInsertId();
        } else {
            $usuario_id = $usuario['id'];
        }
        
        $acao_map = [
            'criacao' => 'create',
            'edicao' => 'update',
            'exclusao' => 'delete',
            'reativacao' => 'update',
            'regeneracao_certificado' => 'update'
        ];
        
        $acao = $acao_map[$historico['acao']] ?? 'update';
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM alunos_historico WHERE id = :id");
        $checkStmt->execute(['id' => $historico['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE alunos_historico SET
                    acao = :acao,
                    campos_alterados = :campos,
                    valores_anteriores = :anteriores,
                    valores_novos = :novos,
                    ip_origem = :ip
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $historico['id'],
                'acao' => $acao,
                'campos' => json_encode(['campo' => $historico['campo_alterado']]),
                'anteriores' => json_encode(['valor' => $historico['valor_anterior']]),
                'novos' => json_encode(['valor' => $historico['valor_novo']]),
                'ip' => $historico['ip_address']
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO alunos_historico (
                        id, aluno_id, usuario_id, acao, campos_alterados, 
                        valores_anteriores, valores_novos, confirmado_email, 
                        ip_origem, created_at
                    ) VALUES (
                        :id, :aluno_id, :usuario_id, :acao, :campos,
                        :anteriores, :novos, 1, :ip, :created
                    )";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $historico['id'],
                'aluno_id' => $historico['aluno_id'],
                'usuario_id' => $usuario_id,
                'acao' => $acao,
                'campos' => json_encode(['campo' => $historico['campo_alterado']]),
                'anteriores' => json_encode(['valor' => $historico['valor_anterior']]),
                'novos' => json_encode(['valor' => $historico['valor_novo']]),
                'ip' => $historico['ip_address'],
                'created' => $historico['data_alteracao']
            ]);
        }
        
        return "Histórico #{$historico['id']} migrado com sucesso";
    }
    
    private function migratePrecadastro($oldConn, $newConn, $index) {
        $stmt = $oldConn->query("SELECT * FROM precertificado LIMIT {$index}, 1");
        $pre = $stmt->fetch();
        
        if (!$pre) {
            throw new Exception("Pré-cadastro não encontrado no índice {$index}");
        }
        
        // Mapear IDs
        $stmt = $newConn->prepare("SELECT id FROM cursos WHERE descricao = :descricao LIMIT 1");
        $stmt->execute(['descricao' => $pre['curso']]);
        $curso = $stmt->fetch();
        $curso_id = $curso ? $curso['id'] : 1;
        
        $stmt = $newConn->prepare("SELECT id FROM professores WHERE nome = :nome LIMIT 1");
        $stmt->execute(['nome' => $pre['ministrante']]);
        $professor = $stmt->fetch();
        $professor_id = $professor ? $professor['id'] : 1;
        
        $stmt = $newConn->prepare("SELECT id FROM cargas_horarias WHERE horas = :horas LIMIT 1");
        $stmt->execute(['horas' => $pre['carga']]);
        $carga = $stmt->fetch();
        $carga_id = $carga ? $carga['id'] : 1;
        
        $status = $pre['validade'] == 1 ? 'pendente' : 'expirado';
        $expiracao = date('Y-m-d H:i:s', strtotime($pre['datahora'] . ' +30 days'));
        
        // Verificar se já existe
        $checkStmt = $newConn->prepare("SELECT id FROM pre_cadastros WHERE id = :id");
        $checkStmt->execute(['id' => $pre['id']]);
        $exists = $checkStmt->fetch();
        
        if ($exists) {
            // UPDATE
            $sql = "UPDATE pre_cadastros SET
                    curso_id = :curso_id,
                    professor_id = :professor_id,
                    carga_horaria_id = :carga_id,
                    data_inicio = :inicio,
                    data_fim = :fim,
                    status = :status,
                    data_expiracao = :expiracao,
                    updated_at = :updated
                    WHERE id = :id";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $pre['id'],
                'curso_id' => $curso_id,
                'professor_id' => $professor_id,
                'carga_id' => $carga_id,
                'inicio' => $pre['inicio'],
                'fim' => $pre['fim'],
                'status' => $status,
                'expiracao' => $expiracao,
                'updated' => $pre['datahora']
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO pre_cadastros (
                        id, token, curso_id, professor_id, carga_horaria_id,
                        data_inicio, data_fim, nota_padrao, status, 
                        data_expiracao, created_by, created_at, updated_at
                    ) VALUES (
                        :id, :token, :curso_id, :professor_id, :carga_id,
                        :inicio, :fim, 8.0, :status,
                        :expiracao, 1, :created, :updated
                    )";
            
            $stmt = $newConn->prepare($sql);
            $stmt->execute([
                'id' => $pre['id'],
                'token' => $pre['link'],
                'curso_id' => $curso_id,
                'professor_id' => $professor_id,
                'carga_id' => $carga_id,
                'inicio' => $pre['inicio'],
                'fim' => $pre['fim'],
                'status' => $status,
                'expiracao' => $expiracao,
                'created' => $pre['datahora'],
                'updated' => $pre['datahora']
            ]);
        }
        
        return "Pré-cadastro #{$pre['id']} migrado com sucesso";
    }
}

// Processar requisição
try {
    // Log da requisição
    error_log("=== Nova Requisição ===");
    error_log("Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido'));
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Log do input
    error_log("Input recebido: " . print_r($input, true));
    
    if (!$input || !isset($input['action'])) {
        throw new Exception('Ação não especificada ou JSON inválido');
    }
    
    $action = $input['action'];
    $config = $input['config'] ?? null;
    
    error_log("Action: " . $action);
    
    if (!$config && $action !== 'test') {
        throw new Exception('Configuração não fornecida');
    }
    
    // Criar manager
    $manager = new MigrationManager($config['old_db'], $config['new_db']);
    
    switch($action) {
        case 'test_connections':
            error_log("Testando conexões...");
            $result = $manager->testConnections();
            error_log("Resultado: " . print_r($result, true));
            echo json_encode($result);
            break;
            
        case 'get_stats':
            $stats = $manager->getStats();
            echo json_encode($stats);
            break;
            
        case 'migrate_table':
            $table = $input['table'];
            $total = $manager->getTableTotal($table);
            echo json_encode([
                'success' => true,
                'total' => $total
            ]);
            break;
            
        case 'migrate_record':
            $table = $input['table'];
            $index = $input['index'];
            $message = $manager->migrateRecord($table, $index);
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            break;
            
        default:
            throw new Exception('Ação desconhecida: ' . $action);
    }
    
} catch(Exception $e) {
    error_log("ERRO: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>