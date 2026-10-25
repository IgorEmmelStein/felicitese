<?php
/**
 * Módulo de Cadastro de Usuário
 * Projeto: Clube Felicite-se - IFSul
 *
 * Criação direta de conta com Nome, E-mail e confirmação de senha.
 */
session_start();
require_once __DIR__ . '/config/conexao.php';

// Se já estiver logado, redireciona para o painel
if (isset($_SESSION['usuario_id'])) {
    header('Location: admin/index.php');
    exit;
}

$erro = null;
$sucesso = null;
$contaCriada = false;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmarSenha)) {
        $erro = "Por favor, preencha todos os campos.";
    } elseif (!$email) {
        $erro = "Por favor, insira um e-mail válido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmarSenha) {
        $erro = "A senha e a confirmação não coincidem.";
    } else {
        try {
            // Verifica se o e-mail já está cadastrado
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE LOWER(email) = LOWER(:email) LIMIT 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            if ($stmt->fetch()) {
                $erro = "Este e-mail já está cadastrado. Tente fazer login.";
            } else {
                // Cria a conta diretamente com hash seguro
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, funcao) VALUES (:nome, :email, :senha, 'Administrador')");
                $stmtInsert->execute([
                    ':nome'  => $nome,
                    ':email' => $email,
                    ':senha' => $senhaHash,
                ]);

                $sucesso = "Conta criada com sucesso! Você já pode fazer login.";
                $contaCriada = true;
            }
        } catch (PDOException $e) {
            $erro = "Erro ao criar conta: " . $e->getMessage();
        }
    }
}

$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Felicite-se</title>
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
                    Faça parte do <span class="text-purple-300">Clube Felicite-se</span>.
                </h1>

                <p class="p-base text-slate-200">
                    Crie sua conta para acessar o painel e <strong>contribuir com conteúdos sobre saúde mental</strong> para a comunidade estudantil.
                </p>
            </div>

            <div class="relative z-10 text-xs font-semibold text-slate-400">
                &copy; 2026 Clube Felicite-se &middot; IFSul Campus Venâncio Aires
            </div>
        </div>

        <!-- Coluna Direita - Formulário -->
        <div class="lg:col-span-6 flex items-center justify-center p-6 sm:p-12 lg:p-16">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-clean space-y-8">

                <div class="space-y-2">
                    <div class="lg:hidden mb-6">
                        <a href="<?= $baseUrl ?>index.php" class="inline-flex items-center gap-2">
                            <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-8 w-auto">
                            <span class="text-lg font-extrabold text-slate-900">Felicite<span class="text-purple">-se</span></span>
                        </a>
                    </div>

                    <?php if ($contaCriada): ?>
                        <div class="flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mx-auto mb-2">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold text-center">Conta Criada!</h2>
                        <p class="p-base text-sm text-slate-500 text-center">
                            Sua conta foi criada com sucesso. Você já pode acessar o painel.
                        </p>
                    <?php else: ?>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold">Criar Conta</h2>
                        <p class="p-base text-sm text-slate-500">
                            Preencha os campos abaixo com seu nome, e-mail e defina a sua senha de acesso.
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($erro): ?>
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold leading-relaxed">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold leading-relaxed">
                        <?= htmlspecialchars($sucesso) ?>
                    </div>
                <?php endif; ?>

                <?php if ($contaCriada): ?>
                    <div class="space-y-3">
                        <a href="login.php" class="block w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm text-center">
                            Ir para o Login
                        </a>
                    </div>
                <?php else: ?>
                    <form action="criar-usuario.php" method="POST" class="space-y-4">

                        <!-- Campo Nome -->
                        <div>
                            <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Nome Completo
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    required
                                    placeholder="Seu nome completo"
                                    value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Campo E-mail -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                E-mail
                            </label>
                            <div class="relative">
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    placeholder="seuemail@felicite-se.org"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Campo Senha -->
                        <div>
                            <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Senha
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    required
                                    minlength="6"
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                                </svg>
                                <button type="button" class="togglePasswordBtn absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" data-target="senha">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Mínimo de 6 caracteres.</span>
                        </div>

                        <!-- Campo Confirmar Senha -->
                        <div>
                            <label for="confirmar_senha" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Confirmar Senha
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="confirmar_senha"
                                    name="confirmar_senha"
                                    required
                                    minlength="6"
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <button type="button" class="togglePasswordBtn absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" data-target="confirmar_senha">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm mt-4">
                            Criar Conta
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Link de volta -->
                <div class="pt-2 text-center flex flex-col gap-2">
                    <a href="login.php" class="text-xs font-bold text-slate-500 hover:text-purple transition-colors inline-flex items-center justify-center gap-1">
                        &larr; Voltar para a Tela de Login
                    </a>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Toggle password visibility
        document.querySelectorAll('.togglePasswordBtn').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                }
            });
        });
    </script>
</body>

</html>
