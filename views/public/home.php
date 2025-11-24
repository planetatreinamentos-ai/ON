<?php
/**
 * Homepage Pública - VERSÃO ULTRA CORRIGIDA
 * Todas as validações defensivas aplicadas
 */

// Função auxiliar para escapar com segurança
function safe_html($value, $default = '') {
    if (is_array($value)) {
        return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
    }
    if ($value === null || $value === '') {
        return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Dados fornecidos pelo controller com valores padrão
$cursos = $cursos ?? [];
$professores = $professores ?? [];
$alunosDestaque = $alunosDestaque ?? [];
$estatisticas = $estatisticas ?? [
    'total_alunos' => 0,
    'total_turmas' => 0,
    'anos_experiencia' => 0
];
$config = $config ?? [];

$currentPage = 'home';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planeta Treinamentos - Cursos Profissionalizantes</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="/assets/css/public.css">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="fas fa-graduation-cap me-2"></i>
            Planeta Treinamentos
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#cursos">
                        <i class="fas fa-book me-1"></i>Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#professores">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Professores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#alunos">
                        <i class="fas fa-users me-1"></i>Alunos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#interesse">
                        <i class="fas fa-paper-plane me-1"></i>Contato
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sobre">
                        <i class="fas fa-info-circle me-1"></i>Sobre
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white mb-4 mb-lg-0" data-aos="fade-right">
                <h1 class="display-3 fw-bold mb-4">
                    Transforme sua Carreira
                </h1>
                <p class="lead mb-4">
                    Aprenda com os melhores profissionais e conquiste seu certificado
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#cursos" class="btn btn-light btn-lg">
                        <i class="fas fa-graduation-cap me-2"></i>Ver Cursos
                    </a>
                    <a href="#interesse" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-pencil-alt me-2"></i>Inscreva-se
                    </a>
                </div>
                
                <!-- Estatísticas -->
                <div class="row mt-5">
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= number_format($estatisticas['total_alunos'] ?? 0) ?>+</h3>
                        <p class="mb-0 small">Alunos</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= number_format($estatisticas['total_turmas'] ?? 0) ?>+</h3>
                        <p class="mb-0 small">Turmas</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= number_format($estatisticas['anos_experiencia'] ?? 0) ?>+</h3>
                        <p class="mb-0 small">Anos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="bg-white p-5 rounded shadow-lg text-center">
                    <i class="fas fa-graduation-cap fa-5x text-primary mb-3"></i>
                    <h3 class="text-dark">Seu Futuro Começa Aqui</h3>
                    <p class="text-muted">Certificação profissional reconhecida no mercado</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cursos Section -->
<section id="cursos" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-4 fw-bold">Nossos Cursos</h2>
            <p class="lead text-muted">Conheça os cursos que vão transformar sua carreira</p>
        </div>

        <?php if (!empty($cursos) && is_array($cursos)): ?>
        <div class="row g-4">
            <?php foreach ($cursos as $index => $curso): ?>
            <?php if (is_array($curso) && isset($curso['nome'])): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="card h-100 shadow-sm hover-lift">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= safe_html($curso['nome']) ?></h5>
                        <p class="card-text text-muted">
                            <?php 
                            $descricao = $curso['descricao'] ?? 'Curso profissionalizante';
                            echo safe_html(substr($descricao, 0, 120));
                            ?>...
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0">
                        <a href="#interesse" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Tenho Interesse
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Em breve disponibilizaremos informações sobre nossos cursos.
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Professores Section -->
<section id="professores" class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-4 fw-bold">Nossos Professores</h2>
            <p class="lead text-muted">Aprenda com os melhores profissionais do mercado</p>
        </div>

        <?php if (!empty($professores) && is_array($professores)): ?>
        <div class="row g-4">
            <?php foreach ($professores as $index => $professor): ?>
            <?php if (is_array($professor) && isset($professor['nome'])): ?>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= $index * 100 ?>">
                <div class="card text-center shadow-sm hover-lift h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <?php if (isset($professor['foto']) && is_string($professor['foto']) && !empty($professor['foto'])): ?>
                            <img src="<?= safe_html($professor['foto']) ?>" 
                                 alt="<?= safe_html($professor['nome']) ?>"
                                 class="rounded-circle border border-3 border-primary"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="card-title fw-bold">
                            <?= safe_html($professor['nome']) ?>
                        </h5>
                        
                        <?php if (isset($professor['formacao']) && is_string($professor['formacao']) && !empty($professor['formacao'])): ?>
                        <p class="text-primary mb-2">
                            <small><?= safe_html($professor['formacao']) ?></small>
                        </p>
                        <?php elseif (isset($professor['descricao']) && is_string($professor['descricao']) && !empty($professor['descricao'])): ?>
                        <p class="text-muted mb-2">
                            <small><?= safe_html(substr($professor['descricao'], 0, 50)) ?>...</small>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Em breve apresentaremos nossa equipe de professores.
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Alunos Section -->
<section id="alunos" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-4 fw-bold">Alunos Formados</h2>
            <p class="lead text-muted">Conheça alguns de nossos ex-alunos de sucesso</p>
        </div>

        <?php if (!empty($alunosDestaque) && is_array($alunosDestaque)): ?>
        <div class="row g-4">
            <?php foreach ($alunosDestaque as $aluno): ?>
            <?php if (is_array($aluno) && isset($aluno['nome'])): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <?php if (isset($aluno['melhor_aluno']) && $aluno['melhor_aluno'] == 1): ?>
                        <div class="mb-2">
                            <i class="fas fa-crown text-warning fa-2x"></i>
                            <p class="text-warning fw-bold mb-0 small">Melhor Aluno</p>
                        </div>
                        <?php endif; ?>
                        
                        <h6 class="fw-bold"><?= safe_html($aluno['nome']) ?></h6>
                        <p class="text-muted small mb-0"><?= safe_html($aluno['curso_nome'] ?? '') ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Em breve apresentaremos nossos alunos formados.
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Formulário de Interesse -->
<section id="interesse" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-paper-plane fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold">Tenho Interesse!</h2>
                            <p class="text-muted">Preencha o formulário e entraremos em contato</p>
                        </div>

                        <form method="POST" action="/interessados/criar">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?= safe_html($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))) ?>">
                            
                            <!-- Honeypot -->
                            <input type="text" name="website" style="display:none" tabindex="-1">
                            
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
                                    <label class="form-label">WhatsApp</label>
                                    <input type="tel" class="form-control" name="whatsapp" placeholder="(00) 00000-0000">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Curso de Interesse</label>
                                    <select class="form-select" name="curso_interesse">
                                        <option value="">Selecione...</option>
                                        <?php if (is_array($cursos)): ?>
                                        <?php foreach ($cursos as $curso): ?>
                                        <?php if (is_array($curso) && isset($curso['id'], $curso['nome'])): ?>
                                        <option value="<?= (int)$curso['id'] ?>"><?= safe_html($curso['nome']) ?></option>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Mensagem (Opcional)</label>
                                    <textarea class="form-control" name="mensagem" rows="3"></textarea>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar
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
                <p class="mb-0">&copy; <?= date('Y') ?> Planeta Treinamentos. Todos os direitos reservados.</p>
                <div class="mt-2">
                    <a href="/sobre" class="text-white-50 text-decoration-none me-3">
                        <i class="fas fa-info-circle me-1"></i>Sobre Nós
                    </a>
                    <a href="/contato" class="text-white-50 text-decoration-none">
                        <i class="fas fa-envelope me-1"></i>Contato
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="mb-2">
                    <a href="/admin" class="text-white-50 text-decoration-none">
                        <i class="fas fa-lock me-1"></i>Área Administrativa
                    </a>
                </div>
                <div>
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-whatsapp fa-lg"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#!') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>

</body>
</html>