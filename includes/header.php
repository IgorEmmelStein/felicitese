<?php
$pageTitle = $pageTitle ?? 'Central de Conteúdo - Felicite-se';
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Carrega os links centralizados
$navItems = file_exists(__DIR__ . '/../config/navigation.php') 
    ? require __DIR__ . '/../config/navigation.php' 
    : [];
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
              purple: {
                DEFAULT: 'var(--purple, #7c3aed)',
                hover: 'var(--purple-hover, #5e2bb6)',
                suave: 'var(--purple-suave, #e7dbfc)',
                texto: 'var(--purple-texto, #8a5dd8)',
                50: '#faf5ff',
                100: '#f3e8ff',
                200: '#e9d5ff',
                300: '#d8b4fe',
                400: '#c084fc',
                500: '#a855f7',
                600: '#9333ea',
                700: '#7e22ce',
                800: '#6b21a8',
                900: '#581c87',
                950: '#3b0764'
              },
              'purple-hover': 'var(--purple-hover, #5e2bb6)',
              'purple-suave': 'var(--purple-suave, #e7dbfc)',
              'purple-texto': 'var(--purple-texto, #8a5dd8)',
              'badge-purple': 'var(--badge-purple, #7c3aed)',
              'azul-felicite': 'var(--purple, #7c3aed)',
              'azul-hover': 'var(--purple-hover, #5e2bb6)',
              'azul-suave': 'var(--purple-suave, #e7dbfc)',
              'azul-texto': 'var(--purple-texto, #8a5dd8)',
              'fundo-app': '#f8fafc'
            }
          }
        }
      }
    </script>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/global.css">
</head>
<body class="bg-fundo-app text-slate-700 font-sans antialiased flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Logo -->
            <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3">
                <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-10 w-auto">
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Felicite<span class="text-purple">-se</span></span>
            </a>

            <!-- Navegação Desktop e Logo Direito -->
            <div class="hidden md:flex items-center gap-4 sm:gap-6">
                <nav class="flex items-center gap-2 sm:gap-3">
                    <?php foreach ($navItems as $item): ?>
                        <a href="<?= $baseUrl . $item['url'] ?>" class="text-sm font-semibold text-slate-600 hover:text-purple px-3 py-2 transition-colors">
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    <?php endforeach; ?>

                    <a href="<?= $baseUrl ?>login.php" class="ml-2 text-sm font-semibold text-white !bg-purple hover:bg-purple-hover px-5 py-2.5 rounded-full shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Área do Membro
                    </a>
                </nav>
            </div>

            <!-- Área Direita Mobile (Logo + Botão Menu) -->
            <div class="flex md:hidden items-center gap-3">
                <a href="<?= $baseUrl ?>index.php" class="flex items-center" title="Projeto Felicite-se">
                    <img src="<?= $baseUrl ?>assets/images/felicitese-logo.png" alt="Logo Projeto Felicite-se" class="h-9 w-auto object-contain">
                </a>
                <button 
                    id="btn-open-mobile"
                    type="button" 
                    class="p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none transition-colors"
                    aria-label="Abrir Menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Drawer do Menu Mobile (Oculto por padrão com 'hidden') -->
    <div id="mobile-menu-wrapper" class="hidden fixed inset-0 z-50 md:hidden">
        <!-- Fundo Roxo Semi-transparente (-purple) -->
        <div id="btn-close-backdrop" class="fixed inset-0 bg-purple/60 backdrop-blur-sm transition-opacity" style="background-color: color-mix(in srgb, var(--purple, #7c3aed) 60%, transparent);"></div>

        <!-- Painel Lateral / Gaveta -->
        <div class="fixed inset-y-0 right-0 w-full max-w-xs bg-white shadow-2xl flex flex-col justify-between p-6 z-10 transition-transform">
            <div class="space-y-6">
                <!-- Cabeçalho do Drawer -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <span class="text-lg font-bold text-slate-900">Navegação</span>
                    <button id="btn-close-mobile" type="button" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Links de Navegação Mobile -->
                <nav class="flex flex-col space-y-1">
                    <?php foreach ($navItems as $item): ?>
                        <a href="<?= $baseUrl . $item['url'] ?>" class="text-base font-semibold text-slate-700 hover:text-purple hover:bg-slate-50 px-4 py-3 rounded-xl transition-all">
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Rodapé do Drawer Mobile -->
            <div class="pt-6 border-t border-slate-100 space-y-3">
                <a href="<?= $baseUrl ?>login.php" class="w-full text-center text-sm font-bold text-white bg-purple hover:bg-purple-hover py-3 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Área do Membro
                </a>
            </div>
        </div>
    </div>

    <!-- Script de Alternância do Menu Mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnOpen = document.getElementById('btn-open-mobile');
            const btnClose = document.getElementById('btn-close-mobile');
            const btnBackdrop = document.getElementById('btn-close-backdrop');
            const menuWrapper = document.getElementById('mobile-menu-wrapper');

            function toggleMenu() {
                if (menuWrapper) {
                    menuWrapper.classList.toggle('hidden');
                    document.body.classList.toggle('overflow-hidden');
                }
            }

            if (btnOpen) btnOpen.addEventListener('click', toggleMenu);
            if (btnClose) btnClose.addEventListener('click', toggleMenu);
            if (btnBackdrop) btnBackdrop.addEventListener('click', toggleMenu);
        });
    </script>

    <main class="flex-grow <?= htmlspecialchars($mainClass ?? '') ?>">