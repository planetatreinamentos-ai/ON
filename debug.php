<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $app = new PlanetaTreinamentos\Core\App();
    $app->run();
} catch (Exception $e) {
    echo '<pre>';
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo '</pre>';
}