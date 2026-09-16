<?php
/**
 * Central de Conteúdo (Blog Público)
 * Projeto: Clube Felicite-se - IFSul
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

/**
 * Retorna a classe CSS de cor para o badge sobreposto na imagem
 */
function getBadgeClass($nomeCategoria) {
    $nome = mb_strtolower(trim($nomeCategoria ?? ''));
    if (strpos($nome, 'artigo') !== false) return 'badge-artigos';
    if (strpos($nome, 'evento') !== false) return 'badge-eventos';
    if (strpos($nome, 'notícia') !== false || strpos($nome, 'noticia') !== false) return 'badge-noticias';
    if (strpos($nome, 'psicologia') !== false || strpos($nome, 'mente') !== false) return 'badge-psico';
    if (strpos($nome, 'vivência') !== false || strpos($nome, 'vivencia') !== false || strpos($nome, 'lúdica') !== false) return 'badge-vivencias';
    return 'badge-artigos';
}

/**
 * Estima o tempo de leitura dinamicamente baseado no conteúdo
 */
function getTempoLeitura($texto, $id) {
    $temposFixos = [
        1 => 6,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 3,
    ];
    if (isset($temposFixos[$id])) {
        return $temposFixos[$id];
    }
    $palavras = str_word_count(strip_tags($texto));
    return max(3, min(10, ceil($palavras / 50)));
}

include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="blog-hero-section">
    <h1 class="blog-hero-title">Central de Conteúdo</h1>
    <p class="blog-hero-subtitle">Artigos, eventos e notícias para cuidar da mente com informação de confiança.</p>
</section>

<!-- Barra Unificada de Busca e Filtros por Categoria -->
<section class="blog-filter-section">
    <form action="blog.php" method="GET" class="blog-filter-bar">
        
        <!-- Campo de Pesquisa em Pílula -->
        <div class="search-pill-box">
            <input 
                type="text" 
                name="busca" 
                placeholder="Buscar por tema, palavra-chave..." 
                value="<?= htmlspecialchars($busca) ?>"
                class="search-pill-input"
                aria-label="Buscar publicações"
            >
            <?php if (!empty($categoriaId)): ?>
                <input type="hidden" name="categoria" value="<?= (int)$categoriaId ?>">
            <?php endif; ?>
        </div>

        <!-- Pílulas de Seleção de Categorias -->
        <div class="filter-pills-row">
            <a href="blog.php<?= !empty($busca) ? '?busca=' . urlencode($busca) : '' ?>" 
               class="filter-pill <?= empty($categoriaId) ? 'active' : '' ?>">
                Todos
            </a>
            <?php foreach ($categorias as $cat): ?>
                <a href="blog.php?categoria=<?= $cat['id'] ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" 
                   class="filter-pill <?= ($categoriaId == $cat['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['nome']) ?>
                </a>
            <?php endforeach; ?>
        </div>

    </form>
</section>

<!-- Grade de Cards das Publicações -->
<div class="cards-grid-container">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <?php
            // Validação da imagem física
            $temImagem = !empty($post['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $post['imagem']);
            $srcImagem = $temImagem
                ? 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                : 'assets/images/fallback-image.jpeg';

            $badgeClass = getBadgeClass($post['categoria_nome'] ?? 'Artigos Científicos');
            $tempoLeitura = getTempoLeitura($post['conteudo'], $post['id']);
            ?>
            <article class="content-card">
                <a href="artigo.php?id=<?= $post['id'] ?>" class="content-card-link-wrapper">
                    
                    <!-- Imagem com Badge Flutuante no Topo Esquerdo -->
                    <div class="content-card-cover">
                        <span class="category-badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($post['categoria_nome'] ?? 'Artigos Científicos') ?>
                        </span>
                        <img
                            src="<?= $srcImagem ?>"
                            alt="<?= htmlspecialchars($post['titulo']) ?>"
                            class="content-card-image"
                            loading="lazy"
                            onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';"
                        >
                    </div>

                    <!-- Corpo do Card -->
                    <div class="content-card-body">
                        <h3 class="content-card-title"><?= htmlspecialchars($post['titulo']) ?></h3>
                        <p class="content-card-excerpt"><?= mb_strimwidth(strip_tags($post['conteudo']), 0, 130, '...') ?></p>
                        
                        <!-- Rodapé com Tempo de Leitura -->
                        <div class="content-card-footer">
                            <svg class="clock-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Leitura de <?= $tempoLeitura ?> min</span>
                        </div>
                    </div>

                </a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state-box">
            <div class="empty-state-icon">🔍</div>
            <h3>Nenhuma publicação encontrada</h3>
            <p>Não encontramos artigos para os termos ou categoria pesquisados. Tente limpar os filtros para ver mais conteúdos.</p>
            <a href="blog.php" class="btn-clean-filter">Ver Todas as Publicações</a>
        </div>
    <?php endif; ?>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>