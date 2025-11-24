<?php
/**
 * Página Pública do Professor
 * URL: /professor?id={professorid}
 */

$currentPage = 'professores';
?>

<?php include __DIR__ . '/includes/meta-tags.php'; ?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Professor Header Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center" data-aos="fade-right">
                <?php if (!empty($professor['foto'])): ?>
                <img src="<?= htmlspecialchars($professor['foto']) ?>" 
                     alt="<?= htmlspecialchars($professor['nome']) ?>"
                     class="rounded-circle shadow-lg border border-5 border-white img-fluid"
                     style="max-width: 300px; width: 100%;">
                <?php else: ?>
                <div class="rounded-circle bg-secondary mx-auto d-flex align-items-center justify-content-center shadow-lg" 
                     style="width: 300px; height: 300px;">
                    <i class="fas fa-user fa-8x text-white"></i>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-8" data-aos="fade-left">
                <h1 class="display-4 fw-bold mb-2"><?= htmlspecialchars($professor['nome']) ?></h1>
                
                <?php if (!empty($professor['titulo'])): ?>
                <p class="lead text-primary mb-3">
                    <?= htmlspecialchars($professor['titulo']) ?>
                </p>
                <?php endif; ?>

                <!-- Estatísticas -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h3 class="text-primary fw-bold mb-0"><?= $totalAlunos ?? 0 ?></h3>
                                <p class="text-muted mb-0 small">Alunos Formados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h3 class="text-success fw-bold mb-0"><?= $mediaNotas ?? 0 ?></h3>
                                <p class="text-muted mb-0 small">Nota Média</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h3 class="text-warning fw-bold mb-0">
                                    <i class="fas fa-star"></i>
                                </h3>
                                <p class="text-muted mb-0 small">Professor Certificado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão de Ação -->
                <div class="d-flex gap-2">
                    <a href="/#interesse" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>
                        Quero Fazer um Curso
                    </a>
                    <a href="/" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i>
                        Voltar ao Início
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre o Professor -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm" data-aos="fade-up">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Sobre o Professor
                        </h3>
                        
                        <?php if (!empty($professor['descricao'])): ?>
                        <div class="text-muted" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($professor['descricao'])) ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted fst-italic">
                            Informações sobre o professor não disponíveis no momento.
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formação Acadêmica -->
<?php if (!empty($professor['formacao'])): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm" data-aos="fade-up">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">
                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                            Formação Acadêmica
                        </h3>
                        <div class="text-muted" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($professor['formacao'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Certificados do Professor -->
<?php if (!empty($professor['certificados_fotos'])): ?>
<?php
$certificados = explode(',', $professor['certificados_fotos']);
if (!empty($certificados) && $certificados[0] !== ''):
?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold text-center mb-5" data-aos="fade-up">
                    <i class="fas fa-certificate text-primary me-2"></i>
                    Certificações
                </h3>

                <!-- Swiper Certificados -->
                <div class="swiper certificadosSwiper" data-aos="fade-up">
                    <div class="swiper-wrapper">
                        <?php foreach ($certificados as $certificado): ?>
                        <?php if (!empty(trim($certificado))): ?>
                        <div class="swiper-slide">
                            <a href="<?= htmlspecialchars(trim($certificado)) ?>" 
                               class="glightbox"
                               data-gallery="certificados">
                                <img src="<?= htmlspecialchars(trim($certificado)) ?>" 
                                     alt="Certificado"
                                     class="img-fluid rounded shadow-sm hover-zoom"
                                     style="max-height: 300px; width: 100%; object-fit: cover;">
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="swiper-pagination mt-4"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

                <script>
                    // Initialize Certificados Swiper
                    const certificadosSwiper = new Swiper('.certificadosSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 30,
                        loop: true,
                        autoplay: {
                            delay: 3000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        breakpoints: {
                            768: {
                                slidesPerView: 2,
                            },
                            1024: {
                                slidesPerView: 3,
                            },
                        }
                    });
                    
                    // Initialize GLightbox
                    const lightbox = GLightbox({
                        selector: '.glightbox'
                    });
                </script>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<!-- Melhor Aluno do Professor -->
<?php if (!empty($melhorAluno)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center" data-aos="zoom-in">
                    <div class="card-body p-4">
                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                        <h4 class="fw-bold mb-3">Destaque do Professor</h4>
                        
                        <?php if (!empty($melhorAluno['foto'])): ?>
                        <img src="<?= htmlspecialchars($melhorAluno['foto']) ?>" 
                             alt="<?= htmlspecialchars($melhorAluno['nome']) ?>"
                             class="rounded-circle mb-3"
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <h5 class="fw-bold"><?= htmlspecialchars($melhorAluno['nome']) ?></h5>
                        <p class="text-muted">
                            <?= htmlspecialchars($melhorAluno['curso_nome'] ?? '') ?>
                        </p>
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-star me-1"></i>
                                Nota: <?= number_format($melhorAluno['nota'], 1) ?>
                            </span>
                        </div>
                        <a href="/verificar/<?= htmlspecialchars($melhorAluno['alunoid']) ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-certificate me-1"></i>
                            Ver Certificado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="fw-bold mb-3">Gostaria de aprender com <?= htmlspecialchars($professor['nome']) ?>?</h2>
        <p class="lead mb-4">Inscreva-se agora e comece sua jornada profissional!</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="/#interesse" class="btn btn-light btn-lg">
                <i class="fas fa-paper-plane me-2"></i>
                Tenho Interesse
            </a>
            <?php if (!empty($config['whatsapp'])): ?>
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $config['whatsapp']) ?>?text=Olá! Gostaria de informações sobre os cursos com <?= urlencode($professor['nome']) ?>" 
               class="btn btn-success btn-lg"
               target="_blank">
                <i class="fab fa-whatsapp me-2"></i>
                WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<?php include __DIR__ . '/includes/scripts.php'; ?>