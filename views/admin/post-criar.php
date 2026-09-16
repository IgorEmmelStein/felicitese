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
    <!-- Quill.js CDN (Tema Snow) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body class="login-body">

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--azul-felicite);">Nova Publicação</h2>
            <a href="index.php" class="btn-acao editar">← Voltar ao Painel</a>
        </div>

        <form action="post-criar.php" method="POST" enctype="multipart/form-data" id="form-post">
            
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
                <label for="editor">Texto / Conteúdo do Artigo: *</label>
                <!-- Editor Visual Quill -->
                <div id="editor-container" style="min-height: 220px; background: #fff; font-size: 1rem; border-bottom-left-radius: var(--radius); border-bottom-right-radius: var(--radius);"></div>
                <!-- Campo oculto para enviar o HTML ao backend -->
                <input type="hidden" id="conteudo" name="conteudo" required>
                <small>Utilize a barra de ferramentas para formatar em <strong>negrito</strong>, <em>itálico</em> ou adicionar <u>links</u>.</small>
            </div>

            <div class="form-group">
                <label for="imagem">Imagem de Capa (JPG, PNG, WEBP - Máx. 2MB):</label>
                <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/webp">
                <small>Aparece em destaque nos cards da Central de Conteúdo e no cabeçalho do artigo.</small>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-salvar btn-full">Publicar Artigo</button>
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
        const form = document.getElementById('form-post');
        form.addEventListener('submit', function(e) {
            const htmlContent = quill.getSemanticHTML();
            const textContent = quill.getText().trim();

            if (textContent.length === 0) {
                e.preventDefault();
                alert('Por favor, preencha o conteúdo do artigo antes de publicar.');
                quill.focus();
                return;
            }

            document.getElementById('conteudo').value = htmlContent;
        });
    </script>
</body>
</html>