<?php
/**
 * Controller de Autenticação - VERSÃO CORRIGIDA
 * 
 * ✅ Logout redireciona para homepage (/)
 * ✅ Login verifica se já está autenticado
 * ✅ Todas as validações de segurança
 * 
 * @package PlanetaTreinamentos\Controllers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Models\User;
use PlanetaTreinamentos\Helpers\Validator;
use PlanetaTreinamentos\Helpers\Logger;
use PlanetaTreinamentos\Middleware\RateLimitMiddleware;

class AuthController
{
    private User $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Exibe formulário de login
     */
    public function showLogin(): void
    {
        // Se já está autenticado, redireciona para admin
        if (Session::isAuthenticated()) {
            redirect('/admin');
        }
        
        View::make('auth/login', [
            'pageTitle' => 'Login'
        ], null);
    }
    
    /**
     * Processa login
     */
    public function login(): void
    {
        // Valida CSRF
        CSRF::check();
        
        // Validação
        $validator = new Validator($_POST);
        $validator
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido')
            ->required('password', 'Senha é obrigatória');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        $email = Validator::sanitizeEmail($_POST['email']);
        $password = $_POST['password'];
        
        // Verifica credenciais
        $user = $this->userModel->verifyCredentials($email, $password);
        
        if (!$user) {
            Logger::warning('Failed login attempt', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            Session::error('Email ou senha inválidos');
            back();
        }
        
        // Login bem-sucedido
        Session::setUser($user);
        
        // Atualiza último acesso
        $this->userModel->updateLastAccess($user['id']);
        
        // Reseta rate limit
        if (class_exists('PlanetaTreinamentos\\Middleware\\RateLimitMiddleware')) {
            RateLimitMiddleware::reset();
        }
        
        Logger::audit('Login realizado', [
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);
        
        Session::success('Bem-vindo, ' . $user['nome'] . '!');
        
        // Redireciona para URL pretendida ou dashboard
        $intendedUrl = Session::get('intended_url', '/admin');
        Session::remove('intended_url');
        redirect($intendedUrl);
    }
    
    /**
     * Logout - CORRIGIDO: Redireciona para homepage
     */
    public function logout(): void
    {
        $userName = Session::getUserName();
        
        Logger::audit('Logout realizado', [
            'user_id' => Session::getUserId()
        ]);
        
        Session::clearUser();
        Session::success('Até logo' . ($userName ? ', ' . $userName : '') . '!');
        
        // ✅ CORREÇÃO: Redireciona para homepage em vez de login
        redirect('/');
    }
    
    /**
     * Exibe formulário de esqueci a senha
     */
    public function showForgotPassword(): void
    {
        if (Session::isAuthenticated()) {
            redirect('/admin');
        }
        
        View::make('auth/forgot-password', [
            'pageTitle' => 'Esqueci minha senha'
        ], null);
    }
    
    /**
     * Processa esqueci a senha
     */
    public function forgotPassword(): void
    {
        CSRF::check();
        
        $validator = new Validator($_POST);
        $validator
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        $email = Validator::sanitizeEmail($_POST['email']);
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            // Por segurança, sempre mostra mensagem de sucesso
            Session::success('Se o email existir, você receberá um link para redefinir sua senha.');
            redirect('/login');
        }
        
        // Gera token
        $token = $this->userModel->generateResetToken($user['id']);
        
        // Envia email (será implementado na Fase 3)
        $resetUrl = url('/redefinir-senha/' . $token);
        
        // Por enquanto, apenas loga (na Fase 3 enviará email real)
        Logger::info('Password reset requested', [
            'user_id' => $user['id'],
            'email' => $email,
            'reset_url' => $resetUrl
        ]);
        
        Session::success('Se o email existir, você receberá um link para redefinir sua senha.');
        redirect('/login');
    }
    
    /**
     * Exibe formulário de redefinir senha
     */
    public function showResetPassword(string $token): void
    {
        if (Session::isAuthenticated()) {
            redirect('/admin');
        }
        
        // Verifica se o token é válido
        $user = $this->userModel->findByResetToken($token);
        
        if (!$user) {
            Session::error('Token inválido ou expirado.');
            redirect('/esqueci-senha');
        }
        
        View::make('auth/reset-password', [
            'token' => $token,
            'pageTitle' => 'Redefinir senha'
        ], null);
    }
    
    /**
     * Processa redefinir senha
     */
    public function resetPassword(): void
    {
        CSRF::check();
        
        $validator = new Validator($_POST);
        $validator
            ->required('token', 'Token é obrigatório')
            ->required('password', 'Senha é obrigatória')
            ->min('password', 6, 'Senha deve ter no mínimo 6 caracteres')
            ->required('password_confirmation', 'Confirmação de senha é obrigatória')
            ->matches('password', 'password_confirmation', 'As senhas não coincidem');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        $token = $_POST['token'];
        $password = $_POST['password'];
        
        $success = $this->userModel->resetPassword($token, $password);
        
        if (!$success) {
            Session::error('Token inválido ou expirado.');
            redirect('/esqueci-senha');
        }
        
        Session::success('Senha redefinida com sucesso! Faça login com sua nova senha.');
        redirect('/login');
    }
}