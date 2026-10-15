<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Felicite-se - Saúde Mental e Bem-Estar no Cotidiano Escolar";
$mainClass = "landing-main bg-ink-50 text-ink-950";

$recentPosts = [];

if (class_exists('PostController')) {
    // Identifica a variável de conexão criada em config/conexao.php
    $db = $pdo ?? $conexao ?? $conn ?? null;

    if ($db) {
        $controller = new PostController($db);
        if (method_exists($controller, 'listarRecentes')) {
            $recentPosts = $controller->listarRecentes(3);
        } elseif (method_exists($controller, 'getRecentPosts')) {
            $recentPosts = $controller->getRecentPosts(3);
        }
    }
}

include_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/components/public/hero.php';
require_once __DIR__ . '/components/public/pillars.php';
require_once __DIR__ . '/components/public/reflection.php';
require_once __DIR__ . '/components/public/posts.php';
require_once __DIR__ . '/components/public/social-proof.php';
require_once __DIR__ . '/components/public/about-teaser.php';
require_once __DIR__ . '/components/public/faq.php';
require_once __DIR__ . '/components/public/cta-banner.php';

include_once __DIR__ . '/includes/footer.php';
?>