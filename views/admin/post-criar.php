<?php
/**
 * Painel Administrativo: Criar Publicação
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

// Busca as categorias cadastradas para popular o <select>
$stmtCategorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
$categorias = $stmtCategorias->fetchAll();

$controller = new PostController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->criar();
}

$pageTitle = "Nova Publicação - Painel Felicite-se";
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
            <h2 style="color: var(--azul-felicite);">Nova Publicação</h2>
            <a href="index.php" class="btn-acao editar">← Voltar ao Painel</a>
        </div>

        <form action="post-criar.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="titulo">Título da Publicação: *</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ex: Oficina sobre Inteligência Emocional">
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoria Temática: *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Geral</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="conteudo">Texto / Conteúdo do Artigo: *</label>
                <textarea id="conteudo" name="conteudo" rows="8" required placeholder="Escreva o artigo ou resumo do evento..."></textarea>
            </div>

            <div class="form-group">
                <label for="imagem">Imagem de Capa (JPG, PNG, WEBP - Máx. 2MB):</label>
                <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/webp">
                <small>Aparece em destaque nos cards da Central de Conteúdo e no cabeçalho do artigo.</small>
            </div>

            <div class="form-group">
                <label for="pdf_anexo">Documento PDF Anexo (Opcional - Máx. 10MB):</label>
                <input type="file" id="pdf_anexo" name="pdf_anexo" accept="application/pdf">
                <small>Permite disponibilizar artigos científicos, guias ou cartilhas para download.</small>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-salvar btn-full">Publicar Artigo</button>
            </div>
        </form>
    </div>

</body>
</html>