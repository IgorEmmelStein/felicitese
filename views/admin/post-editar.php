<?php
/**
 * Tela de Edição de Publicação - Painel Administrativo
 * Projeto: Clube Felicite-se - IFSul
 */
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$controller = new PostController($pdo);
$id = $_GET['id'] ?? null;
$artigo = $controller->exibirArtigo($id);

if (!$artigo) {
    die("Publicação não encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->editar($id);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicação - Clube Felicite-se</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Editar Publicação</h2>
        
        <form action="post-editar.php?id=<?= $artigo['id'] ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título da Publicação:</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($artigo['titulo']) ?>" required>
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoria:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="1" <?= $artigo['categoria_id'] == 1 ? 'selected' : '' ?>>Artigos Científicos</option>
                    <option value="2" <?= $artigo['categoria_id'] == 2 ? 'selected' : '' ?>>Eventos Lúdicos</option>
                    <option value="3" <?= $artigo['categoria_id'] == 3 ? 'selected' : '' ?>>Notícias</option>
                </select>
            </div>

            <div class="form-group">
                <label for="conteudo">Conteúdo (Texto Rico):</label>
                <textarea id="conteudo" name="conteudo" rows="8" required><?= htmlspecialchars($artigo['conteudo']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="pdf_anexo">Substituir Anexo PDF (Opcional):</label>
                <input type="file" id="pdf_anexo" name="pdf_anexo" accept="application/pdf">
                <?php if (!empty($artigo['pdf_anexo'])): ?>
                    <small>Ficheiro atual: <?= htmlspecialchars($artigo['pdf_anexo']) ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-salvar">Salvar Alterações</button>
            <a href="index.php" class="btn-ler-mais" style="margin-left: 15px;">Cancelar</a>
        </form>
    </div>
</body>
</html>