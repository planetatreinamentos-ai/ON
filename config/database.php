<?php
/**
 * Configuração de Banco de Dados
 * 
 * Retorna as configurações de conexão com o banco de dados MySQL/MariaDB
 * 
 * @package PlanetaTreinamentos
 * @since 1.0
 */

return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? '',
    'username' => $_ENV['DB_USERNAME'] ?? '',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    
    // Opções do PDO
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];
