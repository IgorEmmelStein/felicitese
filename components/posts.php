<?php
$recentPosts = array_slice($recentPosts ?? [], 0, 3);
?>
<section class="py-20 bg-ink-50 text-ink-950 border-b border-ink-950" id="artigos">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
            <div>
                <span class="bg-ink-950 text-ink-50 px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider inline-block mb-4">
                    CONTEÚDO ACADÉMICO • PUBLICAÇÕES
                </span>
                <h2 class="t-h2 text-ink-950">
                    Últimas publicações e<br>recursos do projeto
                </h2>
                <p class="p-base text-ink-950">
                    Explore os nossos <strong>artigos científicos e reflexões</strong> mais recentes sobre saúde mental e educação.
                </p>
            </div>
            <a href="blog.php" class="inline-flex items-center font-semibold text-ink-950 underline">
                Ver todos os artigos
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($recentPosts as $post): ?>
                <?php
                // verifica e seta fallback se precisa pra imagem
                $caminhoFisico = dirname(__DIR__) . '/uploads/imagens/' . ($post['imagem'] ?? '');
                $temImagem = !empty($post['imagem']) && file_exists($caminhoFisico);

                $srcImagem = $temImagem
                    ? 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                    : 'assets/images/fallback-image.jpeg';

                // verifica e seta fallback se precisa pra catgoria
                $categoriaNome = $post['categoria_nome'] ?? $post['categoria'] ?? 'Artigo';

                // limita desc até 200 char, seta fallback se precisa pro resumo
                $textoLimpo = strip_tags($post['conteudo'] ?? '');
                $resumoExibicao = !empty($textoLimpo)
                    ? mb_strimwidth($textoLimpo, 0, 200, '...')
                    : 'Aceda à leitura completa para aprofundar os seus conhecimentos.';
                ?>
                <article class="bg-ink-50 rounded-2xl border border-ink-950 p-6 flex flex-col justify-between">
                    <div class="mb-4">
                        <img
                            src="<?= $srcImagem; ?>"
                            alt="<?= htmlspecialchars($post['titulo']); ?>"
                            class="w-full h-48 object-cover rounded-xl border border-ink-950"
                            loading="lazy"
                            onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';">
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-950 mb-2 block">
                            <?= htmlspecialchars($categoriaNome); ?>
                        </span>
                        <h3 class="t-h3 text-ink-950 mb-3">
                            <a href="artigo.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['titulo']); ?></a>
                        </h3>
                        <p class="p-base text-ink-950 mb-6">
                            <strong>Conteúdo destacado:</strong> <?= htmlspecialchars($resumoExibicao); ?>
                        </p>
                    </div>
                    <a href="artigo.php?id=<?= $post['id']; ?>" class="inline-flex items-center text-sm font-semibold text-ink-950 underline">
                        Ler artigo completo
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>