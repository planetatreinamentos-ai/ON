<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Novo Aluno</h1>
        <p class="text-muted">Cadastre um novo aluno</p>
    </div>
    <a href="/admin/alunos" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/alunos" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">Dados Pessoais</h5>
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="255">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" data-mask="phone" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-3">Dados do Curso</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="curso_id" class="form-label">Curso *</label>
                                <select class="form-select" id="curso_id" name="curso_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?= $curso['id'] ?>"><?= e($curso['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="professor_id" class="form-label">Professor *</label>
                                <select class="form-select" id="professor_id" name="professor_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($professores as $professor): ?>
                                        <option value="<?= $professor['id'] ?>"><?= e($professor['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="carga_horaria_id" class="form-label">Carga Horária *</label>
                                <select class="form-select" id="carga_horaria_id" name="carga_horaria_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($cargasHorarias as $ch): ?>
                                        <option value="<?= $ch['id'] ?>"><?= $ch['horas'] ?>h</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="data_inicio" class="form-label">Data de Início *</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="data_conclusao" class="form-label">Data de Conclusão *</label>
                                <input type="date" class="form-control" id="data_conclusao" name="data_conclusao" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nota" class="form-label">Nota Final</label>
                                <input type="number" class="form-control" id="nota" name="nota" min="0" max="10" step="0.1" placeholder="0.0 a 10.0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="melhor_aluno" name="melhor_aluno">
                                    <label class="form-check-label" for="melhor_aluno">
                                        <i class="fas fa-star text-warning"></i> Marcar como Melhor Aluno
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h5 class="mb-3">Foto do Aluno</h5>
                    
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" data-preview="fotoPreview">
                        <small class="text-muted">JPG, PNG, WEBP - máx 5MB</small>
                        <img id="fotoPreview" src="" class="img-fluid mt-3 rounded" style="display: none; max-height: 300px;">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>O <strong>Aluno ID</strong> será gerado automaticamente após salvar.</small>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/alunos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cadastrar Aluno
                </button>
            </div>
        </form>
    </div>
</div>
