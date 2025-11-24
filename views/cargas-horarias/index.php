<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Cargas Horárias</h1>
        <p class="text-muted">Gerencie as cargas horárias disponíveis</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Nova Carga Horária</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/cargas-horarias">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="horas" class="form-label">Horas *</label>
                        <input type="number" class="form-control" id="horas" name="horas" required min="1" placeholder="Ex: 60">
                        <small class="text-muted">Quantidade de horas do curso</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Ativo</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Cargas Horárias Cadastradas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="20%">ID</th>
                                <th width="30%">Horas</th>
                                <th width="25%">Status</th>
                                <th width="25%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cargasHorarias)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Nenhuma carga horária cadastrada
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cargasHorarias as $ch): ?>
                                <tr>
                                    <td><strong><?= $ch['id'] ?></strong></td>
                                    <td>
                                        <span class="badge bg-primary" style="font-size: 14px;">
                                            <?= $ch['horas'] ?> horas
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($ch['status']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button 
                                            onclick="editCargaHoraria(<?= $ch['id'] ?>, <?= $ch['horas'] ?>, <?= $ch['status'] ?>)" 
                                            class="btn btn-sm btn-outline-primary" 
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            onclick="deleteCargaHoraria(<?= $ch['id'] ?>)" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Excluir"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Carga Horária</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_horas" class="form-label">Horas *</label>
                        <input type="number" class="form-control" id="edit_horas" name="horas" required min="1">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_status" name="status">
                            <label class="form-check-label" for="edit_status">Ativo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCargaHoraria(id, horas, status) {
    document.getElementById('edit_horas').value = horas;
    document.getElementById('edit_status').checked = status == 1;
    document.getElementById('editForm').action = `/admin/cargas-horarias/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

async function deleteCargaHoraria(id) {
    await Admin.confirmDelete(
        `/admin/cargas-horarias/${id}`,
        'Tem certeza que deseja excluir esta carga horária? Esta ação não pode ser desfeita se houver alunos cadastrados com ela.'
    );
}
</script>
