<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Certificados</h1>
        <p class="text-muted">Gerencie os certificados dos alunos</p>
    </div>
    <div>
        <a href="/admin/certificados/gerar" class="btn btn-primary">
            <i class="fas fa-certificate"></i> Gerar Certificados
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Total Alunos</div>
                        <h3 class="mb-0"><?= count($certificados) ?></h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Certificados Emitidos</div>
                        <h3 class="mb-0">
                            <?= count(array_filter($certificados, fn($c) => $c['certificado_emitido'])) ?>
                        </h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Pendentes</div>
                        <h3 class="mb-0">
                            <?= count(array_filter($certificados, fn($c) => !$c['certificado_emitido'])) ?>
                        </h3>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Melhores Alunos</div>
                        <h3 class="mb-0">
                            <?= count(array_filter($certificados, fn($c) => $c['melhor_aluno'])) ?>
                        </h3>
                    </div>
                    <i class="fas fa-star fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Certificados -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="certificadosTable">
                <thead>
                    <tr>
                        <th>Aluno ID</th>
                        <th>Nome</th>
                        <th>Curso</th>
                        <th>Conclusão</th>
                        <th>Status</th>
                        <th>Emitido em</th>
                        <th width="15%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificados)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum aluno cadastrado ainda
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificados as $cert): ?>
                        <tr>
                            <td>
                                <code><?= e($cert['alunoid']) ?></code>
                            </td>
                            <td>
                                <strong><?= e($cert['nome']) ?></strong>
                                <?php if ($cert['melhor_aluno']): ?>
                                    <i class="fas fa-star text-warning ms-1" title="Melhor Aluno"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= e($cert['curso_nome']) ?></td>
                            <td><?= formatDate($cert['data_conclusao']) ?></td>
                            <td>
                                <?php if ($cert['certificado_emitido']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Emitido
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock"></i> Pendente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cert['certificado_emitido']): ?>
                                    <?= formatDateTime($cert['certificado_emitido_em']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cert['certificado_emitido']): ?>
                                    <a href="<?= e($cert['certificado_url']) ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver Certificado">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/certificados/download?aluno_id=<?= $cert['id'] ?>" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button 
                                        onclick="reenviarCertificado(<?= $cert['id'] ?>)" 
                                        class="btn btn-sm btn-outline-info" 
                                        title="Reenviar Email">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="/admin/certificados/gerar" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="aluno_id" value="<?= $cert['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Gerar Certificado">
                                            <i class="fas fa-certificate"></i> Gerar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#certificadosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[5, 'desc']],
        pageLength: 25
    });
});

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
        csrfInput.value = document.querySelector('input[name="_token"]').value;
        
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
