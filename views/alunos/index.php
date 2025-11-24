<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Alunos</h1>
        <p class="text-muted">Gerencie os alunos cadastrados</p>
    </div>
    <a href="/admin/alunos/criar" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Aluno
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <!-- 
  FILTROS DE ALUNOS
  Cole este código em: views/alunos/index.php
  ANTES da tabela de alunos
-->

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel"></i> Filtros de Pesquisa
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="/admin/alunos" class="row g-3">
            
            <!-- Busca por texto -->
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" 
                       name="q" 
                       class="form-control" 
                       placeholder="Nome, email ou ID do aluno"
                       value="<?= htmlspecialchars($busca_atual ?? '') ?>">
                <small class="text-muted">Busca em nome, email e ID</small>
            </div>

            <!-- Filtro por status -->
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="filtro" class="form-select">
                    <option value="todos" <?= ($filtro_atual ?? 'todos') === 'todos' ? 'selected' : '' ?>>
                        Todos
                    </option>
                    <option value="ativos" <?= ($filtro_atual ?? '') === 'ativos' ? 'selected' : '' ?>>
                        ✅ Ativos
                    </option>
                    <option value="concluidos" <?= ($filtro_atual ?? '') === 'concluidos' ? 'selected' : '' ?>>
                        ✓ Concluídos
                    </option>
                    <option value="cancelados" <?= ($filtro_atual ?? '') === 'cancelados' ? 'selected' : '' ?>>
                        ✗ Cancelados
                    </option>
                    <option value="sem-certificado" <?= ($filtro_atual ?? '') === 'sem-certificado' ? 'selected' : '' ?>>
                        📄 Sem Certificado
                    </option>
                    <option value="com-certificado" <?= ($filtro_atual ?? '') === 'com-certificado' ? 'selected' : '' ?>>
                        🎓 Com Certificado
                    </option>
                    <option value="melhores" <?= ($filtro_atual ?? '') === 'melhores' ? 'selected' : '' ?>>
                        ⭐ Melhores Alunos
                    </option>
                </select>
            </div>

            <!-- Filtro por curso -->
            <div class="col-md-2">
                <label class="form-label">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">Todos os cursos</option>
                    <?php if (!empty($cursos)): ?>
                        <?php foreach ($cursos as $curso): ?>
                        <option value="<?= $curso['id'] ?>" 
                                <?= ($curso_atual ?? '') == $curso['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($curso['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Filtro por professor -->
            <div class="col-md-2">
                <label class="form-label">Professor</label>
                <select name="professor_id" class="form-select">
                    <option value="">Todos os professores</option>
                    <?php if (!empty($professores)): ?>
                        <?php foreach ($professores as $professor): ?>
                        <option value="<?= $professor['id'] ?>" 
                                <?= ($professor_atual ?? '') == $professor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($professor['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Ordenação -->
            <div class="col-md-2">
                <label class="form-label">Ordenar por</label>
                <select name="ordem" class="form-select">
                    <option value="nome" <?= ($ordem_atual ?? 'nome') === 'nome' ? 'selected' : '' ?>>
                        Nome (A-Z)
                    </option>
                    <option value="recente" <?= ($ordem_atual ?? '') === 'recente' ? 'selected' : '' ?>>
                        Mais Recentes
                    </option>
                    <option value="antigo" <?= ($ordem_atual ?? '') === 'antigo' ? 'selected' : '' ?>>
                        Mais Antigos
                    </option>
                </select>
            </div>

            <!-- Botões -->
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>

            <!-- Botão Limpar Filtros -->
            <?php if (!empty($busca_atual) || ($filtro_atual ?? 'todos') !== 'todos' || !empty($curso_atual) || !empty($professor_atual)): ?>
            <div class="col-12">
                <a href="/admin/alunos" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Limpar Filtros
                </a>
                <span class="text-muted ms-2">
                    <small>Filtros ativos</small>
                </span>
            </div>
            <?php endif; ?>

        </form>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-people-fill"></i> Alunos Ativos
                </h6>
                <h2 class="mb-0"><?= $stats['total_ativos'] ?? 0 ?></h2>
                <small>Status: Ativo</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-check-circle-fill"></i> Concluídos
                </h6>
                <h2 class="mb-0"><?= $stats['total_concluidos'] ?? 0 ?></h2>
                <small>Curso finalizado</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-file-earmark-text"></i> Sem Certificado
                </h6>
                <h2 class="mb-0"><?= $stats['total_sem_certificado'] ?? 0 ?></h2>
                <small>Aguardando emissão</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-star-fill"></i> Melhores Alunos
                </h6>
                <h2 class="mb-0"><?= $stats['total_melhores_alunos'] ?? 0 ?></h2>
                <small>Destaque da turma</small>
            </div>
        </div>
    </div>
</div>

<!-- Contador de resultados -->
<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <div>
        <strong>Mostrando <?= count($alunos ?? []) ?> aluno(s)</strong>
        <?php if (!empty($busca_atual)): ?>
            <br>
            <small>Busca por: <strong><?= htmlspecialchars($busca_atual) ?></strong></small>
        <?php endif; ?>
        <?php if (!empty($curso_atual)): ?>
            <br>
            <small>Curso selecionado</small>
        <?php endif; ?>
        <?php if (!empty($professor_atual)): ?>
            <br>
            <small>Professor selecionado</small>
        <?php endif; ?>
    </div>
</div>

<!-- Aqui continua sua tabela de alunos existente -->
            <table id="alunosTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Aluno ID</th>
                        <th>Curso</th>
                        <th>Professor</th>
                        <th>Conclusão</th>
                        <th>Certificado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td>
                            <?php if ($aluno['foto_principal']): ?>
                                <img src="<?= url('uploads/' . $aluno['foto_principal']) ?>" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($aluno['nome']) ?></strong>
                            <?php if ($aluno['melhor_aluno']): ?>
                                <span class="badge bg-warning text-dark ms-1" title="Melhor Aluno">
                                    <i class="fas fa-star"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= e($aluno['alunoid']) ?></code></td>
                        <td><?= e($aluno['curso_nome']) ?></td>
                        <td><?= e($aluno['professor_nome']) ?></td>
                        <td><?= formatDate($aluno['data_fim']) ?></td>
                        <td>
                            <?php if ($aluno['certificado_path']): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Emitido
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times"></i> Pendente
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/alunos/<?= $aluno['id'] ?>/editar" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteAluno(<?= $aluno['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Excluir">
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
    $('#alunosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        pageLength: 50
    });
});

async function deleteAluno(id) {
    await Admin.confirmDelete(`/admin/alunos/${id}`, 'Tem certeza que deseja excluir este aluno?');
}
</script>