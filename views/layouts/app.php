<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= e($appName) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <!-- CSRF Token -->
    <?= \PlanetaTreinamentos\Core\CSRF::metaTag() ?>
</head>
<body>
    <!-- Content -->
    <main>
        <?= $content ?>
    </main>
    
    <!-- Bootstrap Bundle JS -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
    
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
    
    <?php if (\PlanetaTreinamentos\Core\Session::hasFlash('info')): ?>
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Informação',
            text: '<?= addslashes(\PlanetaTreinamentos\Core\Session::getFlash('info')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>
</body>
</html>
