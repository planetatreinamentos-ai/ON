<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Config
 * Gerencia configurações white-label do sistema
 * 
 * ESTRUTURA REAL:
 * - id, chave, valor, tipo, grupo, descricao, created_at, updated_at
 * - NÃO TEM coluna 'status'
 */
class Config
{
    private PDO $db;
    private static ?array $cache = null;
    
    /**
     * Construtor - $db é opcional para compatibilidade
     */
    public function __construct(PDO $db = null)
    {
        if ($db === null) {
            // Compatibilidade: tenta obter conexão do Database
            $this->db = \PlanetaTreinamentos\Core\Database::getInstance()->getConnection();
        } else {
            $this->db = $db;
        }
    }
    
    /**
     * Obtém todas as configurações
     */
    public function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        
        $sql = "SELECT chave, valor, tipo, grupo, descricao 
                FROM configuracoes 
                ORDER BY grupo, chave";
        
        $stmt = $this->db->query($sql);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organiza por chave
        $result = [];
        foreach ($configs as $config) {
            $result[$config['chave']] = $this->parseValue($config['valor'], $config['tipo']);
        }
        
        self::$cache = $result;
        return $result;
    }
    
    /**
     * Obtém configuração por chave
     */
    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }
    
    /**
     * Obtém configurações por grupo
     */
    public function getByGroup(string $group): array
    {
        $sql = "SELECT chave, valor, tipo, descricao 
                FROM configuracoes 
                WHERE grupo = ?
                ORDER BY chave";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group]);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($configs as $config) {
            $result[$config['chave']] = [
                'valor' => $this->parseValue($config['valor'], $config['tipo']),
                'tipo' => $config['tipo'],
                'descricao' => $config['descricao']
            ];
        }
        
        return $result;
    }
    
    /**
     * Define configuração
     */
    public function set(string $key, $value, string $grupo = 'geral', string $tipo = 'text', ?string $descricao = null): bool
    {
        // Verifica se já existe
        $stmt = $this->db->prepare("SELECT id FROM configuracoes WHERE chave = ?");
        $stmt->execute([$key]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $valueString = $this->stringifyValue($value, $tipo);
        
        if ($existing) {
            // Atualiza
            $stmt = $this->db->prepare("
                UPDATE configuracoes SET
                    valor = ?,
                    tipo = ?,
                    grupo = ?,
                    descricao = ?,
                    updated_at = NOW()
                WHERE chave = ?
            ");
            $result = $stmt->execute([$valueString, $tipo, $grupo, $descricao, $key]);
        } else {
            // Insere
            $stmt = $this->db->prepare("
                INSERT INTO configuracoes (chave, valor, tipo, grupo, descricao, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$key, $valueString, $tipo, $grupo, $descricao]);
        }
        
        // Limpa cache
        self::$cache = null;
        
        return $result;
    }
    
    /**
     * Atualiza múltiplas configurações
     */
    public function updateMany(array $configs): bool
    {
        try {
            $this->db->beginTransaction();
            
            foreach ($configs as $key => $value) {
                $stmt = $this->db->prepare("
                    SELECT tipo, grupo FROM configuracoes WHERE chave = ?
                ");
                $stmt->execute([$key]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $valueString = $this->stringifyValue($value, $existing['tipo']);
                    
                    $stmt = $this->db->prepare("
                        UPDATE configuracoes SET valor = ?, updated_at = NOW()
                        WHERE chave = ?
                    ");
                    $stmt->execute([$valueString, $key]);
                }
            }
            
            $this->db->commit();
            self::$cache = null;
            
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Remove configuração
     */
    public function delete(string $key): bool
    {
        $stmt = $this->db->prepare("DELETE FROM configuracoes WHERE chave = ?");
        $result = $stmt->execute([$key]);
        self::$cache = null;
        return $result;
    }
    
    /**
     * Converte string do banco para valor tipado
     */
    private function parseValue(?string $value, string $type)
    {
        if ($value === null) {
            return null;
        }
        
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            
            case 'json':
                return json_decode($value, true) ?? [];
            
            default:
                return $value;
        }
    }
    
    /**
     * Converte valor para string do banco
     */
    private function stringifyValue($value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }
        
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            
            case 'json':
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            
            default:
                return (string) $value;
        }
    }
    
    /**
     * Inicializa configurações padrão
     */
    public function initializeDefaults(): void
    {
        $defaults = [
            // Empresa
            ['chave' => 'empresa_nome', 'valor' => 'Planeta Treinamentos', 'tipo' => 'text', 'grupo' => 'empresa', 'descricao' => 'Nome da empresa'],
            ['chave' => 'empresa_cnpj', 'valor' => '', 'tipo' => 'text', 'grupo' => 'empresa', 'descricao' => 'CNPJ'],
            ['chave' => 'empresa_telefone', 'valor' => '+55 91 98866-6900', 'tipo' => 'text', 'grupo' => 'empresa', 'descricao' => 'Telefone'],
            ['chave' => 'empresa_email', 'valor' => 'contato@planetatreinamentos.com.br', 'tipo' => 'text', 'grupo' => 'empresa', 'descricao' => 'Email'],
            ['chave' => 'empresa_endereco', 'valor' => 'Belém, Pará, Brasil', 'tipo' => 'text', 'grupo' => 'empresa', 'descricao' => 'Endereço'],
            ['chave' => 'empresa_logo', 'valor' => '', 'tipo' => 'file', 'grupo' => 'empresa', 'descricao' => 'Logo da empresa'],
            
            // Redes Sociais
            ['chave' => 'social_instagram', 'valor' => '@planetatreinamentos', 'tipo' => 'text', 'grupo' => 'social', 'descricao' => 'Instagram'],
            ['chave' => 'social_facebook', 'valor' => '', 'tipo' => 'text', 'grupo' => 'social', 'descricao' => 'Facebook'],
            ['chave' => 'social_whatsapp', 'valor' => '+5591988666900', 'tipo' => 'text', 'grupo' => 'social', 'descricao' => 'WhatsApp'],
            
            // Tema
            ['chave' => 'tema_cor_primaria', 'valor' => '#1a365d', 'tipo' => 'text', 'grupo' => 'tema', 'descricao' => 'Cor primária'],
            ['chave' => 'tema_cor_secundaria', 'valor' => '#f59e0b', 'tipo' => 'text', 'grupo' => 'tema', 'descricao' => 'Cor secundária'],
        ];
        
        foreach ($defaults as $config) {
            // Só insere se não existir
            $stmt = $this->db->prepare("SELECT id FROM configuracoes WHERE chave = ?");
            $stmt->execute([$config['chave']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                $stmt = $this->db->prepare("
                    INSERT INTO configuracoes (chave, valor, tipo, grupo, descricao, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $config['chave'],
                    $config['valor'],
                    $config['tipo'],
                    $config['grupo'],
                    $config['descricao']
                ]);
            }
        }
        
        self::$cache = null;
    }

    /**
     * Lista todas configurações com metadados
     */
    public function getAllWithMeta(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM configuracoes 
            ORDER BY grupo, chave
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica se chave existe
     */
    public function exists(string $key): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM configuracoes WHERE chave = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() > 0;
    }
}
