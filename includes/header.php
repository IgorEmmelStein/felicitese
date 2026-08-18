<?php
/**
 * Componente Base: Header (Cabeçalho e Abertura do HTML)
 * Projeto: Clube Felicite-se
 */
$pageTitle = $pageTitle ?? 'Clube Felicite-se - IFSul';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <div class="container header-container">
            <h1 class="logo"><a href="blog.php">Clube Felicite-se</a></h1>
            <nav class="nav-menu">
                <a href="blog.php">Início</a>
                <a href="blog.php">Central de Conteúdo</a>
                <a href="login.php" class="btn-login">Área Administrativa</a>
            </nav>
        </div>
    </header>

    <main class="container">