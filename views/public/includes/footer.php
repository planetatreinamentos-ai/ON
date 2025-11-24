<!-- Footer -->
<footer class="footer bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- Sobre a Empresa -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">
                    <?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?>
                </h5>
                <p class="text-muted">
                    <?= htmlspecialchars($config['descricao_empresa'] ?? 'Formando profissionais de excelência na manutenção de smartphones.') ?>
                </p>
                
                <!-- Social Media -->
                <div class="social-links mt-3">
                    <?php if (!empty($config['instagram'])): ?>
                    <a href="<?= htmlspecialchars($config['instagram']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="text-light me-3"
                       title="Instagram">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($config['facebook'])): ?>
                    <a href="<?= htmlspecialchars($config['facebook']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="text-light me-3"
                       title="Facebook">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($config['youtube'])): ?>
                    <a href="<?= htmlspecialchars($config['youtube']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="text-light me-3"
                       title="YouTube">
                        <i class="fab fa-youtube fa-2x"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($config['linkedin'])): ?>
                    <a href="<?= htmlspecialchars($config['linkedin']) ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="text-light"
                       title="LinkedIn">
                        <i class="fab fa-linkedin fa-2x"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Links Rápidos</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="/" class="text-muted text-decoration-none">
                            <i class="fas fa-angle-right me-2"></i>Início
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/#cursos" class="text-muted text-decoration-none">
                            <i class="fas fa-angle-right me-2"></i>Cursos
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/#professores" class="text-muted text-decoration-none">
                            <i class="fas fa-angle-right me-2"></i>Professores
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/sobre" class="text-muted text-decoration-none">
                            <i class="fas fa-angle-right me-2"></i>Sobre
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/contato" class="text-muted text-decoration-none">
                            <i class="fas fa-angle-right me-2"></i>Contato
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contato -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Contato</h5>
                <ul class="list-unstyled text-muted">
                    <?php if (!empty($config['endereco'])): ?>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?= nl2br(htmlspecialchars($config['endereco'])) ?>
                    </li>
                    <?php endif; ?>

                    <?php if (!empty($config['telefone'])): ?>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:<?= preg_replace('/\D/', '', $config['telefone']) ?>" 
                           class="text-muted text-decoration-none">
                            <?= htmlspecialchars($config['telefone']) ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (!empty($config['whatsapp'])): ?>
                    <li class="mb-2">
                        <i class="fab fa-whatsapp me-2"></i>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $config['whatsapp']) ?>" 
                           class="text-muted text-decoration-none"
                           target="_blank">
                            <?= htmlspecialchars($config['whatsapp']) ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (!empty($config['email_contato'])): ?>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?= htmlspecialchars($config['email_contato']) ?>" 
                           class="text-muted text-decoration-none">
                            <?= htmlspecialchars($config['email_contato']) ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Horário de Funcionamento -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Horário de Funcionamento</h5>
                <ul class="list-unstyled text-muted">
                    <?php if (!empty($config['horario_funcionamento'])): ?>
                        <?= nl2br(htmlspecialchars($config['horario_funcionamento'])) ?>
                    <?php else: ?>
                        <li>Segunda a Sexta</li>
                        <li>08:00 - 18:00</li>
                        <li class="mt-2">Sábado</li>
                        <li>08:00 - 12:00</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <hr class="my-4 bg-secondary">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted mb-0">
                    © <?= date('Y') ?> 
                    <strong><?= htmlspecialchars($config['nome_empresa'] ?? 'Planeta Treinamentos') ?></strong>. 
                    Todos os direitos reservados.
                </p>
                <?php if (!empty($config['cnpj'])): ?>
                <p class="text-muted small mb-0">
                    CNPJ: <?= htmlspecialchars($config['cnpj']) ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-muted mb-0">
                    <a href="/admin" class="text-muted text-decoration-none me-3">
                        <i class="fas fa-lock me-1"></i>Área Restrita
                    </a>
                    <a href="#" class="text-muted text-decoration-none" id="backToTop">
                        <i class="fas fa-arrow-up me-1"></i>Voltar ao Topo
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>