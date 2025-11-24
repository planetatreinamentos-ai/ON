<?php
/**
 * Middleware de Autenticação
 * 
 * Verifica se o usuário está autenticado
 * 
 * @package PlanetaTreinamentos\Middleware
 * @since 1.0
 */

namespace PlanetaTreinamentos\Middleware;

use PlanetaTreinamentos\Core\Session;

class AuthMiddleware
{
    /**
     * Processa a requisição
     * 
     * @return bool
     */
    public function handle(): bool
    {
        // Verifica se o usuário está autenticado
        if (!Session::isAuthenticated()) {
            // Salva a URL que o usuário tentou acessar
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            
            // Mensagem de erro
            Session::error('Você precisa estar autenticado para acessar esta página.');
            
            // Redireciona para login
            redirect('/login');
            return false;
        }
        
        return true;
    }
}
