<?php

/**
 * Página de Leitura de Artigo / Evento
 * Projeto: Felicite-se
 */
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$controller = new PostController($pdo);
$idPost = $_GET['id'] ?? null;
$artigo = $controller->exibirArtigo($idPost);

if (!$artigo) {
    $pageTitle = "Artigo não encontrado - Felicite-se";
    include_once __DIR__ . '/includes/header.php';
    echo "<div class='admin-container'><h2>Publicação não encontrada.</h2><p>O artigo que você está procurando pode ter sido removido ou o link é inválido.</p><a href='blog.php' class='btn-ler-mais'>← Voltar para a Central de Conteúdo</a></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($artigo['titulo']) . " - Felicite-se";
include_once __DIR__ . '/includes/header.php';
?>

<?php
$temImagem = !empty($artigo['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $artigo['imagem']);
$srcImagem = $temImagem
    ? 'uploads/imagens/' . htmlspecialchars($artigo['imagem'])
    : 'assets/images/fallback-image.jpeg';
?>

<div class="admin-container" style="max-width: 800px; margin: 40px auto;">
    <div class="card-header" style="background-color: var(--azul-claro); border-radius: 4px; margin-bottom: 20px;">
        <a href="blog.php?categoria=<?= $artigo['categoria_id'] ?>" class="tag-categoria"><?= htmlspecialchars($artigo['categoria_nome'] ?? 'Geral') ?></a>
        <span class="data-publicacao">Publicado em <?= date('d/m/Y H:i', strtotime($artigo['data_criacao'])) ?></span>
    </div>

    <h1 style="color: var(--azul-felicite); margin-bottom: 20px; font-size: 2rem;"><?= htmlspecialchars($artigo['titulo']) ?></h1>

    <div class="artigo-container-capa" style="margin-bottom: 25px;">
        <img
            src="<?= $srcImagem ?>"
            alt="<?= htmlspecialchars($artigo['titulo']) ?>"
            class="artigo-img-destaque"
            onerror="this.onerror=null; this.src='assets/images/fallback-image.jpeg';">
    </div>

    <div class="artigo-conteudo" style="font-size: 1.05rem; line-height: 1.8; color: var(--texto-principal); margin-bottom: 30px;">
        <?= nl2br($artigo['conteudo']) ?>
    </div>

    <?php if (!empty($artigo['pdf_anexo'])): ?>
        <div style="background-color: #F8F9FA; border: 1px solid var(--borda-cinza); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: var(--azul-felicite); margin-bottom: 8px; font-size: 1.1rem;">📄 Material Científico Anexo</h3>
            <p style="color: #666; margin-bottom: 12px; font-size: 0.95rem;">Este artigo possui um documento complementar para aprofundamento da pesquisa.</p>
            <a href="uploads/pdfs/<?= htmlspecialchars($artigo['pdf_anexo']) ?>" target="_blank" class="btn-salvar" style="display: inline-block; text-decoration: none;">
                Baixar Artigo em PDF
            </a>
        </div>
    <?php endif; ?>

    <div style="margin-top: 30px; border-top: 1px solid var(--borda-cinza); padding-top: 20px;">
        <a href="blog.php" class="btn-ler-mais">← Voltar para a Central de Conteúdo</a>
    </div>
</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>