<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Cursos</h1>
        <p class="text-muted">Gerencie os cursos oferecidos</p>
    </div>
    <a href="/admin/cursos/criar" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Curso
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="cursosTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ordem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?= e($curso['id']) ?></td>
                        <td><strong><?= e($curso['nome']) ?></strong></td>
                        <td><?= e(substr($curso['descricao'], 0, 80)) ?>...</td>
                        <td><?= e($curso['ordem_exibicao']) ?></td>
                        <td>
                            <?php if ($curso['status']): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/cursos/<?= $curso['id'] ?>/editar" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteCurso(<?= $curso['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// DataTable
$(document).ready(function() {
    $('#cursosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        order: [[0, 'desc']]
    });
});

// Função de deletar
async function deleteCurso(id) {
    await Admin.confirmDelete(`/admin/cursos/${id}`);
}
</script>
