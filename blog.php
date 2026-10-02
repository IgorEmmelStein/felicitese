<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Central de Conteúdo - Felicite-se";

$controller = new PostController($pdo);

$busca = trim(filter_input(INPUT_GET, 'busca', FILTER_DEFAULT) ?? '');
$categoriaId = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: null;

$categorias = $controller->listarCategorias();
$posts = $controller->listarPublicos($busca, $categoriaId);

function getBadgeClass($nomeCategoria) {
    $nome = mb_strtolower(trim($nomeCategoria ?? ''));
    if (strpos($nome, 'artigo') !== false) return 'bg-sky-100 text-sky-800 border-sky-200';
    if (strpos($nome, 'evento') !== false) return 'bg-amber-100 text-amber-800 border-amber-200';
    if (strpos($nome, 'notícia') !== false || strpos($nome, 'noticia') !== false) return 'bg-purple-100 text-purple-800 border-purple-200';
    if (strpos($nome, 'psicologia') !== false || strpos($nome, 'mente') !== false) return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    if (strpos($nome, 'vivência') !== false || strpos($nome, 'vivencia') !== false || strpos($nome, 'lúdica') !== false) return 'bg-orange-100 text-orange-800 border-orange-200';
    return 'bg-sky-100 text-sky-800 border-sky-200';
}

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

<section class="py-16 lg:py-20 bg-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl space-y-4">
        <h1 class="t-h1">
            Central de <span class="text-azul-felicite">Conteúdo</span>
        </h1>
        <p class="p-base">
            Explore artigos, eventos e notícias.
        </p>
    </div>
</section>

<section class="py-10 bg-fundo-app">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <form action="blog.php" method="GET" class="space-y-6">
            <div class="max-w-2xl mx-auto relative">
                <input 
                    type="text" 
                    name="busca" 
                    placeholder="Buscar por tema, palavra-chave..." 
                    value="<?= htmlspecialchars($busca) ?>"
                    class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-200/80 rounded-full text-slate-900 text-sm focus:outline-none focus:border-azul-felicite shadow-clean transition-all"
                    aria-label="Buscar publicações"
                >
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <?php if (!empty($categoriaId)): ?>
                    <input type="hidden" name="categoria" value="<?= (int)$categoriaId ?>">
                <?php endif; ?>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="blog.php<?= !empty($busca) ? '?busca=' . urlencode($busca) : '' ?>" 
                   class="px-5 py-2 rounded-full text-xs font-bold transition-all <?= empty($categoriaId) ? 'bg-azul-felicite text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' ?>">
                    Todos
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a href="blog.php?categoria=<?= $cat['id'] ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" 
                       class="px-5 py-2 rounded-full text-xs font-bold transition-all <?= ($categoriaId == $cat['id']) ? 'bg-azul-felicite text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' ?>">
                        <?= htmlspecialchars($cat['nome']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</section>

<section class="pb-20 bg-fundo-app border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($posts)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($posts as $post): ?>
                    <?php
                    $temImagem = !empty($post['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $post['imagem']);
                    $srcImagem = $temImagem
                        ? 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                        : 'assets/images/fallback-image.jpeg';

                    $badgeClass = getBadgeClass($post['categoria_nome'] ?? 'Artigos Científicos');
                    $tempoLeitura = getTempoLeitura($post['conteudo'], $post['id']);
                    ?>
                    <article class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-clean hover:border-sky-300 transition-all flex flex-col justify-between group">
                        <a href="artigo.php?id=<?= $post['id'] ?>" class="flex flex-col h-full">
                            <div class="relative aspect-video w-full overflow-hidden bg-slate-100 p-3">
                                <span class="absolute top-5 left-5 z-10 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($post['categoria_nome'] ?? 'Artigo') ?>
                                </span>
                                <img
                                    src="<?= $srcImagem ?>"
                                    alt="<?= htmlspecialchars($post['titulo']) ?>"
                                    class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                    onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';"
                                >
                            </div>

                            <div class="p-6 flex flex-col justify-between flex-grow space-y-4">
                                <div class="space-y-2">
                                    <h3 class="t-h3 group-hover:text-azul-felicite transition-colors">
                                        <?= htmlspecialchars($post['titulo']) ?>
                                    </h3>
                                    <p class="p-base text-sm line-clamp-3">
                                        <strong>Resumo:</strong> <?= mb_strimwidth(strip_tags($post['conteudo']), 0, 130, '...') ?>
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-semibold">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        Leitura de <?= $tempoLeitura ?> min
                                    </span>
                                    <span class="text-azul-felicite font-bold group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                        Ler artigo &rarr;
                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-2xl mx-auto shadow-clean space-y-4">
                <h3 class="t-h3">
                    Nenhuma publicação encontrada
                </h3>
                <p class="p-base">
                    Não encontramos artigos para os termos ou categorias pesquisadas. Tente <strong>limpar os filtros de busca</strong> para visualizar todo o acervo.
                </p>
                <div class="pt-2">
                    <a href="blog.php" class="inline-flex justify-center items-center px-7 py-3.5 rounded-full font-bold text-white bg-azul-felicite hover:bg-azul-hover transition-all">
                        Ver Todas as Publicações
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>