<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Preview do Certificado</h1>
        <p class="text-muted">Aluno: <?= e($aluno['nome']) ?></p>
    </div>
    <a href="/admin/certificados" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Visualização do Certificado</h5>
            </div>
            <div class="card-body text-center bg-light">
                <?php if ($aluno['certificado_emitido'] && $aluno['certificado_url']): ?>
                    <img 
                        src="<?= e($aluno['certificado_url']) ?>" 
                        class="img-fluid border"
                        alt="Certificado de <?= e($aluno['nome']) ?>"
                    >
                <?php else: ?>
                    <div class="py-5">
                        <i class="fas fa-certificate fa-5x text-muted mb-3"></i>
                        <h4>Certificado Ainda Não Gerado</h4>
                        <p class="text-muted">Clique no botão abaixo para gerar o certificado</p>
                        
                        <form method="POST" action="/admin/certificados/gerar" class="mt-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-certificate"></i> Gerar Certificado
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações do Aluno</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Aluno ID:</dt>
                    <dd class="col-sm-8"><code><?= e($aluno['alunoid']) ?></code></dd>
                    
                    <dt class="col-sm-4">Nome:</dt>
                    <dd class="col-sm-8"><?= e($aluno['nome']) ?></dd>
                    
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8"><?= e($aluno['email']) ?></dd>
                    
                    <dt class="col-sm-4">Curso:</dt>
                    <dd class="col-sm-8"><?= e($aluno['curso_nome']) ?></dd>
                    
                    <dt class="col-sm-4">Professor:</dt>
                    <dd class="col-sm-8"><?= e($aluno['professor_nome']) ?></dd>
                    
                    <dt class="col-sm-4">Carga Horária:</dt>
                    <dd class="col-sm-8"><?= $aluno['carga_horaria'] ?> horas</dd>
                    
                    <dt class="col-sm-4">Início:</dt>
                    <dd class="col-sm-8"><?= formatDate($aluno['data_inicio']) ?></dd>
                    
                    <dt class="col-sm-4">Conclusão:</dt>
                    <dd class="col-sm-8"><?= formatDate($aluno['data_conclusao']) ?></dd>
                    
                    <?php if ($aluno['nota']): ?>
                    <dt class="col-sm-4">Nota:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-primary"><?= number_format($aluno['nota'], 1) ?></span>
                    </dd>
                    <?php endif; ?>
                    
                    <?php if ($aluno['melhor_aluno']): ?>
                    <dt class="col-sm-4">Destaque:</dt>
                    <dd class="col-sm-8">
                        <i class="fas fa-star text-warning"></i> Melhor Aluno
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
        
        <?php if ($aluno['certificado_emitido']): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Status do Certificado</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i>
                    <strong>Certificado Emitido</strong><br>
                    <small><?= formatDateTime($aluno['certificado_emitido_em']) ?></small>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?= e($aluno['certificado_url']) ?>" 
                       target="_blank" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-eye"></i> Ver Certificado
                    </a>
                    
                    <a href="/admin/certificados/download?aluno_id=<?= $aluno['id'] ?>" 
                       class="btn btn-outline-success">
                        <i class="fas fa-download"></i> Download
                    </a>
                    
                    <button 
                        onclick="reenviarCertificado(<?= $aluno['id'] ?>)" 
                        class="btn btn-outline-info">
                        <i class="fas fa-envelope"></i> Reenviar Email
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function reenviarCertificado(alunoId) {
    const result = await Swal.fire({
        title: 'Reenviar Certificado?',
        text: 'O certificado será reenviado por email para o aluno.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, reenviar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/certificados/reenviar';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '<?= csrf_token() ?>';
        
        const alunoInput = document.createElement('input');
        alunoInput.type = 'hidden';
        alunoInput.name = 'aluno_id';
        alunoInput.value = alunoId;
        
        form.appendChild(csrfInput);
        form.appendChild(alunoInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
