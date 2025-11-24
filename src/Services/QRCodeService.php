<?php
/**
 * QR Code Service
 * 
 * Serviço para geração de QR Codes
 * 
 * @package PlanetaTreinamentos\Services
 * @since 1.0
 */

namespace PlanetaTreinamentos\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

class QRCodeService
{
    /**
     * Diretório de saída
     */
    private string $outputPath;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->outputPath = __DIR__ . '/../../storage/qrcodes/';
        
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * Gera QR Code para verificação de certificado
     * 
     * @param string $alunoId ID do aluno
     * @return string|null Caminho do QR Code gerado
     */
    public function generate(string $alunoId): ?string
    {
        try {
            $config = require __DIR__ . '/../../config/app.php';
            $baseUrl = $config['url'];
            
            // URL de verificação
            $verifyUrl = "$baseUrl/verificar/$alunoId";
            
            // Nome do arquivo
            $fileName = "qrcode_{$alunoId}.png";
            $filePath = $this->outputPath . $fileName;
            
            // Se já existe, retorna
            if (file_exists($filePath)) {
                return $filePath;
            }
            
            // Gera QR Code usando Endroid (se disponível)
            if (class_exists('Endroid\QrCode\Builder\Builder')) {
                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($verifyUrl)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                    ->size(300)
                    ->margin(10)
                    ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                    ->build();
                
                $result->saveToFile($filePath);
            } else {
                // Fallback: usa Google Charts API
                $this->generateWithGoogleCharts($verifyUrl, $filePath);
            }
            
            return $filePath;
            
        } catch (Exception $e) {
            \PlanetaTreinamentos\Helpers\Logger::error('Erro ao gerar QR Code: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Gera QR Code usando Google Charts API (fallback)
     */
    private function generateWithGoogleCharts(string $data, string $filePath): void
    {
        $size = '300x300';
        $url = "https://chart.googleapis.com/chart?chs=$size&cht=qr&chl=" . urlencode($data);
        
        $qrCode = file_get_contents($url);
        
        if ($qrCode) {
            file_put_contents($filePath, $qrCode);
        }
    }
    
    /**
     * Remove QR Code
     */
    public function delete(string $alunoId): bool
    {
        $fileName = "qrcode_{$alunoId}.png";
        $filePath = $this->outputPath . $fileName;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
}
