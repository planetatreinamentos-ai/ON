<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Configurações do Sistema</h1>
        <p class="text-muted">Personalize as informações da sua empresa (White-Label)</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#empresa" role="tab">
                    <i class="fas fa-building"></i> Empresa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#social" role="tab">
                    <i class="fas fa-share-alt"></i> Redes Sociais
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tema" role="tab">
                    <i class="fas fa-palette"></i> Tema
                </a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <!-- ABA EMPRESA -->
            <div class="tab-pane fade show active" id="empresa" role="tabpanel">
                <form method="POST" action="/admin/configuracoes">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa_nome" class="form-label">Nome da Empresa *</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="empresa_nome" 
                                    name="empresa_nome" 
                                    value="<?= e($empresa['empresa_nome']['valor'] ?? '') ?>" 
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa_cnpj" class="form-label">CNPJ</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="empresa_cnpj" 
                                    name="empresa_cnpj" 
                                    value="<?= e($empresa['empresa_cnpj']['valor'] ?? '') ?>"
                                    placeholder="00.000.000/0000-00"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa_telefone" class="form-label">Telefone</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="empresa_telefone" 
                                    name="empresa_telefone" 
                                    value="<?= e($empresa['empresa_telefone']['valor'] ?? '') ?>"
                                    data-mask="phone"
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa_email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="empresa_email" 
                                    name="empresa_email" 
                                    value="<?= e($empresa['empresa_email']['valor'] ?? '') ?>"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="empresa_endereco" class="form-label">Endereço</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="empresa_endereco" 
                            name="empresa_endereco" 
                            value="<?= e($empresa['empresa_endereco']['valor'] ?? '') ?>"
                        >
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
                
                <!-- Upload de Logo -->
                <hr class="my-4">
                <h5 class="mb-3">Logo da Empresa</h5>
                
                <?php if (!empty($empresa['empresa_logo']['valor'])): ?>
                    <div class="mb-3">
                        <img src="<?= url('uploads/' . $empresa['empresa_logo']['valor']) ?>" alt="Logo" class="img-fluid" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/admin/configuracoes/logo" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="logo" class="form-label">Enviar Nova Logo</label>
                                <input 
                                    type="file" 
                                    class="form-control" 
                                    id="logo" 
                                    name="logo" 
                                    accept="image/*"
                                    data-preview="logoPreview"
                                >
                                <small class="text-muted">PNG ou JPG transparente - recomendado 300x100px</small>
                                <img id="logoPreview" src="" class="img-fluid mt-2" style="display: none; max-height: 100px;">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-3">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- ABA REDES SOCIAIS -->
            <div class="tab-pane fade" id="social" role="tabpanel">
                <form method="POST" action="/admin/configuracoes">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_instagram" class="form-label">
                                    <i class="fab fa-instagram"></i> Instagram
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="social_instagram" 
                                    name="social_instagram" 
                                    value="<?= e($social['social_instagram']['valor'] ?? '') ?>"
                                    placeholder="@seu_perfil"
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="social_facebook" class="form-label">
                                    <i class="fab fa-facebook"></i> Facebook
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="social_facebook" 
                                    name="social_facebook" 
                                    value="<?= e($social['social_facebook']['valor'] ?? '') ?>"
                                    placeholder="https://facebook.com/..."
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="social_whatsapp" class="form-label">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="social_whatsapp" 
                            name="social_whatsapp" 
                            value="<?= e($social['social_whatsapp']['valor'] ?? '') ?>"
                            placeholder="+55 91 98866-6900"
                            data-mask="phone"
                        >
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- ABA TEMA -->
            <div class="tab-pane fade" id="tema" role="tabpanel">
                <form method="POST" action="/admin/configuracoes">
                    <?= csrf_field() ?>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Cores do Sistema:</strong> Estas cores serão aplicadas em todo o site público.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_cor_primaria" class="form-label">Cor Primária</label>
                                <input 
                                    type="color" 
                                    class="form-control form-control-color" 
                                    id="tema_cor_primaria" 
                                    name="tema_cor_primaria" 
                                    value="<?= e($tema['tema_cor_primaria']['valor'] ?? '#1a365d') ?>"
                                >
                                <small class="text-muted">Cor principal do site (botões, links, etc)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_cor_secundaria" class="form-label">Cor Secundária</label>
                                <input 
                                    type="color" 
                                    class="form-control form-control-color" 
                                    id="tema_cor_secundaria" 
                                    name="tema_cor_secundaria" 
                                    value="<?= e($tema['tema_cor_secundaria']['valor'] ?? '#f59e0b') ?>"
                                >
                                <small class="text-muted">Cor secundária (destaques, ícones, etc)</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
