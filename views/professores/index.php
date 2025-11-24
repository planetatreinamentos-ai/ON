<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Professores</h1>
        <p class="text-muted">Gerencie os professores</p>
    </div>
    <a href="/admin/professores/criar" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Professor
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="professoresTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Professor ID</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($professores as $professor): ?>
                    <tr>
                        <td>
                            <?php if ($professor['foto']): ?>
                                <img src="<?= url('uploads/' . $professor['foto']) ?>" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($professor['nome']) ?></strong></td>
                        <td><code><?= e($professor['professorid']) ?></code></td>
                        <td><?= e($professor['email']) ?></td>
                        <td><?= formatPhone($professor['whatsapp']) ?></td>
                        <td>
                            <?php if ($professor['status']): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/professores/<?= $professor['id'] ?>/editar" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteProfessor(<?= $professor['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Excluir">
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
$(document).ready(function() {
    $('#professoresTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        order: [[1, 'asc']]
    });
});

async function deleteProfessor(id) {
    await Admin.confirmDelete(`/admin/professores/${id}`);
}
</script>
