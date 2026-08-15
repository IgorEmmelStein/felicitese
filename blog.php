<?php
/**
 * Central de Conteúdo (Blog Público)
 * Projeto: Clube Felicite-se
 */
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

// Define o título da página
$pageTitle = "Central de Conteúdo - Clube Felicite-se";

// Busca os posts via controlador
$controller = new PostController($pdo);
$posts = $controller->listarPublicos();

// 1. O header.php já abre <html>, <head>, <link do CSS>, <body>, <header> e o <main class="container">
include_once __DIR__ . '/includes/header.php';
?>

<section class="hero-blog">
    <h2>Central de Conteúdo &amp; Produções Acadêmicas</h2>
    <p>Explore artigos científicos, orientações psicológicas e registros das nossas vivências lúdicas.</p>
</section>

<div class="posts-grid">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <article class="card-post">
                <div class="card-header">
                    <span class="tag-categoria"><?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?></span>
                    <span class="data-publicacao"><?= date('d/m/Y', strtotime($post['data_criacao'])) ?></span>
                </div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                    <p><?= mb_strimwidth(strip_tags($post['conteudo']), 0, 120, '...') ?></p>
                </div>
                <div class="card-footer">
                    <a href="artigo.php?id=<?= $post['id'] ?>" class="btn-ler-mais">Ler Artigo Completo</a>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="sem-posts">Nenhuma publicação registrada até o momento.</p>
    <?php endif; ?>
</div>

<?php
// 2. O footer.php fecha o </main>, o </footer>, o </body> e o </html>
include_once __DIR__ . '/includes/footer.php';
?>