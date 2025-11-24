<?php
/**
 * Classe App
 * 
 * Inicializador principal da aplicação
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

use Exception;
use Throwable;
use PlanetaTreinamentos\Helpers\Logger;

class App
{
    /**
     * Instância do Router
     */
    private Router $router;
    
    /**
     * Configurações da aplicação
     */
    private array $config;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->loadEnvironment();
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->configure();
        $this->loadHelpers();
        $this->router = require __DIR__ . '/../../config/routes.php';
    }
    
    /**
     * Carrega variáveis de ambiente
     */
    private function loadEnvironment(): void
    {
        $envFile = __DIR__ . '/../../.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse linha (KEY=VALUE)
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove aspas do valor
                $value = trim($value, '"\'');
                
                // Define variável de ambiente
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    /**
     * Configura a aplicação
     */
    private function configure(): void
    {
        // Timezone
        date_default_timezone_set($this->config['timezone']);
        
        // Error reporting
        if ($this->config['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
        
        // Handler de erros
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);
        
        // Inicia sessão
        Session::start();
        
        // Compartilha dados com views
        $this->shareDataWithViews();
    }
    
    /**
     * Carrega funções auxiliares
     */
    private function loadHelpers(): void
    {
        require_once __DIR__ . '/../Helpers/helpers.php';
    }
    
    /**
     * Compartilha dados globais com todas as views
     */
    private function shareDataWithViews(): void
    {
        // Configurações da empresa (white-label)
        View::share('appName', $this->config['company']['name']);
        View::share('companyName', $this->config['company']['name']);
        View::share('companyEmail', $this->config['company']['email']);
        View::share('companyPhone', $this->config['company']['phone']);
        
        // Usuário autenticado
        View::share('isAuthenticated', Session::isAuthenticated());
        View::share('userName', Session::getUserName());
        View::share('userId', Session::getUserId());
        
        // URLs
        View::share('baseUrl', $this->config['url']);
    }
    
    /**
     * Executa a aplicação
     */
    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (Throwable $e) {
            $this->exceptionHandler($e);
        }
    }
    
    /**
     * Handler de erros PHP
     */
    public function errorHandler($errno, $errstr, $errfile, $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error = "[$errno] $errstr in $errfile on line $errline";
        
        Logger::error('PHP Error: ' . $error);
        
        if ($this->config['debug']) {
            echo "<div style='background: #fee; border: 2px solid #c00; padding: 10px; margin: 10px; border-radius: 5px;'>";
            echo "<strong>PHP Error:</strong> $error";
            echo "</div>";
        }
        
        return true;
    }
    
    /**
     * Handler de exceções
     */
    public function exceptionHandler(Throwable $e): void
    {
        Logger::error('Exception: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        http_response_code(500);
        
        if ($this->config['debug']) {
            echo '<div style="background: #f8d7da; border: 2px solid #842029; padding: 20px; margin: 10px; border-radius: 5px; font-family: monospace;">';
            echo '<h2 style="color: #842029; margin-top: 0;">Exception</h2>';
            echo '<p><strong>Message:</strong> ' . e($e->getMessage()) . '</p>';
            echo '<p><strong>File:</strong> ' . e($e->getFile()) . '</p>';
            echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
            echo '<pre style="background: #fff; padding: 10px; overflow: auto;">' . e($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        } else {
            if (View::exists('errors/500')) {
                View::make('errors/500', [], null);
            } else {
                echo '<h1>500 - Erro interno do servidor</h1>';
                echo '<p>Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.</p>';
            }
        }
    }
    
    /**
     * Handler de shutdown (pega erros fatais)
     */
    public function shutdownHandler(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            Logger::critical('Fatal Error', $error);
            
            if (!$this->config['debug']) {
                http_response_code(500);
                if (View::exists('errors/500')) {
                    View::make('errors/500', [], null);
                } else {
                    echo '<h1>500 - Erro interno do servidor</h1>';
                }
            }
        }
    }
}
