<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Felicite-se - Saúde Mental e Bem-Estar no Cotidiano Escolar";
$mainClass = "landing-main bg-ink-50 text-ink-950";

$recentPosts = [];
/*
if (class_exists('PostController')) {
    $controller = new PostController();
    if (method_exists($controller, 'listarRecentes')) {
        $recentPosts = $controller->listarRecentes(3);
    } elseif (method_exists($controller, 'getRecentPosts')) {
        $recentPosts = $controller->getRecentPosts(3);
    }
}*/

include_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/components/hero.php';
require_once __DIR__ . '/components/pillars.php';
require_once __DIR__ . '/components/reflection.php';
require_once __DIR__ . '/components/posts.php';
require_once __DIR__ . '/components/social-proof.php';
require_once __DIR__ . '/components/about-teaser.php';
require_once __DIR__ . '/components/faq.php';
require_once __DIR__ . '/components/cta-banner.php';

include_once __DIR__ . '/includes/footer.php';
?>