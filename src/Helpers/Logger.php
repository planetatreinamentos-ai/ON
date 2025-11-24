<?php
/**
 * Classe Logger
 * 
 * Sistema de logs para auditoria e debug
 * 
 * @package PlanetaTreinamentos\Helpers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Helpers;

class Logger
{
    /**
     * Níveis de log
     */
    private const LEVELS = [
        'debug' => 1,
        'info' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5
    ];
    
    /**
     * Configurações de log
     */
    private static ?array $config = null;
    
    /**
     * Obtém as configurações de log
     */
    private static function getConfig(): array
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/app.php';
            self::$config = self::$config['log'];
        }
        return self::$config;
    }
    
    /**
     * Escreve uma mensagem no log
     */
    private static function write(string $level, string $message, array $context = []): void
    {
        $config = self::getConfig();
        
        // Verifica se o nível está habilitado
        if (self::LEVELS[$level] < self::LEVELS[$config['level']]) {
            return;
        }
        
        // Prepara o diretório de logs
        $logPath = $config['path'];
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
        
        // Nome do arquivo de log (um por dia)
        $filename = $logPath . date('Y-m-d') . '.log';
        
        // Formata a mensagem
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        $contextJson = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        $logMessage = "[$timestamp] [$levelUpper] $message$contextJson" . PHP_EOL;
        
        // Escreve no arquivo
        file_put_contents($filename, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Limpa logs antigos
        self::cleanOldLogs();
    }
    
    /**
     * Remove logs antigos
     */
    private static function cleanOldLogs(): void
    {
        static $cleaned = false;
        
        // Executa apenas uma vez por request
        if ($cleaned) {
            return;
        }
        
        $config = self::getConfig();
        $logPath = $config['path'];
        $maxFiles = $config['max_files'];
        
        $files = glob($logPath . '*.log');
        if (count($files) > $maxFiles) {
            // Ordena por data (mais antigos primeiro)
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove os mais antigos
            $toRemove = count($files) - $maxFiles;
            for ($i = 0; $i < $toRemove; $i++) {
                @unlink($files[$i]);
            }
        }
        
        $cleaned = true;
    }
    
    /**
     * Log de debug
     */
    public static function debug(string $message, array $context = []): void
    {
        self::write('debug', $message, $context);
    }
    
    /**
     * Log de informação
     */
    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }
    
    /**
     * Log de aviso
     */
    public static function warning(string $message, array $context = []): void
    {
        self::write('warning', $message, $context);
    }
    
    /**
     * Log de erro
     */
    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }
    
    /**
     * Log crítico
     */
    public static function critical(string $message, array $context = []): void
    {
        self::write('critical', $message, $context);
    }
    
    /**
     * Log de auditoria (ações administrativas)
     */
    public static function audit(string $action, array $data = []): void
    {
        $context = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => $data
        ];
        
        self::info("AUDIT: $action", $context);
        
        // Também salva no banco de dados
        try {
            $db = \PlanetaTreinamentos\Core\Database::getInstance();
            $db->insert('logs_sistema', [
                'nivel' => 'info',
                'usuario_id' => $context['user_id'],
                'acao' => $action,
                'descricao' => $action,
                'dados' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'ip' => $context['ip'],
                'user_agent' => $context['user_agent']
            ]);
        } catch (\Exception $e) {
            // Silenciosamente falha se não conseguir gravar no banco
            self::error('Failed to write audit log to database: ' . $e->getMessage());
        }
    }
}
