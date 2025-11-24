<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-cadastro Recebido</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); 
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
            color: #f59e0b;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p { 
            margin-bottom: 15px; 
            font-size: 16px;
            color: #555;
        }
        .interest-info {
            background: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .interest-info strong {
            color: #f59e0b;
            display: block;
            margin-bottom: 5px;
        }
        .what-happens {
            background: #f0fdf4;
            border: 1px solid #22c55e;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .what-happens h3 {
            color: #22c55e;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .what-happens ul {
            margin-left: 20px;
        }
        .what-happens li {
            margin-bottom: 10px;
            color: #555;
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
            color: #f59e0b;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✉️</div>
            <h1>Obrigado, <?= e($nome) ?>!</h1>
            <p>Recebemos seu interesse</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>Pré-cadastro Confirmado!</h2>
            
            <p>Ficamos muito felizes com seu interesse em fazer parte da família <strong>Planeta Treinamentos</strong>!</p>
            
            <p>Seu pré-cadastro foi recebido e registrado com sucesso.</p>
            
            <div class="interest-info">
                <strong>📚 Curso de Interesse:</strong>
                <p><?= e($curso) ?></p>
            </div>
            
            <div class="what-happens">
                <h3>📞 O que acontece agora?</h3>
                <ul>
                    <li><strong>Análise:</strong> Nossa equipe está analisando seu cadastro</li>
                    <li><strong>Contato:</strong> Em breve entraremos em contato com mais informações</li>
                    <li><strong>Matrícula:</strong> Você receberá instruções para finalizar sua matrícula</li>
                    <li><strong>Início:</strong> Informaremos a data de início da próxima turma</li>
                </ul>
            </div>
            
            <p><strong>⏰ Prazo de retorno:</strong> Geralmente respondemos em até 48 horas úteis.</p>
            
            <p style="margin-top: 25px;">Enquanto isso, você pode:</p>
            <ul style="margin-left: 20px; color: #555;">
                <li>Seguir nossas redes sociais para ficar por dentro das novidades</li>
                <li>Conhecer mais sobre nossos cursos no site</li>
                <li>Preparar suas dúvidas para conversarmos</li>
            </ul>
            
            <p style="margin-top: 25px;"><strong>Estamos ansiosos para tê-lo(a) como aluno(a)!</strong> 🎓</p>
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
