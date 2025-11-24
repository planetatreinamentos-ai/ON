<?php
/**
 * WhatsApp Service
 * 
 * Serviço para envio de mensagens via WhatsApp usando Evolution API
 * 
 * @package PlanetaTreinamentos\Services
 * @since 1.0
 */

namespace PlanetaTreinamentos\Services;

use PlanetaTreinamentos\Helpers\Logger;
use Exception;

class WhatsAppService
{
    /**
     * Configurações
     */
    private array $config;
    
    /**
     * Base URL da API
     */
    private string $apiUrl;
    
    /**
     * API Key
     */
    private string $apiKey;
    
    /**
     * Instance Name
     */
    private string $instance;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->apiUrl = $this->config['whatsapp']['api_url'] ?? '';
        $this->apiKey = $this->config['whatsapp']['api_key'] ?? '';
        $this->instance = $this->config['whatsapp']['instance'] ?? '';
    }
    
    /**
     * Verifica se o serviço está disponível
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiUrl) && 
               !empty($this->apiKey) && 
               !empty($this->instance) &&
               ($this->config['whatsapp']['enabled'] ?? false);
    }
    
    /**
     * Envia mensagem de certificado pronto
     */
    public function sendCertificateReady(array $aluno, string $certificateUrl): bool
    {
        if (!$this->isAvailable()) {
            Logger::info('WhatsApp não está habilitado');
            return false;
        }
        
        $phone = $this->formatPhone($aluno['whatsapp']);
        
        if (!$phone) {
            Logger::warning('Telefone do aluno inválido', ['aluno_id' => $aluno['id']]);
            return false;
        }
        
        $message = $this->config['whatsapp']['templates']['certificate_ready'];
        $message = str_replace([
            '{nome}',
            '{curso}',
            '{url}'
        ], [
            $aluno['nome'],
            $aluno['curso_nome'],
            $certificateUrl
        ], $message);
        
        return $this->sendMessage($phone, $message);
    }
    
    /**
     * Envia mensagem de boas-vindas
     */
    public function sendWelcome(array $aluno): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        $phone = $this->formatPhone($aluno['whatsapp']);
        
        if (!$phone) {
            return false;
        }
        
        $message = $this->config['whatsapp']['templates']['welcome'];
        $message = str_replace([
            '{nome}',
            '{curso}'
        ], [
            $aluno['nome'],
            $aluno['curso_nome']
        ], $message);
        
        return $this->sendMessage($phone, $message);
    }
    
    /**
     * Envia mensagem customizada
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $url = rtrim($this->apiUrl, '/') . "/message/sendText/{$this->instance}";
            
            $data = [
                'number' => $phone,
                'text' => $message
            ];
            
            $response = $this->makeRequest($url, 'POST', $data);
            
            if ($response && isset($response['key'])) {
                Logger::info('Mensagem WhatsApp enviada', [
                    'phone' => $phone,
                    'message_id' => $response['key']['id'] ?? null
                ]);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia imagem com legenda
     */
    public function sendImage(string $phone, string $imageUrl, string $caption = ''): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $url = rtrim($this->apiUrl, '/') . "/message/sendMedia/{$this->instance}";
            
            $data = [
                'number' => $phone,
                'mediatype' => 'image',
                'media' => $imageUrl,
                'caption' => $caption
            ];
            
            $response = $this->makeRequest($url, 'POST', $data);
            
            return $response && isset($response['key']);
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar imagem WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Formata número de telefone
     */
    private function formatPhone(string $phone): ?string
    {
        // Remove tudo exceto números
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (empty($phone)) {
            return null;
        }
        
        // Adiciona código do país se não tiver
        if (strlen($phone) === 11) {
            $phone = '55' . $phone;
        } elseif (strlen($phone) === 10) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Faz requisição para a API
     */
    private function makeRequest(string $url, string $method = 'GET', array $data = []): ?array
    {
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->apiKey
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        Logger::error('Erro na requisição WhatsApp API', [
            'http_code' => $httpCode,
            'response' => $response
        ]);
        
        return null;
    }
    
    /**
     * Verifica status da instância
     */
    public function checkConnection(): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $url = rtrim($this->apiUrl, '/') . "/instance/connectionState/{$this->instance}";
            $response = $this->makeRequest($url, 'GET');
            
            return $response && ($response['state'] === 'open');
            
        } catch (Exception $e) {
            Logger::error('Erro ao verificar conexão WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}
