<?php
/**
 * Controller de Configurações
 * 
 * Gerencia configurações white-label do sistema
 * 
 * @package PlanetaTreinamentos\Controllers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Models\Config;
use PlanetaTreinamentos\Helpers\Logger;

class ConfigController
{
    /**
     * Model de configuração
     */
    private Config $configModel;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->configModel = new Config();
    }
    
    /**
     * Exibe página de configurações
     */
    public function index(): void
    {
        // Obtém todas as configurações agrupadas
        $empresa = $this->configModel->getByGroup('empresa');
        $social = $this->configModel->getByGroup('social');
        $tema = $this->configModel->getByGroup('tema');
        
        View::make('config/index', [
            'pageTitle' => 'Configurações',
            'empresa' => $empresa,
            'social' => $social,
            'tema' => $tema
        ], 'admin');
    }
    
    /**
     * Atualiza configurações
     */
    public function update(): void
    {
        CSRF::check();
        
        try {
            // Atualiza configurações
            $this->configModel->updateMany($_POST);
            
            Logger::audit('Configurações atualizadas', $_POST);
            
            Session::success('Configurações atualizadas com sucesso!');
        } catch (\Exception $e) {
            Logger::error('Erro ao atualizar configurações: ' . $e->getMessage());
            Session::error('Erro ao atualizar configurações.');
        }
        
        redirect('/admin/configuracoes');
    }
    
    /**
     * Upload de logo
     */
    public function uploadLogo(): void
    {
        CSRF::check();
        
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            Session::error('Erro ao fazer upload do logo.');
            redirect('/admin/configuracoes');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['logo']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            Session::error('Tipo de arquivo inválido. Use JPG, PNG, GIF ou WEBP.');
            redirect('/admin/configuracoes');
        }
        
        // Upload do arquivo
        $logo = uploadFile($_FILES['logo'], 'logos', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        
        if (!$logo) {
            Session::error('Erro ao fazer upload do logo.');
            redirect('/admin/configuracoes');
        }
        
        // Atualiza configuração
        $this->configModel->set('empresa_logo', $logo, 'empresa', 'file', 'Logo da empresa');
        
        Logger::audit('Logo atualizado', ['logo' => $logo]);
        
        Session::success('Logo atualizado com sucesso!');
        redirect('/admin/configuracoes');
    }
}
