<?php
require_once __DIR__ . '/config/conexao.php';

$pageTitle = "Contato - Universo Felicite-se";
$mainClass = "bg-fundo-app text-slate-700 font-sans antialiased";

include_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/components/public/contact-hero.php';
require_once __DIR__ . '/components/public/contact-section.php';

include_once __DIR__ . '/includes/footer.php';
?>