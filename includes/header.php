<?php
/**
 * Componente Base: Header (Cabeçalho e Abertura do HTML)
 * Projeto: Felicite-se
 */
$pageTitle = $pageTitle ?? 'Central de Conteúdo - Felicite-se';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/css/style.css?v=2.2">
</head>
<body>

    <header class="site-header">
        <div class="container header-container">
            <a href="blog.php" class="brand-logo">
                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/felicitese-logo.png" alt="Logo Felicite-se" class="brand-logo-img">
                <span class="brand-text">Felicite<span class="brand-dash">-</span><span class="brand-accent">se</span></span>
            </a>

            <nav class="nav-menu">
                <a href="blog.php" class="nav-link">Início</a>
                <a href="blog.php" class="nav-link-pill">Central de Conteúdo</a>
                <a href="login.php" class="btn-member-pill">Área do Membro</a>
            </nav>
        </div>
    </header>

    <main class="container">