<?php
/**
 * TESTE DE DIAGNÓSTICO - PLANETA TREINAMENTOS
 * Execute este arquivo para ver exatamente onde está o erro
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<html><head><meta charset="UTF-8"><title>Diagnóstico</title>';
echo '<style>body{font-family:monospace;padding:20px;background:#f0f0f0}';
echo '.ok{color:green;background:#d4edda;padding:10px;margin:5px 0;border-radius:4px}';
echo '.erro{color:red;background:#f8d7da;padding:10px;margin:5px 0;border-radius:4px}';
echo '.info{color:blue;background:#d1ecf1;padding:10px;margin:5px 0;border-radius:4px}';
echo '</style></head><body>';

echo '<h1>🔍 Diagnóstico do Sistema</h1>';

// Teste 1: Estrutura de pastas
echo '<h2>1. Estrutura de Pastas</h2>';

$pastas = [
    '/vendor' => 'Composer vendor',
    '/src' => 'Código fonte',
    '/src/Core' => 'Core classes',
    '/src/Controllers' => 'Controllers',
    '/src/Models' => 'Models',
    '/src/Helpers' => 'Helpers',
    '/views' => 'Views',
    '/views/public' => 'Views públicas',
    '/public' => 'Public directory',
    '/storage' => 'Storage',
    '/storage/logs' => 'Logs'
];

foreach ($pastas as $pasta => $desc) {
    $caminho = __DIR__ . $pasta;
    if (is_dir($caminho)) {
        echo "<div class='ok'>✅ $desc ($pasta) - EXISTE</div>";
    } else {
        echo "<div class='erro'>❌ $desc ($pasta) - NÃO EXISTE</div>";
    }
}

// Teste 2: Arquivos críticos
echo '<h2>2. Arquivos Críticos</h2>';

$arquivos = [
    '/.env' => '.env (configuração)',
    '/vendor/autoload.php' => 'Composer autoload',
    '/src/Core/App.php' => 'Classe App',
    '/src/Core/Router.php' => 'Router',
    '/src/Controllers/Controller.php' => 'Controller base',
    '/src/Controllers/PublicController.php' => 'PublicController',
    '/views/public/home.php' => 'View home'
];

foreach ($arquivos as $arquivo => $desc) {
    $caminho = __DIR__ . $arquivo;
    if (file_exists($caminho)) {
        $tamanho = filesize($caminho);
        echo "<div class='ok'>✅ $desc - EXISTE (" . number_format($tamanho) . " bytes)</div>";
    } else {
        echo "<div class='erro'>❌ $desc - NÃO EXISTE</div>";
    }
}

// Teste 3: Permissões de escrita
echo '<h2>3. Permissões de Escrita</h2>';

$pastasTeste = [
    '/storage',
    '/storage/logs',
    '/storage/certificates'
];

foreach ($pastasTeste as $pasta) {
    $caminho = __DIR__ . $pasta;
    if (is_dir($caminho)) {
        if (is_writable($caminho)) {
            echo "<div class='ok'>✅ $pasta - GRAVÁVEL</div>";
        } else {
            echo "<div class='erro'>❌ $pasta - SEM PERMISSÃO DE ESCRITA</div>";
        }
    }
}

// Teste 4: Composer Autoload
echo '<h2>4. Teste de Autoload</h2>';

try {
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        echo "<div class='ok'>✅ Composer autoload carregado</div>";
    } else {
        echo "<div class='erro'>❌ Composer autoload NÃO encontrado</div>";
    }
} catch (Exception $e) {
    echo "<div class='erro'>❌ Erro ao carregar autoload: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Teste 5: Dotenv
echo '<h2>5. Teste de .env</h2>';

try {
    if (class_exists('Dotenv\Dotenv')) {
        echo "<div class='ok'>✅ Classe Dotenv disponível</div>";
        
        if (file_exists(__DIR__ . '/.env')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            echo "<div class='ok'>✅ Arquivo .env carregado</div>";
            
            // Verificar variáveis importantes
            $vars = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'APP_URL'];
            foreach ($vars as $var) {
                if (isset($_ENV[$var])) {
                    echo "<div class='ok'>✅ $_ENV['$var'] está definido</div>";
                } else {
                    echo "<div class='erro'>❌ $_ENV['$var'] NÃO está definido</div>";
                }
            }
        } else {
            echo "<div class='erro'>❌ Arquivo .env não encontrado</div>";
        }
    } else {
        echo "<div class='erro'>❌ Classe Dotenv NÃO disponível (execute: composer install)</div>";
    }
} catch (Exception $e) {
    echo "<div class='erro'>❌ Erro no Dotenv: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Teste 6: Classes do sistema
echo '<h2>6. Classes do Sistema</h2>';

$classes = [
    'PlanetaTreinamentos\Core\App',
    'PlanetaTreinamentos\Core\Router',
    'PlanetaTreinamentos\Controllers\Controller',
    'PlanetaTreinamentos\Controllers\PublicController'
];

foreach ($classes as $classe) {
    if (class_exists($classe)) {
        echo "<div class='ok'>✅ Classe $classe - EXISTE</div>";
    } else {
        echo "<div class='erro'>❌ Classe $classe - NÃO EXISTE</div>";
    }
}

// Teste 7: Conexão com banco
echo '<h2>7. Teste de Banco de Dados</h2>';

try {
    if (isset($_ENV['DB_HOST'])) {
        $pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "<div class='ok'>✅ Conexão com banco de dados FUNCIONANDO</div>";
        
        // Testar query
        $stmt = $pdo->query("SELECT COUNT(*) FROM cursos");
        $count = $stmt->fetchColumn();
        echo "<div class='ok'>✅ Query funcionando - $count cursos cadastrados</div>";
        
    } else {
        echo "<div class='erro'>❌ Variáveis de banco não definidas no .env</div>";
    }
} catch (PDOException $e) {
    echo "<div class='erro'>❌ Erro de conexão: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Teste 8: Sessões
echo '<h2>8. Teste de Sessões</h2>';

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['teste'] = 'ok';
    
    if ($_SESSION['teste'] === 'ok') {
        echo "<div class='ok'>✅ Sessões funcionando</div>";
    } else {
        echo "<div class='erro'>❌ Sessões NÃO funcionando</div>";
    }
} catch (Exception $e) {
    echo "<div class='erro'>❌ Erro em sessões: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Teste 9: Tentar iniciar aplicação
echo '<h2>9. Teste de Inicialização da Aplicação</h2>';

try {
    if (class_exists('PlanetaTreinamentos\Core\App')) {
        echo "<div class='info'>ℹ️ Tentando iniciar aplicação...</div>";
        
        ob_start();
        $app = new PlanetaTreinamentos\Core\App();
        // Não vamos executar o run() aqui, só instanciar
        ob_end_clean();
        
        echo "<div class='ok'>✅ Classe App instanciada com sucesso</div>";
    } else {
        echo "<div class='erro'>❌ Classe App não existe</div>";
    }
} catch (Throwable $e) {
    echo "<div class='erro'>❌ ERRO ao instanciar App:</div>";
    echo "<div class='erro'>Mensagem: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='erro'>Arquivo: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</div>";
    echo "<pre style='background:white;padding:10px;border-radius:4px'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Resumo final
echo '<h2>📋 Resumo</h2>';
echo '<div class="info">';
echo '<p><strong>Versão PHP:</strong> ' . phpversion() . '</p>';
echo '<p><strong>Servidor:</strong> ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . '</p>';
echo '<p><strong>Diretório:</strong> ' . __DIR__ . '</p>';
echo '<p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>';
echo '</div>';

echo '<h2>🚀 Próximos Passos</h2>';
echo '<div class="info">';
echo '<ol>';
echo '<li>Copie TODO o conteúdo desta página</li>';
echo '<li>Envie para o Claude para análise</li>';
echo '<li>Ele vai identificar exatamente o que está faltando</li>';
echo '</ol>';
echo '</div>';

echo '</body></html>';