<div class="dashboard">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Bem-vindo ao painel administrativo do <?= e($appName) ?></p>
        </div>
    </div>
    
    <!-- Cards de Estatísticas -->
    <div class="row g-3 mb-4">
        <!-- Total de Alunos -->
        <div class="col-md-6 col-lg-3">
            <div class="card stats-card stats-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total de Alunos</p>
                            <h3 class="mb-0"><?= number_format($stats['total_alunos'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total de Cursos -->
        <div class="col-md-6 col-lg-3">
            <div class="card stats-card stats-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total de Cursos</p>
                            <h3 class="mb-0"><?= number_format($stats['total_cursos'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total de Professores -->
        <div class="col-md-6 col-lg-3">
            <div class="card stats-card stats-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total de Professores</p>
                            <h3 class="mb-0"><?= number_format($stats['total_professores'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total de Certificados -->
        <div class="col-md-6 col-lg-3">
            <div class="card stats-card stats-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Certificados Emitidos</p>
                            <h3 class="mb-0"><?= number_format($stats['total_certificados'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ações Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="/admin/alunos/criar" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus"></i><br>
                                Cadastrar Aluno
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/cursos/criar" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus-circle"></i><br>
                                Criar Curso
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/certificados" class="btn btn-outline-info w-100">
                                <i class="fas fa-certificate"></i><br>
                                Gerar Certificados
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/configuracoes" class="btn btn-outline-warning w-100">
                                <i class="fas fa-cog"></i><br>
                                Configurações
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informações do Sistema -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Sistema</h5>
                </div>
                <div class="card-body">
                    <p><strong>Versão:</strong> 1.0</p>
                    <p><strong>Fase Atual:</strong> Fase 1 - Core do Sistema</p>
                    <p><strong>Status:</strong> <span class="badge bg-success">Operacional</span></p>
                    <p class="mb-0"><strong>Última Atualização:</strong> <?= date('d/m/Y') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> Próximas Fases</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Fase 1:</strong> Core do Sistema ✓
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted"></i>
                            <strong>Fase 2:</strong> CRUD Completo
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted"></i>
                            <strong>Fase 3:</strong> Certificados e Integrações
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted"></i>
                            <strong>Fase 4:</strong> Páginas Públicas
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-circle text-muted"></i>
                            <strong>Fase 5:</strong> Relatórios e Finalização
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
