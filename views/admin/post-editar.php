<?php
/**
 * Painel Administrativo: Editar Publicação
 * Projeto: Clube Felicite-se
 */
session_start();

// Proteção da rota administrativa
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_nome'])) {
    header('Location: ../../login.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$controller = new PostController($pdo);
$artigo = $controller->exibirArtigo($id);

if (!$artigo) {
    header('Location: index.php');
    exit;
}

// Processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->editar($id);
}

// Busca categorias
$stmtCategorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
$categorias = $stmtCategorias->fetchAll();

$pageTitle = "Editar: " . $artigo['titulo'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="login-body">

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--azul-felicite);">Editar Publicação #<?= $artigo['id'] ?></h2>
            <a href="index.php" class="btn-acao editar">← Voltar ao Painel</a>
        </div>

        <form action="post-editar.php?id=<?= $artigo['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="titulo">Título da Publicação: *</label>
                <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($artigo['titulo']) ?>">
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoria Temática: *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $artigo['categoria_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="conteudo">Texto / Conteúdo do Artigo: *</label>
                <textarea id="conteudo" name="conteudo" rows="8" required><?= htmlspecialchars($artigo['conteudo']) ?></textarea>
            </div>

            <!-- Imagem de Capa com Pré-visualização -->
            <div class="form-group">
                <label for="imagem">Imagem de Capa:</label>
                <?php if (!empty($artigo['imagem']) && file_exists(__DIR__ . '/../../uploads/imagens/' . $artigo['imagem'])): ?>
                    <div style="margin-bottom: 12px;">
                        <small style="display:block; margin-bottom: 5px;">Imagem atual:</small>
                        <img src="../../uploads/imagens/<?= htmlspecialchars($artigo['imagem']) ?>" alt="Capa Atual" style="max-width: 160px; height: 95px; object-fit: cover; border-radius: 6px; border: 1px solid var(--borda-cinza);">
                    </div>
                <?php endif; ?>
                <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/webp">
                <small>Envie um novo arquivo apenas se desejar substituir a imagem atual (Máx. 2MB).</small>
            </div>

            <!-- Arquivo PDF com Status Atual -->
            <div class="form-group">
                <label for="pdf_anexo">Documento PDF Anexo:</label>
                <?php if (!empty($artigo['pdf_anexo'])): ?>
                    <div style="margin-bottom: 10px; font-size: 0.9rem;">
                        <span>📄 Arquivo atual: <strong><?= htmlspecialchars($artigo['pdf_anexo']) ?></strong></span>
                    </div>
                <?php endif; ?>
                <input type="file" id="pdf_anexo" name="pdf_anexo" accept="application/pdf">
                <small>Envie um novo arquivo apenas se desejar substituir o documento atual (Máx. 10MB).</small>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-salvar btn-full">Salvar Alterações</button>
            </div>
        </form>
    </div>

</body>
</html>