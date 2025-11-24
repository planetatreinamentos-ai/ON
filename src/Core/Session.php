<?php
/**
 * Classe Session
 * 
 * Gerencia sessões de forma segura
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

class Session
{
    /**
     * Indica se a sessão foi iniciada
     */
    private static bool $started = false;
    
    /**
     * Inicia a sessão com configurações seguras
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $session = $config['session'];
        
        // Configurações de segurança
        ini_set('session.cookie_httponly', $session['httponly'] ? '1' : '0');
        ini_set('session.cookie_secure', $session['secure'] ? '1' : '0');
        ini_set('session.cookie_samesite', $session['samesite']);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.gc_maxlifetime', $session['lifetime'] * 60);
        
        // Nome da sessão
        session_name($session['name']);
        
        // Parâmetros do cookie
        session_set_cookie_params([
            'lifetime' => $session['lifetime'] * 60,
            'path' => $session['path'],
            'domain' => $session['domain'],
            'secure' => $session['secure'],
            'httponly' => $session['httponly'],
            'samesite' => $session['samesite']
        ]);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::$started = true;
        
        // Regenerar ID periodicamente (a cada 30 minutos)
        if (!self::has('last_regeneration')) {
            self::regenerate();
        } elseif (time() - self::get('last_regeneration') > 1800) {
            self::regenerate();
        }
    }
    
    /**
     * Define um valor na sessão
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtém um valor da sessão
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Verifica se uma chave existe na sessão
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove um valor da sessão
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Limpa toda a sessão
     */
    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }
    
    /**
     * Destrói a sessão completamente
     */
    public static function destroy(): void
    {
        self::start();
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Regenera o ID da sessão (previne session fixation)
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
        self::set('last_regeneration', time());
    }
    
    /**
     * Define uma mensagem flash (aparece uma vez e depois é removida)
     */
    public static function flash(string $key, $value): void
    {
        self::set('_flash_' . $key, $value);
    }
    
    /**
     * Obtém e remove uma mensagem flash
     */
    public static function getFlash(string $key, $default = null)
    {
        $flashKey = '_flash_' . $key;
        $value = self::get($flashKey, $default);
        self::remove($flashKey);
        return $value;
    }
    
    /**
     * Verifica se existe uma mensagem flash
     */
    public static function hasFlash(string $key): bool
    {
        return self::has('_flash_' . $key);
    }
    
    /**
     * Define mensagem de sucesso
     */
    public static function success(string $message): void
    {
        self::flash('success', $message);
    }
    
    /**
     * Define mensagem de erro
     */
    public static function error(string $message): void
    {
        self::flash('error', $message);
    }
    
    /**
     * Define mensagem de aviso
     */
    public static function warning(string $message): void
    {
        self::flash('warning', $message);
    }
    
    /**
     * Define mensagem de informação
     */
    public static function info(string $message): void
    {
        self::flash('info', $message);
    }
    
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isAuthenticated(): bool
    {
        return self::has('user_id') && self::has('user_authenticated');
    }
    
    /**
     * Define o usuário autenticado
     */
    public static function setUser(array $user): void
    {
        self::set('user_id', $user['id']);
        self::set('user_nome', $user['nome']);
        self::set('user_email', $user['email']);
        self::set('user_authenticated', true);
        self::regenerate(); // Previne session fixation
    }
    
    /**
     * Obtém o ID do usuário autenticado
     */
    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }
    
    /**
     * Obtém o nome do usuário autenticado
     */
    public static function getUserName(): ?string
    {
        return self::get('user_nome');
    }
    
    /**
     * Remove o usuário da sessão (logout)
     */
    public static function clearUser(): void
    {
        self::remove('user_id');
        self::remove('user_nome');
        self::remove('user_email');
        self::remove('user_authenticated');
    }
}
