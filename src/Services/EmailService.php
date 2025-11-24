<?php
/**
 * Email Service
 * 
 * Serviço para envio de emails usando PHPMailer
 * 
 * @package PlanetaTreinamentos\Services
 * @since 1.0
 */

namespace PlanetaTreinamentos\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PlanetaTreinamentos\Helpers\Logger;

class EmailService
{
    /**
     * Configurações de email
     */
    private array $config;
    
    /**
     * PHPMailer instance
     */
    private PHPMailer $mailer;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    /**
     * Configura PHPMailer
     */
    private function configure(): void
    {
        try {
            // Configurações do servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth = $this->config['smtp']['auth'];
            $this->mailer->Username = $this->config['smtp']['username'];
            $this->mailer->Password = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
            $this->mailer->Port = $this->config['smtp']['port'];
            $this->mailer->Timeout = $this->config['smtp']['timeout'];
            
            // Charset
            $this->mailer->CharSet = 'UTF-8';
            
            // Remetente padrão
            $this->mailer->setFrom(
                $this->config['from']['address'],
                $this->config['from']['name']
            );
            
        } catch (Exception $e) {
            Logger::error('Erro ao configurar email: ' . $e->getMessage());
        }
    }
    
    /**
     * Envia email de certificado pronto
     */
    public function sendCertificateReady(array $aluno, string $certificateUrl): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($aluno['email'], $aluno['nome']);
            $this->mailer->Subject = 'Seu Certificado está Pronto! 🎓';
            
            // Template HTML
            $template = $this->loadTemplate('certificate-ready', [
                'nome' => $aluno['nome'],
                'curso' => $aluno['curso_nome'],
                'alunoid' => $aluno['alunoid'],
                'certificate_url' => $certificateUrl
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $template;
            $this->mailer->AltBody = strip_tags($template);
            
            $result = $this->mailer->send();
            
            if ($result) {
                Logger::info('Email de certificado enviado', [
                    'aluno_id' => $aluno['id'],
                    'email' => $aluno['email']
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email de certificado: ' . $e->getMessage(), [
                'aluno_id' => $aluno['id'] ?? null
            ]);
            return false;
        }
    }
    
    /**
     * Envia email de boas-vindas
     */
    public function sendWelcome(array $aluno): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($aluno['email'], $aluno['nome']);
            $this->mailer->Subject = 'Bem-vindo ao ' . $this->config['from']['name'] . '! 🎉';
            
            $template = $this->loadTemplate('welcome', [
                'nome' => $aluno['nome'],
                'curso' => $aluno['curso_nome'],
                'data_inicio' => formatDate($aluno['data_inicio'])
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $template;
            $this->mailer->AltBody = strip_tags($template);
            
            $result = $this->mailer->send();
            
            if ($result) {
                Logger::info('Email de boas-vindas enviado', [
                    'aluno_id' => $aluno['id'],
                    'email' => $aluno['email']
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email de boas-vindas: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia email de pré-cadastro
     */
    public function sendPreRegistration(array $data): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($data['email'], $data['nome']);
            $this->mailer->Subject = 'Pré-cadastro Recebido!';
            
            $template = $this->loadTemplate('pre-registration', [
                'nome' => $data['nome'],
                'curso' => $data['curso']
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $template;
            $this->mailer->AltBody = strip_tags($template);
            
            $result = $this->mailer->send();
            
            if ($result) {
                Logger::info('Email de pré-cadastro enviado', [
                    'email' => $data['email']
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email de pré-cadastro: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia email customizado
     */
    public function send(string $to, string $toName, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($to, $toName);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            // Anexos
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Carrega template de email
     */
    private function loadTemplate(string $template, array $data = []): string
    {
        $templatePath = __DIR__ . '/../../views/' . $this->config['templates'][$template];
        
        if (!file_exists($templatePath)) {
            Logger::warning("Template de email não encontrado: $template");
            return $this->getDefaultTemplate($data);
        }
        
        extract($data);
        
        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Template padrão básico
     */
    private function getDefaultTemplate(array $data): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $companyName = $config['company']['name'];
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>$companyName</h1>
                </div>
                <div class='content'>
                    <p>Olá {$data['nome']},</p>
                    <p>" . ($data['message'] ?? '') . "</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " $companyName. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Envia email de recuperação de senha
     */
    public function sendPasswordReset(string $email, string $token): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Redefinir Senha - ' . $this->config['from']['name'];
            
            $resetUrl = $this->config['app_url'] . '/reset-password?token=' . $token;
            
            $body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Redefinir Senha</h2>
                    <p>Você solicitou a redefinição de senha.</p>
                    <p>Clique no botão abaixo para criar uma nova senha:</p>
                    <a href='$resetUrl' class='btn'>Redefinir Senha</a>
                    <p>Este link expira em 1 hora.</p>
                    <p>Se você não solicitou, ignore este email.</p>
                </div>
            </body>
            </html>
            ";
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            $result = $this->mailer->send();
            
            if ($result) {
                Logger::info('Email de recuperação de senha enviado', ['email' => $email]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email de recuperação: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia notificação ao admin
     */
    public function sendAdminNotification(string $subject, string $message): bool
    {
        try {
            $appConfig = require __DIR__ . '/../../config/app.php';
            $adminEmail = $appConfig['company']['email'];
            
            if (empty($adminEmail)) {
                return false;
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($adminEmail);
            $this->mailer->Subject = '[ADMIN] ' . $subject;
            
            $body = "
            <!DOCTYPE html>
            <html>
            <head><meta charset='UTF-8'></head>
            <body style='font-family: Arial; line-height: 1.6;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2>$subject</h2>
                    <div style='background: #f5f5f5; padding: 15px; border-left: 4px solid #667eea;'>
                        $message
                    </div>
                    <p style='color: #666; font-size: 12px; margin-top: 20px;'>
                        Enviado automaticamente em " . date('d/m/Y H:i:s') . "
                    </p>
                </div>
            </body>
            </html>
            ";
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar notificação admin: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia email com anexo
     */
    public function sendWithAttachment(string $to, string $toName, string $subject, string $body, array $attachments): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($to, $toName);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            foreach ($attachments as $filePath => $fileName) {
                if (file_exists($filePath)) {
                    $this->mailer->addAttachment($filePath, $fileName);
                }
            }
            
            $result = $this->mailer->send();
            
            if ($result) {
                Logger::info('Email com anexo enviado', [
                    'to' => $to,
                    'attachments' => count($attachments)
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Logger::error('Erro ao enviar email com anexo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Testa conexão SMTP
     */
    public function testConnection(): array
    {
        try {
            $this->mailer->SMTPDebug = 0;
            $this->mailer->Debugoutput = function($str, $level) {
                // Silencioso
            };
            
            if ($this->mailer->smtpConnect()) {
                $this->mailer->smtpClose();
                return [
                    'success' => true,
                    'message' => 'Conexão SMTP estabelecida com sucesso!'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Não foi possível conectar ao servidor SMTP'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtém últimos erros
     */
    public function getLastError(): string
    {
        return $this->mailer->ErrorInfo;
    }
    
    /**
     * Reseta configurações para novo email
     */
    public function reset(): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        $this->mailer->clearCCs();
        $this->mailer->clearBCCs();
        $this->mailer->clearReplyTos();
    }
}
