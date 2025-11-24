<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Editar Aluno</h1>
        <p class="text-muted">Edite as informações do aluno</p>
    </div>
    <a href="/admin/alunos" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/admin/alunos/<?= $aluno['id'] ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">Dados Pessoais</h5>
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="255" value="<?= e($aluno['nome']) ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= e($aluno['email']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" data-mask="phone" value="<?= e($aluno['whatsapp']) ?>">
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
                                        <option value="<?= $curso['id'] ?>" <?= $curso['id'] == $aluno['curso_id'] ? 'selected' : '' ?>>
                                            <?= e($curso['nome']) ?>
                                        </option>
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
                                        <option value="<?= $professor['id'] ?>" <?= $professor['id'] == $aluno['professor_id'] ? 'selected' : '' ?>>
                                            <?= e($professor['nome']) ?>
                                        </option>
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
                                        <option value="<?= $ch['id'] ?>" <?= $ch['id'] == $aluno['carga_horaria_id'] ? 'selected' : '' ?>>
                                            <?= $ch['horas'] ?>h
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="data_inicio" class="form-label">Data de Início *</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" required value="<?= e($aluno['data_inicio']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="data_conclusao" class="form-label">Data de Conclusão *</label>
                                <input type="date" class="form-control" id="data_conclusao" name="data_conclusao" required value="<?= e($aluno['data_fim']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nota" class="form-label">Nota Final</label>
                                <input type="number" class="form-control" id="nota" name="nota" min="0" max="10" step="0.1" value="<?= e($aluno['nota']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="melhor_aluno" name="melhor_aluno" <?= $aluno['melhor_aluno'] ? 'checked' : '' ?>>
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
                        <label class="form-label">Aluno ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?= e($aluno['alunoid']) ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('<?= e($aluno['alunoid']) ?>')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="foto" class="form-label">Nova Foto</label>
                        <?php if ($aluno['foto_principal']): ?>
                            <img src="<?= url('uploads/' . $aluno['foto_principal']) ?>" class="img-fluid mb-2 rounded" style="max-height: 300px;">
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" data-preview="fotoPreview">
                        <small class="text-muted">JPG, PNG, WEBP - máx 5MB</small>
                        <img id="fotoPreview" src="" class="img-fluid mt-3 rounded" style="display: none; max-height: 300px;">
                    </div>
                    
                    <?php if ($aluno['certificado_path']): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Certificado Emitido</strong><br>
                            <small><?= formatDateTime($aluno['certificado_emitido_em']) ?></small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <small>Certificado ainda não foi emitido</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="/admin/alunos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar Aluno
                </button>
            </div>
        </form>
    </div>
</div>
