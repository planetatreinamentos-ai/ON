<?php
/**
 * Página de Contato - Pública
 */

$config = $config ?? [];
$pageTitle = $pageTitle ?? 'Contato';
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
        
        .contact-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 80px 0 60px;
        }
        
        .contact-info-card {
            transition: transform 0.3s ease;
        }
        
        .contact-info-card:hover {
            transform: translateY(-5px);
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
                    <a class="nav-link" href="/sobre">
                        <i class="fas fa-info-circle me-1"></i>Sobre
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/contato">
                        <i class="fas fa-envelope me-1"></i>Contato
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3">Entre em Contato</h1>
            <p class="lead">Estamos aqui para ajudar. Envie sua mensagem!</p>
        </div>
    </div>
</section>

<!-- Informações de Contato -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <?php if (!empty($config['whatsapp'])): ?>
            <div class="col-md-4">
                <div class="card h-100 text-center shadow-sm contact-info-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fab fa-whatsapp fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">WhatsApp</h5>
                        <p class="card-text">
                            <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp']) ?>" target="_blank" class="text-decoration-none">
                                <?= htmlspecialchars($config['whatsapp']) ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($config['email'])): ?>
            <div class="col-md-4">
                <div class="card h-100 text-center shadow-sm contact-info-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">E-mail</h5>
                        <p class="card-text">
                            <a href="mailto:<?= htmlspecialchars($config['email']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($config['email']) ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($config['endereco'])): ?>
            <div class="col-md-4">
                <div class="card h-100 text-center shadow-sm contact-info-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title">Endereço</h5>
                        <p class="card-text">
                            <?= htmlspecialchars($config['endereco']) ?><br>
                            <?= htmlspecialchars($config['cidade'] ?? '') ?> - <?= htmlspecialchars($config['estado'] ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Formulário de Contato -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Envie sua Mensagem</h2>
                            <p class="text-muted">Preencha o formulário abaixo</p>
                        </div>

                        <?php if (isset($_SESSION['_flash_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['_flash_success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['_flash_success']); endif; ?>

                        <?php if (isset($_SESSION['_flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['_flash_error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['_flash_error']); endif; ?>

                        <form method="POST" action="/contato">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))) ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" name="nome" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Telefone/WhatsApp</label>
                                    <input type="tel" class="form-control" name="telefone" placeholder="(00) 00000-0000">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Assunto</label>
                                    <input type="text" class="form-control" name="assunto" placeholder="Ex: Informações sobre cursos">
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Mensagem *</label>
                                    <textarea class="form-control" name="mensagem" rows="5" required></textarea>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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