<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="/">
            <?php if (!empty($config['logo_url'])): ?>
                <img src="<?= htmlspecialchars($config['logo_url']) ?>" 
                     alt="<?= htmlspecialchars($config['nome_empresa']) ?>" 
                     height="50"
                     class="d-inline-block align-text-top">
            <?php else: ?>
                <span class="fw-bold fs-4"><?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?></span>
            <?php endif; ?>
        </a>

        <!-- Toggle Button (Mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>" 
                       href="/">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'cursos' ? 'active' : '' ?>" 
                       href="/#cursos">
                        <i class="fas fa-graduation-cap"></i> Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'professores' ? 'active' : '' ?>" 
                       href="/#professores">
                        <i class="fas fa-chalkboard-teacher"></i> Professores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'alunos' ? 'active' : '' ?>" 
                       href="/#alunos">
                        <i class="fas fa-users"></i> Alunos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'sobre' ? 'active' : '' ?>" 
                       href="/sobre">
                        <i class="fas fa-info-circle"></i> Sobre
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'contato' ? 'active' : '' ?>" 
                       href="/contato">
                        <i class="fas fa-envelope"></i> Contato
                    </a>
                </li>
            </ul>

            <!-- Social Links & WhatsApp -->
            <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
                <?php if (!empty($config['whatsapp'])): ?>
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $config['whatsapp']) ?>?text=Olá! Gostaria de mais informações sobre os cursos." 
                   class="btn btn-success btn-sm me-2"
                   target="_blank"
                   rel="noopener">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php endif; ?>

                <!-- Social Icons -->
                <div class="social-icons">
                    <?php if (!empty($config['instagram'])): ?>
                    <a href="<?= htmlspecialchars($config['instagram']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       title="Instagram"
                       class="text-decoration-none me-2">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($config['facebook'])): ?>
                    <a href="<?= htmlspecialchars($config['facebook']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       title="Facebook"
                       class="text-decoration-none me-2">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($config['youtube'])): ?>
                    <a href="<?= htmlspecialchars($config['youtube']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       title="YouTube"
                       class="text-decoration-none">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- WhatsApp Float Button (Mobile) -->
<?php if (!empty($config['whatsapp'])): ?>
<a href="https://wa.me/<?= preg_replace('/\D/', '', $config['whatsapp']) ?>?text=Olá! Gostaria de mais informações sobre os cursos." 
   class="whatsapp-float"
   target="_blank"
   rel="noopener"
   title="Fale conosco no WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>