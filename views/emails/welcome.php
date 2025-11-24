<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo!</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
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
            color: #28a745;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p { 
            margin-bottom: 15px; 
            font-size: 16px;
            color: #555;
        }
        .course-info {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .course-info strong {
            color: #28a745;
            display: block;
            margin-bottom: 5px;
        }
        .next-steps {
            background: #e7f5ff;
            border: 1px solid #0c63e4;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #0c63e4;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .next-steps ul {
            margin-left: 20px;
        }
        .next-steps li {
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
            color: #28a745;
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
            <div class="icon">🎉</div>
            <h1>Bem-vindo, <?= e($nome) ?>!</h1>
            <p>Estamos felizes em tê-lo(a) conosco</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>Sua Jornada Começa Agora!</h2>
            
            <p>É com grande satisfação que confirmamos sua <strong>matrícula</strong> no Planeta Treinamentos!</p>
            
            <p>Você está prestes a embarcar em uma jornada de aprendizado transformadora que vai desenvolver suas habilidades e abrir novas oportunidades profissionais.</p>
            
            <div class="course-info">
                <strong>📚 Seu Curso:</strong>
                <p><?= e($curso) ?></p>
                
                <strong>📅 Data de Início:</strong>
                <p><?= formatDate($data_inicio) ?></p>
            </div>
            
            <div class="next-steps">
                <h3>📋 Próximos Passos:</h3>
                <ul>
                    <li><strong>Prepare-se:</strong> Separe um tempo dedicado para suas aulas</li>
                    <li><strong>Material:</strong> Todo material necessário será fornecido</li>
                    <li><strong>Dúvidas:</strong> Nossa equipe está disponível para ajudar</li>
                    <li><strong>Certificado:</strong> Ao concluir, você receberá seu certificado digital</li>
                </ul>
            </div>
            
            <p><strong>💡 Dica importante:</strong> A participação ativa e a prática constante são fundamentais para o sucesso no curso!</p>
            
            <p style="margin-top: 25px;">Estamos aqui para apoiá-lo(a) em cada etapa desta jornada.</p>
            
            <p><strong>Sucesso nos estudos!</strong> 🚀</p>
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
