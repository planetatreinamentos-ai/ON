<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Editar Professor</h1>
        <p class="text-muted">Edite as informações do professor</p>
    </div>
    <a href="/admin/professores" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/professores/<?= $professor['id'] ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="255" value="<?= e($professor['nome']) ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="professorid" class="form-label">ID do Professor * (slug)</label>
                                <input type="text" class="form-control" id="professorid" name="professorid" required value="<?= e($professor['professorid']) ?>">
                                <small class="text-muted">Usado na URL pública</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= e($professor['email']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" data-mask="phone" value="<?= e($professor['whatsapp']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= e($professor['descricao']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="formacao" class="form-label">Formação</label>
                        <textarea class="form-control" id="formacao" name="formacao" rows="2"><?= e($professor['formacao']) ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto do Professor</label>
                        <?php if ($professor['foto']): ?>
                            <img src="<?= url('uploads/' . $professor['foto']) ?>" class="img-fluid mb-2 rounded" style="max-height: 200px;">
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" data-preview="fotoPreview">
                        <img id="fotoPreview" src="" class="img-fluid mt-2 rounded" style="display: none; max-height: 200px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="assinatura" class="form-label">Assinatura (imagem)</label>
                        <?php if ($professor['assinatura']): ?>
                            <img src="<?= url('uploads/' . $professor['assinatura']) ?>" class="img-fluid mb-2 rounded" style="max-height: 100px;">
                        <?php endif; ?>
                        <input type="file" class="form-control" id="assinatura" name="assinatura" accept="image/*" data-preview="assinaturaPreview">
                        <img id="assinaturaPreview" src="" class="img-fluid mt-2 rounded" style="display: none; max-height: 100px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ordem_exibicao" class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" id="ordem_exibicao" name="ordem_exibicao" value="<?= e($professor['ordem_exibicao']) ?>" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" <?= $professor['status'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="status">Professor Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/professores" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Professor
                </button>
            </div>
        </form>
    </div>
</div>
