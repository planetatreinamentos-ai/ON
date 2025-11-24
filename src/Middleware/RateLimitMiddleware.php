<?php
/**
 * Middleware de Rate Limiting
 * 
 * Limita número de requisições por IP
 * 
 * @package PlanetaTreinamentos\Middleware
 * @since 1.0
 */

namespace PlanetaTreinamentos\Middleware;

use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Helpers\Logger;

class RateLimitMiddleware
{
    /**
     * Chave para armazenar tentativas na sessão
     */
    private const SESSION_KEY = 'rate_limit_attempts';
    
    /**
     * Chave para armazenar timestamp do primeiro acesso
     */
    private const FIRST_ATTEMPT_KEY = 'rate_limit_first_attempt';
    
    /**
     * Processa a requisição
     * 
     * @return bool
     */
    public function handle(): bool
    {
        $config = config('rate_limit');
        
        // Se rate limiting está desabilitado
        if (!$config['enabled']) {
            return true;
        }
        
        $maxAttempts = $config['max_attempts'];
        $decayMinutes = $config['decay_minutes'];
        
        // Obtém tentativas atuais
        $attempts = Session::get(self::SESSION_KEY, 0);
        $firstAttempt = Session::get(self::FIRST_ATTEMPT_KEY);
        
        // Se é a primeira tentativa
        if (!$firstAttempt) {
            Session::set(self::FIRST_ATTEMPT_KEY, time());
            Session::set(self::SESSION_KEY, 1);
            return true;
        }
        
        // Calcula tempo decorrido
        $elapsed = time() - $firstAttempt;
        $decaySeconds = $decayMinutes * 60;
        
        // Se passou o tempo de decay, reseta
        if ($elapsed > $decaySeconds) {
            Session::set(self::FIRST_ATTEMPT_KEY, time());
            Session::set(self::SESSION_KEY, 1);
            return true;
        }
        
        // Incrementa tentativas
        $attempts++;
        Session::set(self::SESSION_KEY, $attempts);
        
        // Se excedeu o limite
        if ($attempts > $maxAttempts) {
            $remainingMinutes = ceil(($decaySeconds - $elapsed) / 60);
            
            Logger::warning('Rate limit exceeded', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts
            ]);
            
            http_response_code(429);
            Session::error("Muitas tentativas. Tente novamente em $remainingMinutes minutos.");
            redirect('/login');
            return false;
        }
        
        return true;
    }
    
    /**
     * Reseta o contador de tentativas
     */
    public static function reset(): void
    {
        Session::remove(self::SESSION_KEY);
        Session::remove(self::FIRST_ATTEMPT_KEY);
    }
}
