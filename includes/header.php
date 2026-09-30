<?php
$pageTitle = $pageTitle ?? 'Central de Conteúdo - Felicite-se';
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'azul-felicite': '#005691',
              'azul-hover': '#003e6b',
              'azul-suave': '#e0f2fe',
              'azul-texto': '#0369a1',
              'fundo-app': '#f8fafc'
            }
          }
        }
      }
    </script>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/global.css">
</head>
<body class="bg-fundo-app text-slate-700 font-sans antialiased">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3">
                <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-10 w-auto">
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Felicite<span class="text-azul-felicite">-se</span></span>
            </a>

            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="<?= $baseUrl ?>index.php" class="text-sm font-semibold text-slate-600 hover:text-azul-felicite px-3 py-2 transition-colors">
                    Início
                </a>
                <a href="<?= $baseUrl ?>sobre.php" class="text-sm font-semibold text-slate-600 hover:text-azul-felicite px-3 py-2 transition-colors">
                    Nossa História
                </a>
                <a href="<?= $baseUrl ?>blog.php" class="text-sm font-semibold text-slate-600 hover:text-azul-felicite px-3 py-2 transition-colors">
                    Central de Conteúdo
                </a>
                <a href="<?= $baseUrl ?>contato.php" class="text-sm font-semibold text-slate-600 hover:text-azul-felicite px-3 py-2 transition-colors">
                    Contato
                </a>
                <a href="<?= $baseUrl ?>login.php" class="ml-2 text-sm font-semibold text-white bg-azul-felicite hover:bg-azul-hover px-5 py-2.5 rounded-full shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Área do Membro
                </a>
            </nav>
        </div>
    </header>

    <main class="<?= htmlspecialchars($mainClass ?? '') ?>">