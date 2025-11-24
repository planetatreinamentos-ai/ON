<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Novo Curso</h1>
        <p class="text-muted">Cadastre um novo curso</p>
    </div>
    <a href="/admin/cursos" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/cursos">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Curso *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição *</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                        <small class="text-muted">Descrição breve do curso</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="frase_certificado" class="form-label">Frase do Certificado *</label>
                        <textarea class="form-control" id="frase_certificado" name="frase_certificado" rows="3" required></textarea>
                        <small class="text-muted">Frase que aparecerá no certificado do aluno</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="ordem_exibicao" class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" id="ordem_exibicao" name="ordem_exibicao" value="0" min="0">
                        <small class="text-muted">Menor número aparece primeiro</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">
                                Curso Ativo
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/cursos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Curso
                </button>
            </div>
        </form>
    </div>
</div>
