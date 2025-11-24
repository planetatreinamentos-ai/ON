<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Certificado está Pronto!</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: white; 
            border-radius: 10px; 
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 40px 30px; 
            text-align: center; 
        }
        .header h1 { 
            font-size: 28px; 
            margin-bottom: 10px; 
        }
        .header .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .content { 
            padding: 40px 30px; 
        }
        .content h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p { 
            margin-bottom: 15px; 
            font-size: 16px;
            color: #555;
        }
        .certificate-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .certificate-info strong {
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }
        .btn { 
            display: inline-block; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            text-decoration: none; 
            padding: 15px 40px; 
            border-radius: 30px; 
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .verification {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .verification code {
            background: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            color: #856404;
        }
        .footer { 
            background: #f8f9fa; 
            text-align: center; 
            padding: 25px; 
            color: #666; 
            font-size: 13px;
            border-top: 1px solid #e9ecef;
        }
        .footer p { 
            margin: 5px 0; 
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .container { 
                margin: 0; 
                border-radius: 0; 
            }
            .content, .header { 
                padding: 25px 20px; 
            }
            .btn {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🎓</div>
            <h1>Parabéns, <?= e($nome) ?>!</h1>
            <p>Seu certificado está pronto</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>Certificado Emitido com Sucesso!</h2>
            
            <p>É com grande satisfação que informamos que seu <strong>certificado de conclusão</strong> está pronto e disponível para download!</p>
            
            <div class="certificate-info">
                <strong>📚 Curso:</strong>
                <p><?= e($curso) ?></p>
                
                <strong>🆔 Código de Verificação:</strong>
                <p><?= e($alunoid) ?></p>
            </div>
            
            <p>Você pode acessar seu certificado clicando no botão abaixo:</p>
            
            <div style="text-align: center;">
                <a href="<?= e($certificate_url) ?>" class="btn" target="_blank">
                    📄 Acessar Meu Certificado
                </a>
            </div>
            
            <div class="verification">
                <strong>🔐 Verificação do Certificado</strong>
                <p style="margin: 10px 0 5px 0;">Qualquer pessoa pode verificar a autenticidade do seu certificado usando o código:</p>
                <p><code><?= e($alunoid) ?></code></p>
            </div>
            
            <p><strong>Dicas importantes:</strong></p>
            <ul style="margin-left: 20px; color: #555;">
                <li>Faça o download e guarde uma cópia em local seguro</li>
                <li>O certificado é válido em todo território nacional</li>
                <li>Você pode reimprimir quando quiser</li>
            </ul>
            
            <p style="margin-top: 25px;">Mais uma vez, <strong>parabéns pela conquista!</strong> 🎉</p>
            <p>Estamos muito orgulhosos do seu esforço e dedicação.</p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Planeta Treinamentos</strong></p>
            <p>Excelência em educação profissional</p>
            
            <div class="social-links">
                <a href="#">📘 Facebook</a> | 
                <a href="#">📷 Instagram</a> | 
                <a href="#">💬 WhatsApp</a>
            </div>
            
            <p>&copy; <?= date('Y') ?> Planeta Treinamentos. Todos os direitos reservados.</p>
            <p style="font-size: 11px; margin-top: 10px;">
                Este é um email automático, por favor não responda.
            </p>
        </div>
    </div>
</body>
</html>
