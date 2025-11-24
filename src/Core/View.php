<?php
/**
 * Classe View
 * 
 * Sistema de renderização de views (templates)
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

use Exception;

class View
{
    /**
     * Diretório base das views
     */
    private static string $viewsPath = __DIR__ . '/../../views/';
    
    /**
     * Dados compartilhados com todas as views
     */
    private static array $sharedData = [];
    
    /**
     * Renderiza uma view
     * 
     * @param string $view Nome da view (ex: 'auth/login')
     * @param array $data Dados a passar para a view
     * @param string|null $layout Layout a usar (null = sem layout)
     * @return string HTML renderizado
     */
    public static function render(string $view, array $data = [], ?string $layout = 'app'): string
    {
        // Mescla dados da view com dados compartilhados
        $data = array_merge(self::$sharedData, $data);
        
        // Renderiza a view
        $content = self::renderView($view, $data);
        
        // Se tem layout, renderiza com layout
        if ($layout !== null) {
            $data['content'] = $content;
            return self::renderLayout($layout, $data);
        }
        
        return $content;
    }
    
    /**
     * Renderiza apenas a view (sem layout)
     */
    private static function renderView(string $view, array $data): string
    {
        $viewPath = self::$viewsPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("View não encontrada: $view");
        }
        
        // Extrai variáveis para o escopo da view
        extract($data);
        
        // Inicia buffer de saída
        ob_start();
        
        try {
            require $viewPath;
            return ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
    
    /**
     * Renderiza o layout
     */
    private static function renderLayout(string $layout, array $data): string
    {
        $layoutPath = self::$viewsPath . 'layouts/' . $layout . '.php';
        
        if (!file_exists($layoutPath)) {
            throw new Exception("Layout não encontrado: $layout");
        }
        
        extract($data);
        
        ob_start();
        
        try {
            require $layoutPath;
            return ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
    
    /**
     * Compartilha dados com todas as views
     */
    public static function share(string $key, $value): void
    {
        self::$sharedData[$key] = $value;
    }
    
    /**
     * Renderiza e exibe uma view
     */
    public static function make(string $view, array $data = [], ?string $layout = 'app'): void
    {
        echo self::render($view, $data, $layout);
    }
    
    /**
     * Renderiza view sem layout
     */
    public static function renderPartial(string $view, array $data = []): void
    {
        echo self::render($view, $data, null);
    }
    
    /**
     * Inclui uma partial dentro de uma view
     */
    public static function include(string $partial, array $data = []): void
    {
        echo self::render($partial, $data, null);
    }
    
    /**
     * Verifica se a view existe
     */
    public static function exists(string $view): bool
    {
        $viewPath = self::$viewsPath . str_replace('.', '/', $view) . '.php';
        return file_exists($viewPath);
    }
}
