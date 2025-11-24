<?php
/**
 * Meta Tags SEO para páginas públicas
 * Otimizado para Google, Facebook, Twitter e WhatsApp
 */

// Valores padrão
$pageTitle = $pageTitle ?? ($config['nome_empresa'] ?? 'Planeta Treinamentos');
$metaDescription = $metaDescription ?? ($config['descricao_empresa'] ?? 'Cursos profissionalizantes de manutenção de smartphones');
$metaKeywords = $metaKeywords ?? 'cursos, manutenção, smartphone, celular, treinamento, certificado';
$metaImage = $metaImage ?? ($config['logo_url'] ?? '/assets/img/logo.png');
$canonical = $canonical ?? $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Básico -->
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($config['nome_empresa'] ?? '') ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($config['site_url'] ?? '') . $canonical ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($config['site_url'] ?? '') . $canonical ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($metaImage) ?>">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="<?= htmlspecialchars($config['nome_empresa'] ?? '') ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= htmlspecialchars($config['site_url'] ?? '') . $canonical ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($metaImage) ?>">
    
    <!-- WhatsApp -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $config['favicon_url'] ?? '/assets/img/favicon.png' ?>">
    <link rel="apple-touch-icon" href="<?= $config['favicon_url'] ?? '/assets/img/favicon.png' ?>">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="<?= $config['cor_primaria'] ?? '#007bff' ?>">
    <meta name="msapplication-TileColor" content="<?= $config['cor_primaria'] ?? '#007bff' ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
    <link href="/assets/css/public.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Variáveis CSS do tema -->
    <style>
        :root {
            --primary-color: <?= $config['cor_primaria'] ?? '#007bff' ?>;
            --secondary-color: <?= $config['cor_secundaria'] ?? '#6c757d' ?>;
            --accent-color: <?= $config['cor_destaque'] ?? '#ffc107' ?>;
        }
    </style>
    
    <?php if (!empty($additionalCSS)): ?>
    <!-- CSS Adicional -->
    <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>