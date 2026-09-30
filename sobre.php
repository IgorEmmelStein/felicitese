<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Nossa História - projeto Felicite-se";
$mainClass = "bg-fundo-app text-slate-700 font-sans antialiased";

include_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/components/about-hero.php';
require_once __DIR__ . '/components/about-origin.php';
require_once __DIR__ . '/components/about-timeline.php';
require_once __DIR__ . '/components/about-reconhecimento.php';
require_once __DIR__ . '/components/about-conceito.php';
require_once __DIR__ . '/components/faq.php';

include_once __DIR__ . '/includes/footer.php';
?>