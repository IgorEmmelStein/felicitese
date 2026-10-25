<?php
/**
 * Módulo de Recuperação de Senha
 * Projeto: Clube Felicite-se - IFSul
 *
 * A alteração de senha é realizada diretamente pelo administrador do sistema.
 */
session_start();
require_once __DIR__ . '/config/conexao.php';

$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Felicite-se</title>
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
                            texto: 'var(--purple-texto, #8a5dd8)'
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

<body class="bg-fundo-app text-slate-700 font-sans antialiased">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-fundo-app">

        <!-- Coluna Esquerda - Banner -->
        <div class="hidden lg:flex lg:col-span-6 bg-gradient-to-br from-slate-900 via-slate-900 to-purple-hover text-white p-12 flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <a href="<?= $baseUrl ?>index.php" class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-8 w-auto">
                    <span class="text-lg font-extrabold text-white">Felicite-se</span>
                </a>
            </div>

            <div class="relative z-10 max-w-lg space-y-6">
                <h1 class="text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Segurança e <span class="text-purple-300">acesso</span> à sua conta.
                </h1>

                <p class="p-base text-slate-200">
                    Por políticas de segurança institucional, o gerenciamento e a redefinição de senhas são realizados <strong>diretamente pelo administrador do sistema</strong>.
                </p>
            </div>

            <div class="relative z-10 text-xs font-semibold text-slate-400">
                &copy; 2026 Clube Felicite-se &middot; IFSul Campus Venâncio Aires
            </div>
        </div>

        <!-- Coluna Direita - Informação / Alerta -->
        <div class="lg:col-span-6 flex items-center justify-center p-6 sm:p-12 lg:p-16">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-clean space-y-8 text-center">

                <div class="space-y-3">
                    <div class="lg:hidden mb-6 text-left">
                        <a href="<?= $baseUrl ?>index.php" class="inline-flex items-center gap-2">
                            <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-8 w-auto">
                            <span class="text-lg font-extrabold text-slate-900">Felicite<span class="text-purple">-se</span></span>
                        </a>
                    </div>

                    <div class="flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mx-auto">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>

                    <h2 class="t-h2 text-slate-900 text-2xl font-bold">Recuperação de Senha</h2>
                    <p class="p-base text-sm text-slate-600 leading-relaxed">
                        Para trocar ou recuperar a sua senha de acesso, <strong>solicite diretamente ao administrador do site</strong> para que ele redefina suas credenciais no painel.
                    </p>
                </div>

                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs leading-relaxed font-medium">
                    Entre em contato com a coordenação do projeto ou envie uma mensagem informando seu e-mail cadastrado.
                </div>

                <div class="space-y-3 pt-2">
                    <button type="button" onclick="alert('Para alterar ou recuperar sua senha, por favor solicite ao administrador do site.');" class="w-full py-3.5 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm">
                        Ver Aviso Novamente
                    </button>

                    <a href="login.php" class="block w-full py-3.5 rounded-full font-bold text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition-all text-sm text-center">
                        &larr; Voltar para a Tela de Login
                    </a>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Dispara o alerta ao carregar a página
        window.addEventListener('DOMContentLoaded', function () {
            alert('Para alterar ou recuperar sua senha, por favor solicite ao administrador do site.');
        });
    </script>
</body>

</html>
