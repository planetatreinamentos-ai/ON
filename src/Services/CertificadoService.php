<?php
/**
 * Certificate Service
 * 
 * Serviço para geração de certificados digitais
 * Usa GD para manipulação de imagens
 * 
 * @package PlanetaTreinamentos\Services
 * @since 1.0
 */

namespace PlanetaTreinamentos\Services;

use PlanetaTreinamentos\Helpers\Logger;
use Exception;

class CertificateService
{
    /**
     * Diretório de templates
     */
    private string $templatePath;
    
    /**
     * Diretório de saída
     */
    private string $outputPath;
    
    /**
     * Configurações
     */
    private array $config;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->templatePath = $this->config['certificate']['template_path'];
        $this->outputPath = $this->config['certificate']['path'];
        
        // Cria diretório se não existe
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * Gera certificado para um aluno
     * 
     * @param array $aluno Dados completos do aluno
     * @return string Caminho do certificado gerado
     */
    public function generate(array $aluno): string
    {
        try {
            // Carrega template
            $templateFile = $this->templatePath . $this->config['certificate']['template'];
            
            if (!file_exists($templateFile)) {
                throw new Exception("Template de certificado não encontrado: $templateFile");
            }
            
            // Carrega imagem base
            $image = imagecreatefrompng($templateFile);
            
            if (!$image) {
                throw new Exception("Erro ao carregar template do certificado");
            }
            
            // Define cores
            $preto = imagecolorallocate($image, 0, 0, 0);
            $azul = imagecolorallocate($image, 26, 54, 93); // #1a365d
            
            // Fontes
            $fontRegular = __DIR__ . '/../../public/assets/fonts/arial.ttf';
            $fontBold = __DIR__ . '/../../public/assets/fonts/arialbd.ttf';
            
            // Se não existir as fontes, usa fonte do sistema
            if (!file_exists($fontRegular)) {
                $fontRegular = 3; // GD font
                $fontBold = 5;
            }
            
            // Nome do aluno (centralizado, grande)
            $this->addText($image, $fontBold, 40, $aluno['nome'], 600, 350, $azul, 'center');
            
            // Frase do certificado
            $frase = $aluno['frase_certificado'];
            $this->addTextWrapped($image, $fontRegular, 18, $frase, 600, 450, 1000, $preto, 'center');
            
            // Curso
            $curso = "Curso: " . $aluno['curso_nome'];
            $this->addText($image, $fontBold, 20, $curso, 600, 550, $preto, 'center');
            
            // Carga horária
            $cargaHoraria = "Carga Horária: " . $aluno['carga_horaria'] . " horas";
            $this->addText($image, $fontRegular, 16, $cargaHoraria, 600, 590, $preto, 'center');
            
            // Data de conclusão
            $dataConclusao = "Concluído em: " . formatDate($aluno['data_conclusao'], 'd/m/Y');
            $this->addText($image, $fontRegular, 16, $dataConclusao, 600, 630, $preto, 'center');
            
            // Professor
            $professor = $aluno['professor_nome'];
            $this->addText($image, $fontRegular, 14, $professor, 300, 850, $preto, 'center');
            
            // Assinatura do professor (se houver)
            if (!empty($aluno['professor_assinatura'])) {
                $assinaturaPath = __DIR__ . '/../../public/uploads/' . $aluno['professor_assinatura'];
                if (file_exists($assinaturaPath)) {
                    $assinatura = imagecreatefrompng($assinaturaPath);
                    if ($assinatura) {
                        imagecopyresampled($image, $assinatura, 230, 750, 0, 0, 140, 70, imagesx($assinatura), imagesy($assinatura));
                        imagedestroy($assinatura);
                    }
                }
            }
            
            // Adiciona QR Code (será implementado com QRCodeService)
            $qrCodeService = new QRCodeService();
            $qrCodePath = $qrCodeService->generate($aluno['alunoid']);
            
            if ($qrCodePath && file_exists($qrCodePath)) {
                $qrCode = imagecreatefrompng($qrCodePath);
                if ($qrCode) {
                    imagecopyresampled($image, $qrCode, 900, 750, 0, 0, 100, 100, imagesx($qrCode), imagesy($qrCode));
                    imagedestroy($qrCode);
                }
            }
            
            // Código de verificação
            $codigoVerificacao = "Código: " . $aluno['alunoid'];
            $this->addText($image, $fontRegular, 10, $codigoVerificacao, 950, 870, $preto, 'center');
            
            // Nome do arquivo
            $fileName = $this->generateFileName($aluno);
            $filePath = $this->outputPath . $fileName;
            
            // Salva imagem
            $quality = $this->config['certificate']['quality'];
            imagejpeg($image, $filePath, $quality);
            
            // Libera memória
            imagedestroy($image);
            
            Logger::audit('Certificado gerado', [
                'aluno_id' => $aluno['id'],
                'aluno_nome' => $aluno['nome'],
                'arquivo' => $fileName
            ]);
            
            return $filePath;
            
        } catch (Exception $e) {
            Logger::error('Erro ao gerar certificado: ' . $e->getMessage(), [
                'aluno_id' => $aluno['id'] ?? null
            ]);
            throw $e;
        }
    }
    
    /**
     * Adiciona texto na imagem
     */
    private function addText($image, $font, $size, $text, $x, $y, $color, $align = 'left'): void
    {
        if (is_string($font) && file_exists($font)) {
            // TrueType font
            $bbox = imagettfbbox($size, 0, $font, $text);
            $textWidth = abs($bbox[4] - $bbox[0]);
            
            if ($align === 'center') {
                $x = $x - ($textWidth / 2);
            } elseif ($align === 'right') {
                $x = $x - $textWidth;
            }
            
            imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
        } else {
            // GD font
            if ($align === 'center') {
                $textWidth = imagefontwidth($font) * strlen($text);
                $x = $x - ($textWidth / 2);
            }
            imagestring($image, $font, $x, $y, $text, $color);
        }
    }
    
    /**
     * Adiciona texto com quebra de linha
     */
    private function addTextWrapped($image, $font, $size, $text, $x, $y, $maxWidth, $color, $align = 'left'): void
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            
            if (is_string($font) && file_exists($font)) {
                $bbox = imagettfbbox($size, 0, $font, $testLine);
                $testWidth = abs($bbox[4] - $bbox[0]);
            } else {
                $testWidth = imagefontwidth($font) * strlen($testLine);
            }
            
            if ($testWidth > $maxWidth && $currentLine) {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        // Desenha cada linha
        $lineHeight = $size * 1.5;
        foreach ($lines as $i => $line) {
            $lineY = $y + ($i * $lineHeight);
            $this->addText($image, $font, $size, $line, $x, $lineY, $color, $align);
        }
    }
    
    /**
     * Gera nome do arquivo do certificado
     */
    private function generateFileName(array $aluno): string
    {
        $slug = slugify($aluno['nome']);
        $timestamp = time();
        return "{$aluno['id']}_{$slug}_{$timestamp}.jpg";
    }
    
    /**
     * Remove certificado
     */
    public function delete(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
