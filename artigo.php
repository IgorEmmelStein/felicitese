<?php
/**
 * Página de Leitura de Artigo / Evento
 * Projeto: Felicite-se
 */
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$controller = new PostController($pdo);
$idPost = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$artigo = $idPost ? $controller->exibirArtigo($idPost) : null;

if (!$artigo) {
    $pageTitle = "Artigo não encontrado - Felicite-se";
    include_once __DIR__ . '/includes/header.php';
    ?>
    <section class="py-20 bg-fundo-app min-h-[60vh] flex items-center justify-center">
        <div class="max-w-xl mx-auto px-4 w-full">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 text-center shadow-clean space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-3xl font-bold mx-auto">
                    ⚠️
                </div>
                <h1 class="t-h2 text-slate-900">Publicação não encontrada</h1>
                <p class="p-base">
                    O artigo que procura pode ter sido <strong>removido ou o link inserido é inválido</strong>.
                </p>
                <div class="pt-2">
                    <a href="blog.php" class="inline-flex justify-center items-center px-7 py-3.5 rounded-full font-bold text-white bg-azul-felicite hover:bg-azul-hover transition-all">
                        &larr; Voltar para a Central de Conteúdo
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($artigo['titulo']) . " - Felicite-se";
include_once __DIR__ . '/includes/header.php';

$temImagem = !empty($artigo['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $artigo['imagem']);
$srcImagem = $temImagem
    ? 'uploads/imagens/' . htmlspecialchars($artigo['imagem'])
    : 'assets/images/fallback-image.jpeg';
?>

<article class="py-12 lg:py-16 bg-fundo-app border-b border-slate-200/80">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="space-y-4">
            <a href="blog.php" class="inline-flex items-center gap-2 text-xs font-bold text-azul-felicite hover:text-azul-hover transition-colors">
                &larr; Voltar para a Central de Conteúdo
            </a>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="blog.php?categoria=<?= (int)$artigo['categoria_id'] ?>" class="inline-flex items-center bg-sky-100 text-azul-texto border border-sky-200/60 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-sky-200 transition-colors">
                    <?= htmlspecialchars($artigo['categoria_nome'] ?? 'Geral') ?>
                </a>
                <span class="text-xs font-semibold text-slate-500 inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Publicado em <?= date('d/m/Y \à\s H:i', strtotime($artigo['data_criacao'])) ?>
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <?= htmlspecialchars($artigo['titulo']) ?>
            </h1>
        </div>

        <div class="bg-white p-3 rounded-3xl border border-slate-200/80 shadow-clean overflow-hidden">
            <img
                src="<?= $srcImagem ?>"
                alt="<?= htmlspecialchars($artigo['titulo']) ?>"
                class="w-full h-auto max-h-[500px] object-cover rounded-2xl"
                loading="lazy"
                onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';"
            >
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-clean space-y-6">
            <div class="text-slate-700 p-base leading-relaxed space-y-4 [&>p]:mb-4 [&>h2]:text-2xl [&>h2]:font-bold [&>h2]:text-slate-900 [&>h3]:text-xl [&>h3]:font-bold [&>h3]:text-slate-900 [&>ul]:list-disc [&>ul]:pl-5 [&>ol]:list-decimal [&>ol]:pl-5">
                <?php
                $conteudoFormatado = strip_tags($artigo['conteudo'], '<p><br><strong><b><em><i><u><a><ul><ol><li><h2><h3>');
                $conteudoFormatado = preg_replace('/<a\s+(?!.*?target=)/i', '<a target="_blank" rel="noopener noreferrer" class="text-azul-felicite font-semibold underline" ', $conteudoFormatado);
                echo $conteudoFormatado;
                ?>
            </div>

            <?php if (!empty($artigo['pdf_anexo'])): ?>
                <div class="pt-6 border-t border-slate-100">
                    <div class="p-6 bg-sky-50/80 border border-sky-200/80 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-azul-texto block">
                                📄 MATERIAL CIENTÍFICO ANEXO
                            </span>
                            <h3 class="t-h3 text-slate-900">
                                Documento Complementar em PDF
                            </h3>
                            <p class="text-xs text-slate-600 font-medium">
                                Aceda ao documento original para <strong>aprofundamento da pesquisa acadêmica</strong>.
                            </p>
                        </div>
                        <a href="uploads/pdfs/<?= htmlspecialchars($artigo['pdf_anexo']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-bold text-white bg-azul-felicite hover:bg-azul-hover transition-all text-xs flex-shrink-0 shadow-sm">
                            Baixar PDF
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="blog.php" class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200/80 shadow-clean transition-all text-sm">
                &larr; Voltar para a Central de Conteúdo
            </a>
        </div>

    </div>
</article>

<?php
include_once __DIR__ . '/includes/footer.php';
?>