<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Novo Professor</h1>
        <p class="text-muted">Cadastre um novo professor</p>
    </div>
    <a href="/admin/professores" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/professores" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="255">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="professorid" class="form-label">ID do Professor * (slug)</label>
                                <input type="text" class="form-control" id="professorid" name="professorid" required placeholder="ex: adson-quaresma">
                                <small class="text-muted">Usado na URL pública</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" data-mask="phone" placeholder="(00) 00000-0000">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="formacao" class="form-label">Formação</label>
                        <textarea class="form-control" id="formacao" name="formacao" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto do Professor</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" data-preview="fotoPreview">
                        <img id="fotoPreview" src="" class="img-fluid mt-2 rounded" style="display: none; max-height: 200px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="assinatura" class="form-label">Assinatura (imagem)</label>
                        <input type="file" class="form-control" id="assinatura" name="assinatura" accept="image/*" data-preview="assinaturaPreview">
                        <img id="assinaturaPreview" src="" class="img-fluid mt-2 rounded" style="display: none; max-height: 100px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ordem_exibicao" class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" id="ordem_exibicao" name="ordem_exibicao" value="0" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Professor Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/professores" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Professor
                </button>
            </div>
        </form>
    </div>
</div>
