<?php

/**
 * Central de Conteúdo (Blog Público)
 * Projeto: Clube Felicite-se
 */
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Central de Conteúdo - Felicite-se";

$controller = new PostController($pdo);

// Filtros da requisição
$busca = trim(filter_input(INPUT_GET, 'busca', FILTER_DEFAULT) ?? '');
$categoriaId = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: null;

$categorias = $controller->listarCategorias();
$posts = $controller->listarPublicos($busca, $categoriaId);

include_once __DIR__ . '/includes/header.php';
?>

<section class="hero-blog">
    <h2>Central de Conteúdo &amp; Produções Acadêmicas</h2>
    <p>Explore artigos científicos, orientações psicológicas e registros das nossas vivências lúdicas.</p>
</section>

<!-- Módulo de Busca e Filtro de Categorias -->
<section class="busca-filtro-secao">
    <form action="blog.php" method="GET" class="form-busca-filtros">
        <div class="busca-input-wrapper">
            <svg class="icone-busca" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
                type="text" 
                name="busca" 
                placeholder="Pesquisar por título, palavra-chave ou tema..." 
                value="<?= htmlspecialchars($busca) ?>"
                class="input-busca"
                aria-label="Buscar publicações"
            >
            <?php if (!empty($categoriaId)): ?>
                <input type="hidden" name="categoria" value="<?= (int)$categoriaId ?>">
            <?php endif; ?>
            
            <button type="submit" class="btn-busca">Buscar</button>

            <?php if (!empty($busca) || !empty($categoriaId)): ?>
                <a href="blog.php" class="btn-limpar-busca" title="Limpar todos os filtros">✕ Limpar</a>
            <?php endif; ?>
        </div>

        <div class="categorias-pills">
            <a href="blog.php<?= !empty($busca) ? '?busca=' . urlencode($busca) : '' ?>" 
               class="pill-categoria <?= empty($categoriaId) ? 'active' : '' ?>">
                Todas as Categorias
            </a>
            <?php foreach ($categorias as $cat): ?>
                <a href="blog.php?categoria=<?= $cat['id'] ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" 
                   class="pill-categoria <?= ($categoriaId == $cat['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['nome']) ?>
                    <?php if (isset($cat['total_posts'])): ?>
                        <span class="pill-count">(<?= $cat['total_posts'] ?>)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </form>
</section>

<div class="posts-grid">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <?php
            // Verifica se a imagem existe no disco; caso contrário, define o fallback
            $temImagem = !empty($post['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $post['imagem']);
            $srcImagem = $temImagem
                ? 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                : 'assets/images/fallback-image.jpeg';
            ?>
            <article class="card-post">
                <div class="card-post-thumb">
                    <img
                        src="<?= $srcImagem ?>"
                        alt="<?= htmlspecialchars($post['titulo']) ?>"
                        class="card-post-img"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';">
                </div>

                <div class="card-header">
                    <a href="blog.php?categoria=<?= $post['categoria_id'] ?>" class="tag-categoria"><?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?></a>
                    <span class="data-publicacao"><?= !empty($post['data_criacao']) ? date('d/m/Y', strtotime($post['data_criacao'])) : '' ?></span>
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
        <div class="sem-posts-card">
            <div class="sem-posts-icone">🔍</div>
            <h3>Nenhuma publicação encontrada</h3>
            <p>Não encontramos artigos para os termos ou categoria pesquisados. Tente ajustar os termos de pesquisa.</p>
            <a href="blog.php" class="btn-salvar">Ver Todas as Publicações</a>
        </div>
    <?php endif; ?>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>