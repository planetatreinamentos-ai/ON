<?php
/**
 * Funções Auxiliares Globais
 * 
 * Funções helper que podem ser usadas em qualquer lugar da aplicação
 * 
 * @package PlanetaTreinamentos\Helpers
 * @since 1.0
 */

use PlanetaTreinamentos\Helpers\Validator;

if (!function_exists('env')) {
    /**
     * Obtém variável de ambiente
     */
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * Obtém configuração
     */
    function config(string $key, $default = null)
    {
        static $config = null;
        
        if ($config === null) {
            $config = require __DIR__ . '/../../config/app.php';
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redireciona para uma URL
     */
    function redirect(string $url, int $code = 302): void
    {
        header("Location: $url", true, $code);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redireciona para a página anterior
     */
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('url')) {
    /**
     * Gera URL completa
     */
    function url(string $path = ''): string
    {
        $baseUrl = rtrim(config('url'), '/');
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Gera URL para assets
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    /**
     * Escapa HTML (previne XSS)
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    /**
     * Retorna valor antigo do formulário
     */
    function old(string $key, $default = '')
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Gera campo CSRF
     */
    function csrf_field(): string
    {
        return \PlanetaTreinamentos\Core\CSRF::field();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Retorna token CSRF
     */
    function csrf_token(): string
    {
        return \PlanetaTreinamentos\Core\CSRF::getToken();
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die (debug)
     */
    function dd(...$vars): void
    {
        echo '<pre style="background: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 8px; font-family: monospace;">';
        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formata data para padrão brasileiro
     */
    function formatDate(?string $date, string $format = 'd/m/Y'): string
    {
        if (!$date) {
            return '';
        }
        
        try {
            $dt = new DateTime($date);
            return $dt->format($format);
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Formata data e hora para padrão brasileiro
     */
    function formatDateTime(?string $datetime): string
    {
        return formatDate($datetime, 'd/m/Y H:i');
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Formata valor como moeda brasileira
     */
    function formatCurrency(?float $value): string
    {
        if ($value === null) {
            return 'R$ 0,00';
        }
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('formatPhone')) {
    /**
     * Formata número de telefone
     */
    function formatPhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }
        
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $phone;
    }
}

if (!function_exists('formatCPF')) {
    /**
     * Formata CPF
     */
    function formatCPF(?string $cpf): string
    {
        if (!$cpf) {
            return '';
        }
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
}

if (!function_exists('formatCNPJ')) {
    /**
     * Formata CNPJ
     */
    function formatCNPJ(?string $cnpj): string
    {
        if (!$cnpj) {
            return '';
        }
        
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}

if (!function_exists('slugify')) {
    /**
     * Converte string para slug
     */
    function slugify(?string $text): string
    {
        if (!$text) {
            return '';
        }
        
        // Remove acentos
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        
        // Remove caracteres especiais
        $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
        
        // Substitui espaços e múltiplos hífens por um único hífen
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Remove hífens do início e fim
        $text = trim($text, '-');
        
        return strtolower($text);
    }
}

if (!function_exists('generateRandomString')) {
    /**
     * Gera string aleatória
     */
    function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('isAjax')) {
    /**
     * Verifica se a requisição é AJAX
     */
    function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Retorna resposta JSON
     */
    function jsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('uploadFile')) {
    /**
     * Faz upload de arquivo
     */
    function uploadFile(array $file, string $directory, array $allowedExtensions = []): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        // Valida extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
            return null;
        }
        
        // Valida tamanho
        $maxSize = config('upload.max_size', 5242880);
        if ($file['size'] > $maxSize) {
            return null;
        }
        
        // Gera nome único
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Cria diretório se não existe
        $uploadPath = __DIR__ . '/../../public/uploads/' . trim($directory, '/') . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move arquivo
        if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            return $directory . '/' . $filename;
        }
        
        return null;
    }
}

if (!function_exists('deleteFile')) {
    /**
     * Deleta arquivo
     */
    function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        
        $fullPath = __DIR__ . '/../../public/uploads/' . ltrim($path, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
}
