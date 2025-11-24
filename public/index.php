
<?php
/**
 * Planeta Treinamentos - Entry Point
 * Versão ROBUSTA e FUNCIONAL
 */
 
 // DEBUG TEMPORÁRIO
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Definir caminhos
define('PUBLIC_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Criar diretório de logs se não existir
$logDir = ROOT_PATH . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$errorLogFile = $logDir . '/php_errors.log';
ini_set('error_log', $errorLogFile);

// Função auxiliar de log
function writeLog($message, $level = 'INFO') {
    $logFile = ROOT_PATH . '/storage/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
// Tentar executar aplicação
try {
    // 1. Verificar vendor/autoload
    $autoloadPath = ROOT_PATH . '/vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Composer autoload não encontrado. Execute: composer install');
    }
    
    require_once $autoloadPath;
    writeLog('Autoload carregado com sucesso');
    
    // 2. Carregar .env
    $envPath = ROOT_PATH . '/.env';
    if (file_exists($envPath)) {
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
            $dotenv->load();
            writeLog('Arquivo .env carregado com sucesso');
        } else {
            writeLog('Classe Dotenv não encontrada', 'WARNING');
        }
    } else {
        writeLog('Arquivo .env não encontrado', 'WARNING');
    }
    
    // 3. Gerar CSRF token se não existir
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // 4. Verificar se classe App existe
    if (!class_exists('PlanetaTreinamentos\Core\App')) {
        throw new Exception('Classe App não encontrada. Verifique se o namespace está correto.');
    }
    
    writeLog('Iniciando aplicação');
    
    // 5. Instanciar e executar aplicação
    $app = new PlanetaTreinamentos\Core\App();
    $app->run();
    
} catch (Throwable $e) {
    // Registrar erro no log
    $errorMessage = sprintf(
        'ERRO FATAL: %s em %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    
    writeLog($errorMessage, 'ERROR');
    writeLog('Stack trace: ' . $e->getTraceAsString(), 'ERROR');
    
    // Determinar se deve mostrar detalhes do erro
    $showDetails = false;
    if (isset($_ENV['APP_DEBUG'])) {
        $showDetails = ($_ENV['APP_DEBUG'] === 'true' || $_ENV['APP_DEBUG'] === true);
    } elseif (isset($_SERVER['SERVER_NAME'])) {
        $showDetails = (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false);
    }
    
    // Definir código HTTP
    http_response_code(500);
    
    // Página de erro
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erro no Servidor - Planeta Treinamentos</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .error-container {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                max-width: 600px;
                width: 100%;
                padding: 40px;
                text-align: center;
            }
            
            .error-icon {
                font-size: 64px;
                margin-bottom: 20px;
            }
            
            h1 {
                color: #e53e3e;
                font-size: 28px;
                margin-bottom: 15px;
            }
            
            p {
                color: #4a5568;
                line-height: 1.6;
                margin-bottom: 25px;
            }
            
            .btn {
                display: inline-block;
                background: #667eea;
                color: white;
                padding: 12px 30px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
            }
            
            .btn:hover {
                background: #5568d3;
                transform: translateY(-2px);
            }
            
            .error-details {
                background: #f7fafc;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                padding: 20px;
                margin-top: 30px;
                text-align: left;
            }
            
            .error-details h3 {
                color: #2d3748;
                margin-bottom: 10px;
                font-size: 16px;
            }
            
            .error-details pre {
                background: #2d3748;
                color: #f7fafc;
                padding: 15px;
                border-radius: 4px;
                overflow-x: auto;
                font-size: 13px;
                line-height: 1.5;
            }
            
            .error-file {
                color: #e53e3e;
                font-weight: 600;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1>Erro no Servidor</h1>
            <p>Desculpe, ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.</p>
            <a href="/" class="btn">Voltar para a Página Inicial</a>
            
            <?php if ($showDetails): ?>
            <div class="error-details">
                <h3>🔍 Detalhes do Erro (Modo Debug)</h3>
                <p class="error-file">
                    <strong>Arquivo:</strong> <?= htmlspecialchars($e->getFile()) ?><br>
                    <strong>Linha:</strong> <?= $e->getLine() ?>
                </p>
                <p><strong>Mensagem:</strong></p>
                <pre><?= htmlspecialchars($e->getMessage()) ?></pre>
                <p><strong>Stack Trace:</strong></p>
                <pre><?= htmlspecialchars($e->getTraceAsString()) ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}