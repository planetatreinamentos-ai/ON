<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= e($appName) ?> - Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    
    <!-- CSRF Token -->
    <?= \PlanetaTreinamentos\Core\CSRF::metaTag() ?>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><?= e($appName) ?></h3>
            </div>
            
            <ul class="list-unstyled components">
                <li>
                    <a href="/admin">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                
                <li>
                    <a href="#alunosSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-user-graduate"></i>
                        Alunos
                    </a>
                    <ul class="collapse list-unstyled" id="alunosSubmenu">
                        <li><a href="/admin/alunos">Listar Alunos</a></li>
                        <li><a href="/admin/alunos/criar">Novo Aluno</a></li>
                        <li><a href="/admin/pre-cadastros">Pré-cadastros</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="#cursosSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-book"></i>
                        Cursos
                    </a>
                    <ul class="collapse list-unstyled" id="cursosSubmenu">
                        <li><a href="/admin/cursos">Listar Cursos</a></li>
                        <li><a href="/admin/cursos/criar">Novo Curso</a></li>
                        <li><a href="/admin/cargas-horarias">Cargas Horárias</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="#professoresSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Professores
                    </a>
                    <ul class="collapse list-unstyled" id="professoresSubmenu">
                        <li><a href="/admin/professores">Listar Professores</a></li>
                        <li><a href="/admin/professores/criar">Novo Professor</a></li>
                    </ul>
                </li>
                
                <li>
                    <a href="/admin/certificados">
                        <i class="fas fa-certificate"></i>
                        Certificados
                    </a>
                </li>
                
                <li>
                    <a href="/admin/configuracoes">
                        <i class="fas fa-cog"></i>
                        Configurações
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Page Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3">
                            <i class="fas fa-user-circle"></i>
                            <?= e($userName) ?>
                        </span>
                        <a href="/logout" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <div class="container-fluid mt-4">
                <?= $content ?>
            </div>
        </div>
    </div>
    
    <!-- jQuery (necessário para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/admin.js') ?>"></script>
    
    <!-- Flash Messages -->
    <?php if (\PlanetaTreinamentos\Core\Session::hasFlash('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '<?= addslashes(\PlanetaTreinamentos\Core\Session::getFlash('success')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>
    
    <?php if (\PlanetaTreinamentos\Core\Session::hasFlash('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '<?= addslashes(\PlanetaTreinamentos\Core\Session::getFlash('error')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>
    
    <?php if (\PlanetaTreinamentos\Core\Session::hasFlash('warning')): ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: '<?= addslashes(\PlanetaTreinamentos\Core\Session::getFlash('warning')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>
</body>
</html>
