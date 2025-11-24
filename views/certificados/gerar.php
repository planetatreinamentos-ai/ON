<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Gerar Certificados</h1>
        <p class="text-muted">Selecione os alunos para gerar certificados</p>
    </div>
    <a href="/admin/certificados" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<?php if (empty($alunos)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Nenhum aluno pendente!</strong><br>
        Todos os alunos que concluíram o curso já possuem certificado emitido.
    </div>
    <a href="/admin/certificados" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Voltar para Certificados
    </a>
<?php else: ?>

<form method="POST" action="/admin/certificados/gerar-lote" id="formGerarLote">
    <?= csrf_field() ?>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Alunos Pendentes de Certificação</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarTodos()">
                    <i class="fas fa-check-square"></i> Selecionar Todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="desmarcarTodos()">
                    <i class="fas fa-square"></i> Desmarcar Todos
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="alunosTable">
                    <thead>
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            </th>
                            <th>Aluno ID</th>
                            <th>Nome</th>
                            <th>Curso</th>
                            <th>Professor</th>
                            <th>CH</th>
                            <th>Conclusão</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="aluno_ids[]" 
                                    value="<?= $aluno['id'] ?>" 
                                    class="aluno-checkbox"
                                >
                            </td>
                            <td><code><?= e($aluno['alunoid']) ?></code></td>
                            <td>
                                <strong><?= e($aluno['nome']) ?></strong>
                                <?php if ($aluno['melhor_aluno']): ?>
                                    <i class="fas fa-star text-warning ms-1" title="Melhor Aluno"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= e($aluno['curso_nome']) ?></td>
                            <td><?= e($aluno['professor_nome']) ?></td>
                            <td><?= $aluno['carga_horaria'] ?>h</td>
                            <td><?= formatDate($aluno['data_conclusao']) ?></td>
                            <td>
                                <?php if ($aluno['nota']): ?>
                                    <span class="badge bg-primary"><?= number_format($aluno['nota'], 1) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">
                        <span id="selectedCount">0</span> aluno(s) selecionado(s)
                    </span>
                </div>
                <div>
                    <a href="/admin/certificados" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="btnGerar" disabled>
                        <i class="fas fa-certificate"></i> Gerar Certificados Selecionados
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php endif; ?>

<script>
$(document).ready(function() {
    $('#alunosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[6, 'asc']],
        pageLength: 50
    });
    
    // Atualiza contador ao selecionar
    $('.aluno-checkbox').on('change', updateCount);
    updateCount();
});

function toggleAll(checkbox) {
    $('.aluno-checkbox').prop('checked', checkbox.checked);
    updateCount();
}

function selecionarTodos() {
    $('.aluno-checkbox').prop('checked', true);
    $('#selectAll').prop('checked', true);
    updateCount();
}

function desmarcarTodos() {
    $('.aluno-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    updateCount();
}

function updateCount() {
    const count = $('.aluno-checkbox:checked').length;
    $('#selectedCount').text(count);
    $('#btnGerar').prop('disabled', count === 0);
}

$('#formGerarLote').on('submit', async function(e) {
    e.preventDefault();
    
    const count = $('.aluno-checkbox:checked').length;
    
    if (count === 0) {
        Swal.fire('Atenção', 'Selecione pelo menos um aluno', 'warning');
        return;
    }
    
    const result = await Swal.fire({
        title: 'Gerar Certificados?',
        html: `Você está prestes a gerar <strong>${count} certificado(s)</strong>.<br>
               Os certificados serão gerados e enviados por email.<br><br>
               Este processo pode levar alguns minutos.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, gerar!',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    });
    
    if (result.isConfirmed) {
        // Mostra loading
        Swal.fire({
            title: 'Gerando Certificados...',
            html: 'Por favor aguarde, este processo pode levar alguns minutos.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submete o formulário
        this.submit();
    }
});
</script>
