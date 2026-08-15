<?php
/**
 * Tela de Criação de Publicações - Painel Administrativo
 * Projeto: Clube Felicite-se
 */
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$controller = new PostController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->criar();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Publicação - Clube Felicite-se</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body style="background-color: var(--bg-admin);">

    <header class="site-header">
        <div class="container">
            <h1>Painel Administrativo</h1>
            <nav>
                <a href="index.php">Voltar à Dashboard</a>
                <a href="logout.php" class="btn-login">Sair</a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <h2>Criar Nova Publicação (Artigo ou Evento)</h2>
        
        <form action="post-criar.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título da Publicação:</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Digite o título do artigo...">
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoria:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria...</option>
                    <option value="1">Artigos Científicos</option>
                    <option value="2">Eventos Lúdicos</option>
                    <option value="3">Notícias</option>
                </select>
            </div>

            <div class="form-group">
                <label for="conteudo">Conteúdo (Texto Rico):</label>
                <textarea id="conteudo" name="conteudo" rows="8" required placeholder="Escreva o conteúdo completo aqui..."></textarea>
            </div>

            <div class="form-group">
                <label for="pdf_anexo">Anexo Científico (PDF - Máx. 10MB):</label>
                <input type="file" id="pdf_anexo" name="pdf_anexo" accept="application/pdf">
                <small>Recomendação de segurança: envie arquivos PDF leves para evitar latência na rede.</small>
            </div>

            <button type="submit" class="btn-salvar">Publicar Agora</button>
            <a href="index.php" class="btn-ler-mais" style="margin-left: 15px; text-decoration: none;">Cancelar</a>
        </form>
    </div>

</body>
</html>