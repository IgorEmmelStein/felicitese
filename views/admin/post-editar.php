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
    <!-- Quill.js CDN (Tema Snow) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body class="login-body">

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--azul-felicite);">Editar Publicação #<?= $artigo['id'] ?></h2>
            <a href="index.php" class="btn-acao editar">← Voltar ao Painel</a>
        </div>

        <form action="post-editar.php?id=<?= $artigo['id'] ?>" method="POST" enctype="multipart/form-data" id="form-editar">
            
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
                <label for="editor">Texto / Conteúdo do Artigo: *</label>
                <!-- Editor Visual Quill com o conteúdo existente -->
                <div id="editor-container" style="min-height: 220px; background: #fff; font-size: 1rem; border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);"><?= $artigo['conteudo'] ?></div>
                <!-- Campo oculto para enviar o HTML atualizado -->
                <input type="hidden" id="conteudo" name="conteudo" required>
                <small>Utilize a barra de ferramentas para formatar em <strong>negrito</strong>, <em>itálico</em> ou adicionar <u>links</u>.</small>
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

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-salvar btn-full">Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- Script do Quill.js -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Escreva o artigo ou resumo do evento aqui...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        // Sincroniza o conteúdo do Quill com o input oculto antes do envio
        const form = document.getElementById('form-editar');
        form.addEventListener('submit', function(e) {
            const htmlContent = quill.getSemanticHTML();
            const textContent = quill.getText().trim();

            if (textContent.length === 0) {
                e.preventDefault();
                alert('Por favor, preencha o conteúdo do artigo antes de salvar.');
                quill.focus();
                return;
            }

            document.getElementById('conteudo').value = htmlContent;
        });
    </script>
</body>
</html>