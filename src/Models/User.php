<?php
/**
 * Model User
 * 
 * Gerencia operações com usuários (administradores)
 * 
 * @package PlanetaTreinamentos\Models
 * @since 1.0
 */

namespace PlanetaTreinamentos\Models;

use PlanetaTreinamentos\Core\Database;
use PlanetaTreinamentos\Helpers\Logger;

class User
{
    /**
     * Instância do Database
     */
    private Database $db;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca usuário por email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND status = 1";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    /**
     * Busca usuário por ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id AND status = 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Busca usuário por token de reset de senha
     */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM usuarios 
                WHERE token_reset_senha = :token 
                AND token_reset_expira > NOW()
                AND status = 1";
        return $this->db->fetchOne($sql, ['token' => $token]);
    }
    
    /**
     * Cria novo usuário
     */
    public function create(array $data): int
    {
        // Hash da senha
        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        
        return $this->db->insert('usuarios', $data);
    }
    
    /**
     * Atualiza usuário
     */
    public function update(int $id, array $data): bool
    {
        // Se estiver atualizando senha, faz hash
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->update('usuarios', $data, 'id = :id', [':id' => $id]) > 0;
    }
    
    /**
     * Verifica credenciais de login
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        // Verifica se está bloqueado
        if ($user['bloqueado_ate'] && strtotime($user['bloqueado_ate']) > time()) {
            return null;
        }
        
        // Verifica senha
        if (!password_verify($password, $user['senha'])) {
            // Incrementa tentativas de login
            $this->incrementLoginAttempts($user['id']);
            return null;
        }
        
        // Login bem-sucedido - reseta tentativas
        $this->resetLoginAttempts($user['id']);
        
        return $user;
    }
    
    /**
     * Incrementa tentativas de login falhas
     */
    private function incrementLoginAttempts(int $userId): void
    {
        $sql = "UPDATE usuarios 
                SET tentativas_login = tentativas_login + 1 
                WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
        
        // Verifica se atingiu o limite (5 tentativas)
        $user = $this->findById($userId);
        if ($user && $user['tentativas_login'] >= 5) {
            // Bloqueia por 15 minutos
            $this->blockUser($userId, 15);
        }
    }
    
    /**
     * Reseta tentativas de login
     */
    private function resetLoginAttempts(int $userId): void
    {
        $sql = "UPDATE usuarios 
                SET tentativas_login = 0, 
                    bloqueado_ate = NULL 
                WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Bloqueia usuário temporariamente
     */
    private function blockUser(int $userId, int $minutes): void
    {
        $bloqueadoAte = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
        
        $sql = "UPDATE usuarios 
                SET bloqueado_ate = :bloqueado_ate 
                WHERE id = :id";
        $this->db->query($sql, [
            'bloqueado_ate' => $bloqueadoAte,
            'id' => $userId
        ]);
        
        Logger::warning('User blocked due to multiple failed login attempts', [
            'user_id' => $userId,
            'blocked_until' => $bloqueadoAte
        ]);
    }
    
    /**
     * Atualiza último acesso
     */
    public function updateLastAccess(int $userId): void
    {
        $sql = "UPDATE usuarios 
                SET ultimo_acesso = NOW() 
                WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Gera token de reset de senha
     */
    public function generateResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE usuarios 
                SET token_reset_senha = :token,
                    token_reset_expira = :expira
                WHERE id = :id";
        
        $this->db->query($sql, [
            'token' => $token,
            'expira' => $expira,
            'id' => $userId
        ]);
        
        Logger::info('Password reset token generated', ['user_id' => $userId]);
        
        return $token;
    }
    
    /**
     * Limpa token de reset de senha
     */
    public function clearResetToken(int $userId): void
    {
        $sql = "UPDATE usuarios 
                SET token_reset_senha = NULL,
                    token_reset_expira = NULL
                WHERE id = :id";
        
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Redefine senha
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->findByResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        // Atualiza senha
        $this->update($user['id'], ['senha' => $newPassword]);
        
        // Limpa token
        $this->clearResetToken($user['id']);
        
        Logger::info('Password reset successful', ['user_id' => $user['id']]);
        
        return true;
    }
    
    /**
     * Lista todos os usuários
     */
    public function all(): array
    {
        $sql = "SELECT id, nome, email, status, ultimo_acesso, created_at 
                FROM usuarios 
                ORDER BY nome";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Conta total de usuários
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE status = 1";
        return (int) $this->db->fetchColumn($sql);
    }
}
