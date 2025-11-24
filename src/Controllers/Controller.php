<?php

namespace PlanetaTreinamentos\Controllers;

use PDO;

/**
 * Controller Base
 * Todos os controllers herdam desta classe
 */
abstract class Controller
{
    protected PDO $db;
    protected array $data = [];

    public function __construct()
    {
        // Conexão com banco de dados
        $this->db = $this->getDatabase();
    }

    /**
     * Obter conexão com banco de dados
     */
    protected function getDatabase(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
        $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');

        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Renderizar view
     */
    protected function render(string $view, array $data = [], ?string $layout = 'app'): void
    {
        // Remover variáveis que podem causar conflito
        unset($data['this']);
        unset($data['db']);
        unset($data['_GET']);
        unset($data['_POST']);
        unset($data['_SERVER']);
        unset($data['_SESSION']);
        
        // Extrair dados para variáveis
        extract($data, EXTR_SKIP);
        
        // Iniciar buffer de saída
        ob_start();
        
        // Incluir view
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View não encontrada: $view");
        }
        
        require $viewPath;
        
        // Capturar conteúdo
        $content = ob_get_clean();
        
        // Renderizar com layout (se especificado)
        if ($layout) {
            $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';
            
            if (file_exists($layoutPath)) {
                require $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Renderizar JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirecionar
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Verificar autenticação
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Você precisa estar logado para acessar esta página';
            $this->redirect('/login');
        }
    }

    /**
     * Validar CSRF Token
     */
    protected function validateCSRF(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        
        if (empty($token) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Gerar CSRF Token
     */
    protected function generateCSRF(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanitizar input
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar email
     */
    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Obter usuário logado
     */
    protected function getUser(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Definir mensagem flash
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Obter e limpar mensagem flash
     */
    protected function getFlash(string $type): ?string
    {
        if (!isset($_SESSION['flash'][$type])) {
            return null;
        }

        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        
        return $message;
    }

    /**
     * Paginar resultados
     */
    protected function paginate(int $total, int $perPage = 20, int $currentPage = 1): array
    {
        $totalPages = ceil($total / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages
        ];
    }
}
