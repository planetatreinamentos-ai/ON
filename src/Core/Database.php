<?php
/**
 * Classe Database
 * 
 * Gerencia a conexão com o banco de dados usando PDO
 * Implementa Singleton pattern para garantir uma única instância
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

use PDO;
use PDOException;
use Exception;
use PlanetaTreinamentos\Helpers\Logger;

class Database
{
    /**
     * Instância única do Database (Singleton)
     */
    private static ?Database $instance = null;
    
    /**
     * Conexão PDO
     */
    private ?PDO $pdo = null;
    
    /**
     * Configurações do banco
     */
    private array $config;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct()
    {
        $this->config = require __DIR__ . '/../../config/database.php';
        $this->connect();
    }
    
    /**
     * Previne clonagem (Singleton)
     */
    private function __clone() {}
    
    /**
     * Previne deserialização (Singleton)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Retorna a instância única do Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Estabelece conexão com o banco de dados
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );
            
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
            
        } catch (PDOException $e) {
            Logger::error('Database connection failed: ' . $e->getMessage());
            throw new Exception('Erro ao conectar com o banco de dados');
        }
    }
    
    /**
     * Retorna a conexão PDO
     */
    public function getConnection(): PDO
    {
        // Verifica se a conexão ainda está ativa
        if ($this->pdo === null) {
            $this->connect();
        }
        
        return $this->pdo;
    }
    
    /**
     * Executa uma query preparada com segurança
     * 
     * @param string $sql Query SQL com placeholders
     * @param array $params Parâmetros para bind
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
            
        } catch (PDOException $e) {
            Logger::error('Query error: ' . $e->getMessage(), [
                'sql' => $sql,
                'params' => $params
            ]);
            throw new Exception('Erro ao executar query');
        }
    }
    
    /**
     * Retorna um único registro
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Retorna múltiplos registros
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Retorna uma única coluna
     */
    public function fetchColumn(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Insere um registro e retorna o ID inserido
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        
        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * Atualiza registros
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setParts = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $setParts[] = "$key = :set_$key";
            $params[":set_$key"] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $setParts),
            $where
        );
        
        $params = array_merge($params, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Deleta registros
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    /**
     * Reverte uma transação
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Verifica se está em uma transação
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
