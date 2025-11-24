<?php
/**
 * Classe CSRF
 * 
 * Proteção contra ataques Cross-Site Request Forgery
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

class CSRF
{
    /**
     * Nome da chave do token na sessão
     */
    private const TOKEN_KEY = 'csrf_token';
    
    /**
     * Tempo de vida do token em segundos (1 hora)
     */
    private const TOKEN_LIFETIME = 3600;
    
    /**
     * Gera um novo token CSRF
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        
        Session::set(self::TOKEN_KEY, [
            'token' => $token,
            'time' => time()
        ]);
        
        return $token;
    }
    
    /**
     * Obtém o token CSRF atual ou gera um novo
     */
    public static function getToken(): string
    {
        $tokenData = Session::get(self::TOKEN_KEY);
        
        // Verifica se o token existe e não expirou
        if (
            $tokenData &&
            isset($tokenData['token'], $tokenData['time']) &&
            (time() - $tokenData['time']) < self::TOKEN_LIFETIME
        ) {
            return $tokenData['token'];
        }
        
        // Gera novo token se não existe ou expirou
        return self::generateToken();
    }
    
    /**
     * Valida o token CSRF
     */
    public static function validateToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }
        
        $tokenData = Session::get(self::TOKEN_KEY);
        
        if (!$tokenData || !isset($tokenData['token'], $tokenData['time'])) {
            return false;
        }
        
        // Verifica se o token expirou
        if ((time() - $tokenData['time']) > self::TOKEN_LIFETIME) {
            return false;
        }
        
        // Usa hash_equals para prevenir timing attacks
        return hash_equals($tokenData['token'], $token);
    }
    
    /**
     * Gera o campo hidden HTML com o token CSRF
     */
    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Gera meta tag com token CSRF (para AJAX)
     */
    public static function metaTag(): string
    {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Valida token do POST request
     */
    public static function validatePost(): bool
    {
        $token = $_POST['csrf_token'] ?? null;
        return self::validateToken($token);
    }
    
    /**
     * Valida token do header (para AJAX)
     */
    public static function validateHeader(): bool
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return self::validateToken($token);
    }
    
    /**
     * Valida token de qualquer fonte (POST ou Header)
     */
    public static function validate(): bool
    {
        // Tenta validar primeiro o POST, depois o header
        return self::validatePost() || self::validateHeader();
    }
    
    /**
     * Verifica o token e lança exceção se inválido
     */
    public static function check(): void
    {
        if (!self::validate()) {
            Logger::warning('CSRF token validation failed', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            http_response_code(403);
            die('Token CSRF inválido. Por favor, recarregue a página e tente novamente.');
        }
    }
}
