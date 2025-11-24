<?php
/**
 * Classe Router
 * 
 * Sistema de roteamento da aplicação
 * 
 * @package PlanetaTreinamentos\Core
 * @since 1.0
 */

namespace PlanetaTreinamentos\Core;

use Exception;

class Router
{
    /**
     * Rotas registradas
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    /**
     * Middlewares globais
     */
    private array $globalMiddleware = [];
    
    /**
     * Parâmetros da rota atual
     */
    private array $params = [];
    
    /**
     * Registra rota GET
     */
    public function get(string $uri, string $action, array $middleware = []): self
    {
        return $this->addRoute('GET', $uri, $action, $middleware);
    }
    
    /**
     * Registra rota POST
     */
    public function post(string $uri, string $action, array $middleware = []): self
    {
        return $this->addRoute('POST', $uri, $action, $middleware);
    }
    
    /**
     * Registra rota PUT
     */
    public function put(string $uri, string $action, array $middleware = []): self
    {
        return $this->addRoute('PUT', $uri, $action, $middleware);
    }
    
    /**
     * Registra rota DELETE
     */
    public function delete(string $uri, string $action, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $uri, $action, $middleware);
    }
    
    /**
     * Adiciona uma rota
     */
    private function addRoute(string $method, string $uri, string $action, array $middleware): self
    {
        $this->routes[$method][$uri] = [
            'action' => $action,
            'middleware' => $middleware
        ];
        
        return $this;
    }
    
    /**
     * Adiciona middleware global
     */
    public function middleware(string $middleware): self
    {
        $this->globalMiddleware[] = $middleware;
        return $this;
    }
    
    /**
     * Resolve a rota atual
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        // Suporte para métodos PUT e DELETE via _method
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Procura rota correspondente
        $route = $this->findRoute($method, $uri);
        
        if (!$route) {
            $this->handleNotFound();
            return;
        }
        
        // Executa middlewares
        $this->runMiddleware($route['middleware']);
        
        // Executa action
        $this->runAction($route['action']);
    }
    
    /**
     * Obtém a URI atual
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove trailing slash (exceto para root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = substr($uri, 0, -1);
        }
        
        return $uri;
    }
    
    /**
     * Procura rota correspondente
     */
    private function findRoute(string $method, string $uri): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $routeUri => $route) {
            // Verifica se é match exato
            if ($routeUri === $uri) {
                return $route;
            }
            
            // Verifica se é match com parâmetros
            $pattern = $this->convertToRegex($routeUri);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove match completo
                $this->params = $matches;
                return $route;
            }
        }
        
        return null;
    }
    
    /**
     * Converte URI para regex (suporta {param})
     */
    private function convertToRegex(string $uri): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $uri);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Executa middlewares
     */
    private function runMiddleware(array $middleware): void
    {
        $allMiddleware = array_merge($this->globalMiddleware, $middleware);
        
        foreach ($allMiddleware as $m) {
            $middlewareClass = "PlanetaTreinamentos\\Middleware\\{$m}Middleware";
            
            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware não encontrado: $middlewareClass");
            }
            
            $instance = new $middlewareClass();
            
            if (!method_exists($instance, 'handle')) {
                throw new Exception("Middleware $middlewareClass deve ter método handle()");
            }
            
            $result = $instance->handle();
            
            // Se middleware retornar false, para execução
            if ($result === false) {
                exit;
            }
        }
    }
    
    /**
     * Executa action do controller
     */
    private function runAction(string $action): void
    {
        [$controller, $method] = explode('@', $action);
        
        $controllerClass = "PlanetaTreinamentos\\Controllers\\$controller";
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller não encontrado: $controllerClass");
        }
        
        $instance = new $controllerClass();
        
        if (!method_exists($instance, $method)) {
            throw new Exception("Método $method não encontrado em $controllerClass");
        }
        
        // Passa parâmetros da rota para o método
        call_user_func_array([$instance, $method], $this->params);
    }
    
    /**
     * Trata erro 404
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        
        if (View::exists('errors/404')) {
            View::make('errors/404', [], null);
        } else {
            echo '<h1>404 - Página não encontrada</h1>';
        }
        
        exit;
    }
    
    /**
     * Retorna parâmetros da rota
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
