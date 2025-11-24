<?php
/**
 * SCRIPT DE MAPEAMENTO DE ESTRUTURA
 * 
 * COMO USAR:
 * 1. Faça upload deste arquivo para: public_html/mapear-estrutura.php
 * 2. Acesse: https://planetatreinamentos.com.br/mapear-estrutura.php
 * 3. Copie TODO o resultado e me envie
 * 4. DELETE este arquivo após usar (por segurança)
 */

header('Content-Type: text/plain; charset=utf-8');

echo "==========================================\n";
echo "ESTRUTURA DO PROJETO PLANETA TREINAMENTOS\n";
echo "==========================================\n\n";

$baseDir = dirname(__FILE__);
echo "Pasta Base: $baseDir\n\n";

/**
 * Mapeia estrutura de diretórios
 */
function mapearDiretorios($dir, $nivel = 0, $maxNivel = 4) {
    if ($nivel > $maxNivel) return;
    
    $indent = str_repeat('  ', $nivel);
    $items = @scandir($dir);
    
    if (!$items) return;
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        
        // Ignora algumas pastas
        if (in_array($item, ['vendor', 'node_modules', '.git', 'storage/logs'])) {
            echo $indent . "📁 $item/ (ignorado)\n";
            continue;
        }
        
        if (is_dir($path)) {
            echo $indent . "📁 $item/\n";
            mapearDiretorios($path, $nivel + 1, $maxNivel);
        } elseif (is_file($path)) {
            $size = filesize($path);
            $sizeStr = $size > 1024 ? round($size/1024, 1) . 'KB' : $size . 'B';
            echo $indent . "📄 $item ($sizeStr)\n";
        }
    }
}

echo "ESTRUTURA DE PASTAS:\n";
echo "====================\n\n";
mapearDiretorios($baseDir);

echo "\n\n";
echo "==========================================\n";
echo "BUSCANDO ARQUIVOS IMPORTANTES\n";
echo "==========================================\n\n";

// Procura por views
echo "📂 VIEWS:\n";
$viewsPaths = [
    'views',
    'src/views',
    'app/views',
    'resources/views',
    'templates',
    'src/templates'
];

foreach ($viewsPaths as $path) {
    $fullPath = $baseDir . '/' . $path;
    if (is_dir($fullPath)) {
        echo "✅ Encontrado: $path\n";
        
        // Lista subpastas
        $subDirs = glob($fullPath . '/*', GLOB_ONLYDIR);
        foreach ($subDirs as $subDir) {
            echo "   ├─ " . basename($subDir) . "/\n";
            
            // Lista arquivos
            $files = glob($subDir . '/*.php');
            foreach ($files as $file) {
                echo "   │  └─ " . basename($file) . "\n";
            }
        }
    }
}

echo "\n📂 CONTROLLERS:\n";
$controllersPaths = [
    'src/Controllers',
    'app/Controllers',
    'controllers'
];

foreach ($controllersPaths as $path) {
    $fullPath = $baseDir . '/' . $path;
    if (is_dir($fullPath)) {
        echo "✅ Encontrado: $path\n";
        
        $files = glob($fullPath . '/*.php');
        foreach ($files as $file) {
            echo "   ├─ " . basename($file) . "\n";
        }
    }
}

echo "\n📂 MODELS:\n";
$modelsPaths = [
    'src/Models',
    'app/Models',
    'models'
];

foreach ($modelsPaths as $path) {
    $fullPath = $baseDir . '/' . $path;
    if (is_dir($fullPath)) {
        echo "✅ Encontrado: $path\n";
        
        $files = glob($fullPath . '/*.php');
        foreach ($files as $file) {
            echo "   ├─ " . basename($file) . "\n";
        }
    }
}

echo "\n\n";
echo "==========================================\n";
echo "ARQUIVOS DE CONFIGURAÇÃO\n";
echo "==========================================\n\n";

$configFiles = [
    '.env',
    'config.php',
    'src/config.php',
    'app/config.php',
    'composer.json'
];

foreach ($configFiles as $file) {
    $fullPath = $baseDir . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file existe\n";
    }
}

echo "\n\n";
echo "==========================================\n";
echo "EXEMPLO DE ARQUIVO DE ALUNO\n";
echo "==========================================\n\n";

// Tenta encontrar algum arquivo relacionado a alunos
$alunoFiles = [
    'views/admin/alunos/index.php',
    'src/views/admin/alunos/index.php',
    'templates/admin/alunos/index.php',
    'resources/views/admin/alunos/index.php',
    'src/Controllers/AlunoController.php',
    'app/Controllers/AlunoController.php'
];

foreach ($alunoFiles as $file) {
    $fullPath = $baseDir . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ Encontrado: $file\n";
        echo "   Caminho completo: $fullPath\n\n";
        
        // Mostra primeiras 20 linhas
        echo "Primeiras linhas:\n";
        echo "```\n";
        $lines = file($fullPath);
        $preview = array_slice($lines, 0, 20);
        echo implode('', $preview);
        echo "```\n\n";
        break;
    }
}

echo "\n\n";
echo "==========================================\n";
echo "FINALIZANDO\n";
echo "==========================================\n\n";
echo "COPIE TODO ESTE TEXTO E ME ENVIE!\n";
echo "Depois DELETE este arquivo: mapear-estrutura.php\n\n";
?>