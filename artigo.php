<?php
/**
 * Página de Leitura de Artigo / Evento
 * Projeto: Felicite-se
 */
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

// Instancia o controlador
$controller = new PostController($pdo);

// Recupera o ID da URL de forma segura
$idPost = $_GET['id'] ?? null;
$artigo = $controller->exibirArtigo($idPost);

// Se o artigo não existir, define um título genérico ou redireciona
if (!$artigo) {
    $pageTitle = "Artigo não encontrado - Felicite-se";
    include_once __DIR__ . '/includes/header.php';
    echo "<div class='admin-container'><h2>Publicação não encontrada.</h2><p>O artigo que você está procurando pode ter sido removido ou o link é inválido.</p><a href='blog.php' class='btn-ler-mais'>Voltar para a Central de Conteúdo</a></div>";
    include_once __DIR__ . '/includes/footer.php';
    exit;
}

// Define o título da página com o nome do artigo
$pageTitle = htmlspecialchars($artigo['titulo']) . " - Felicite-se";
include_once __DIR__ . '/includes/header.php';
?>

<article class="artigo-completo">
    <div class="artigo-meta">
        <span class="tag-categoria"><?= htmlspecialchars($artigo['categoria_nome'] ?? 'Geral') ?></span>
        <span class="data-publicacao">Publicado em <?= date('d/m/Y H:i', strtotime($artigo['data_criacao'])) ?></span>
    </div>

    <h1><?= htmlspecialchars($artigo['titulo']) ?></h1>

    <div class="artigo-conteudo">
        <?= nl2br($artigo['conteudo']) ?>
    </div>

    <?php if (!empty($artigo['pdf_anexo'])): ?>
        <div class="bloco-anexo-pdf">
            <h3>📄 Material Científico Anexo</h3>
            <p>Este artigo possui um documento complementar para aprofundamento da pesquisa.</p>
            <a href="uploads/pdfs/<?= htmlspecialchars($artigo['pdf_anexo']) ?>" target="_blank" class="btn-baixar-pdf">
                Baixar Artigo em PDF
            </a>
        </div>
    <?php endif; ?>

    <?php if (!empty($artigo['cta_link']) && !empty($artigo['cta_texto'])): ?>
        <div class="bloco-cta">
            <a href="<?= htmlspecialchars($artigo['cta_link']) ?>" target="_blank" class="btn-acao-destaque">
                <?= htmlspecialchars($artigo['cta_texto']) ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="artigo-voltar">
        <a href="blog.php" class="btn-ler-mais">← Voltar para a Central de Conteúdo</a>
    </div>
</article>

<?php
include_once __DIR__ . '/includes/footer.php';
?>