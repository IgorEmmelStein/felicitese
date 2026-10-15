<?php
// Garante o carregamento dos 3 posts mais recentes se não tiverem sido passados previamente
if (empty($recentPosts)) {
    $dbConnection = $pdo ?? $conexao ?? $conn ?? null;
    if ($dbConnection) {
        require_once dirname(__DIR__, 2) . '/controllers/PostController.php';
        if (class_exists('PostController')) {
            $postController = new PostController($dbConnection);
            if (method_exists($postController, 'listarRecentes')) {
                $recentPosts = $postController->listarRecentes(3);
            }
        }
    }
}

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
            <?php if (!empty($recentPosts)): ?>
                <?php foreach ($recentPosts as $post): ?>
                    <?php
                    // verifica e seta fallback se precisa pra imagem
                    $caminhoFisico = dirname(__DIR__, 2) . '/uploads/imagens/' . ($post['imagem'] ?? '');
                    $temImagem = !empty($post['imagem']) && file_exists($caminhoFisico);

                    $srcImagem = $temImagem
                        ? 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                        : 'assets/images/fallback-image.jpeg';

                    // verifica e seta fallback se precisa pra categoria
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
            <?php else: ?>
                <article class="bg-ink-50 rounded-2xl border border-ink-950 p-6 flex flex-col justify-between">
                    <div class="mb-4">
                        <img src="assets/images/estudantes/felicitese-workshop-imersivo-saude-mental-jovens-participantes.webp" alt="Workshop Imersivo de Saúde Mental" class="w-full h-48 object-cover rounded-xl border border-ink-950" loading="lazy">
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-950 mb-2 block">
                            PSICOLOGIA POSITIVA
                        </span>
                        <h3 class="t-h3 text-ink-950 mb-3">
                            O que são Forças de Caráter<br>e como identificá-las?
                        </h3>
                        <p class="p-base text-ink-950 mb-6">
                            Descubra como reconhecer as suas virtudes e <strong>potenciar o seu bem-estar</strong> no quotidiano com base na ciência.
                        </p>
                    </div>
                    <a href="blog.php" class="inline-flex items-center text-sm font-semibold text-ink-950 underline">
                        Ler artigo completo
                    </a>
                </article>
                <article class="bg-ink-50 rounded-2xl border border-ink-950 p-6 flex flex-col justify-between">
                    <div class="mb-4">
                        <img src="assets/images/estudantes/felicitese-oficina-acolhimento-escuta-empatica-cartoes.webp" alt="Oficina de Escuta Empática" class="w-full h-48 object-cover rounded-xl border border-ink-950" loading="lazy">
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-950 mb-2 block">
                            SAÚDE MENTAL
                        </span>
                        <h3 class="t-h3 text-ink-950 mb-3">
                            Práticas de escuta empática<br>e acolhimento na escola
                        </h3>
                        <p class="p-base text-ink-950 mb-6">
                            Como intervenções simples e dinâmicas criam um <strong>ambiente escolar seguro</strong> e acolhedor para todos.
                        </p>
                    </div>
                    <a href="blog.php" class="inline-flex items-center text-sm font-semibold text-ink-950 underline">
                        Ler artigo completo
                    </a>
                </article>
                <article class="bg-ink-50 rounded-2xl border border-ink-950 p-6 flex flex-col justify-between">
                    <div class="mb-4">
                        <img src="assets/images/estudantes/felicitese-acao-social-parceiros-da-esperanca-estudantes.webp" alt="Ação Social Parceiros da Esperança" class="w-full h-48 object-cover rounded-xl border border-ink-950" loading="lazy">
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-950 mb-2 block">
                            EXTENSÃO SOCIAL
                        </span>
                        <h3 class="t-h3 text-ink-950 mb-3">
                            O impacto das oficinas<br>do Felicite-se na comunidade
                        </h3>
                        <p class="p-base text-ink-950 mb-6">
                            Relato detalhado sobre as dinâmicas vivenciais desenvolvidas com <strong>crianças e jovens da comunidade</strong>.
                        </p>
                    </div>
                    <a href="blog.php" class="inline-flex items-center text-sm font-semibold text-ink-950 underline">
                        Ler artigo completo
                    </a>
                </article>
            <?php endif; ?>
        </div>
    </div>
</section>