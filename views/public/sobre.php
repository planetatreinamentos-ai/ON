<?php
/**
 * Página Sobre - Pública
 */

$config = $config ?? [];
$pageTitle = $pageTitle ?? 'Sobre Nós';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="/assets/css/public.css">
    
    <style>
        :root {
            --primary-color: <?= $config['cor_primaria'] ?? '#667eea' ?>;
            --secondary-color: <?= $config['cor_secundaria'] ?? '#764ba2' ?>;
        }
        
        .about-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="fas fa-graduation-cap me-2"></i>
            <?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="fas fa-home me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/sobre">
                        <i class="fas fa-info-circle me-1"></i>Sobre
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/contato">
                        <i class="fas fa-envelope me-1"></i>Contato
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4">Sobre Nós</h1>
                <p class="lead">
                    <?= htmlspecialchars($config['descricao'] ?? 'Somos uma instituição dedicada a formar profissionais qualificados através de cursos técnicos de excelência.') ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Nossa História -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h2 class="text-center fw-bold mb-4">Nossa História</h2>
                <p class="text-muted text-center mb-5">
                    Há mais de <?= date('Y') - 2018 ?> anos transformando vidas através da educação profissionalizante.
                </p>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <p class="lead">
                            Nascemos com o propósito de democratizar o acesso à educação técnica de qualidade, 
                            oferecendo cursos profissionalizantes que realmente preparam nossos alunos para o mercado de trabalho.
                        </p>
                        <p>
                            Ao longo dos anos, formamos centenas de profissionais capacitados, que hoje atuam 
                            com sucesso em suas áreas. Nossa metodologia combina teoria e prática, sempre com 
                            acompanhamento de professores experientes e apaixonados pelo ensino.
                        </p>
                        <p>
                            Acreditamos que a educação transforma vidas e comunidades. Por isso, trabalhamos 
                            incansavelmente para oferecer a melhor experiência de aprendizado aos nossos alunos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nossos Valores -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Nossos Valores</h2>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="feature-icon">
                            <i class="fas fa-award fa-2x text-white"></i>
                        </div>
                        <h5 class="fw-bold">Excelência</h5>
                        <p class="text-muted">
                            Buscamos sempre a mais alta qualidade em tudo que fazemos, 
                            desde o conteúdo dos cursos até o atendimento aos alunos.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="feature-icon">
                            <i class="fas fa-handshake fa-2x text-white"></i>
                        </div>
                        <h5 class="fw-bold">Compromisso</h5>
                        <p class="text-muted">
                            Estamos comprometidos com o sucesso de cada aluno, 
                            oferecendo suporte completo durante toda a jornada de aprendizado.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb fa-2x text-white"></i>
                        </div>
                        <h5 class="fw-bold">Inovação</h5>
                        <p class="text-muted">
                            Acompanhamos as tendências do mercado e atualizamos constantemente 
                            nosso conteúdo para manter nossos alunos sempre preparados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Diferenciais -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Por Que Nos Escolher?</h2>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Professores Qualificados</h5>
                        <p class="text-muted">
                            Nossa equipe é formada por profissionais experientes e atuantes no mercado.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Certificado Reconhecido</h5>
                        <p class="text-muted">
                            Ao concluir o curso, você recebe um certificado válido em todo território nacional.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Aulas Práticas</h5>
                        <p class="text-muted">
                            Aprendizado hands-on, com muita prática e experiência real.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="fw-bold">Suporte Completo</h5>
                        <p class="text-muted">
                            Acompanhamento durante todo o curso e apoio mesmo após a formação.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fw-bold mb-4">Pronto para Começar?</h2>
                <p class="lead text-muted mb-4">
                    Dê o primeiro passo para transformar sua carreira profissional.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="/#cursos" class="btn btn-primary btn-lg">
                        <i class="fas fa-book me-2"></i>Ver Cursos
                    </a>
                    <a href="/contato" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Entre em Contato
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer --> 
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?>. Todos os direitos reservados.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="/admin" class="text-white-50 text-decoration-none">
                    <i class="fas fa-lock me-1"></i>Área Administrativa
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>