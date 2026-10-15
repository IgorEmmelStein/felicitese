<?php
/**
 * Módulo de Cadastro de Usuário com Verificação por E-mail
 * Projeto: Clube Felicite-se - IFSul
 *
 * Fluxo:
 * 1. Usuário preenche nome, e-mail e senha.
 * 2. Um código de verificação de 6 dígitos é enviado para o e-mail.
 * 3. Usuário insere o código para confirmar e a conta é criada.
 */
session_start();
require_once __DIR__ . '/config/conexao.php';

$erro = null;
$sucesso = null;
$etapa = 'cadastro'; // 'cadastro' ou 'verificacao'

// Se já existem dados pendentes na sessão, mostra etapa de verificação
if (!empty($_SESSION['cadastro_pendente'])) {
    $etapa = 'verificacao';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ── ETAPA 1: Enviar código de verificação ─────────────────────────────
    if ($acao === 'enviar_codigo') {
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
                    $erro = "Este e-mail já está cadastrado. Tente fazer login ou recuperar a senha.";
                } else {
                    // Gera código de verificação de 6 dígitos
                    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expiracao = time() + 900; // 15 minutos

                    // Armazena dados pendentes na sessão
                    $_SESSION['cadastro_pendente'] = [
                        'nome'       => $nome,
                        'email'      => $email,
                        'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                        'codigo'     => $codigo,
                        'expiracao'  => $expiracao,
                        'tentativas' => 0,
                    ];

                    // Envia o e-mail de verificação
                    $assunto = "Felicite-se - Código de Verificação: $codigo";
                    $mensagemHtml = "
                    <html>
                    <body style='font-family: Inter, Arial, sans-serif; background: #f8fafc; padding: 40px 0;'>
                        <div style='max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px;'>
                            <div style='text-align: center; margin-bottom: 24px;'>
                                <h2 style='color: #0f172a; font-size: 22px; font-weight: 800; margin: 0;'>Felicite-se</h2>
                                <p style='color: #64748b; font-size: 13px; margin-top: 4px;'>Verificação de Conta</p>
                            </div>
                            <p style='color: #334155; font-size: 14px; line-height: 1.7;'>
                                Olá <strong>$nome</strong>, use o código abaixo para confirmar o seu cadastro:
                            </p>
                            <div style='text-align: center; margin: 28px 0;'>
                                <span style='display: inline-block; background: #005691; color: #fff; font-size: 32px; font-weight: 800; letter-spacing: 8px; padding: 16px 32px; border-radius: 12px;'>$codigo</span>
                            </div>
                            <p style='color: #64748b; font-size: 12px; text-align: center;'>
                                Este código expira em <strong>15 minutos</strong>. Se você não solicitou este cadastro, ignore este e-mail.
                            </p>
                            <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;'>
                            <p style='color: #94a3b8; font-size: 11px; text-align: center;'>
                                &copy; 2026 Clube Felicite-se &middot; IFSul Campus Venâncio Aires
                            </p>
                        </div>
                    </body>
                    </html>";

                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: Felicite-se <noreply@felicite-se.org>\r\n";

                    $emailEnviado = @mail($email, $assunto, $mensagemHtml, $headers);

                    if ($emailEnviado) {
                        $etapa = 'verificacao';
                    } else {
                        // Mesmo se o mail() falhar (dev local), permite continuar para testar
                        $etapa = 'verificacao';
                        // Em desenvolvimento, exibe o código no erro para facilitar testes
                        if (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])) {
                            $erro = "Não foi possível enviar o e-mail (ambiente local). Código de teste: <strong>$codigo</strong>";
                        }
                    }
                }
            } catch (PDOException $e) {
                $erro = "Erro ao verificar cadastro: " . $e->getMessage();
            }
        }
    }

    // ── ETAPA 2: Verificar código e criar conta ───────────────────────────
    elseif ($acao === 'verificar_codigo') {
        $codigoInserido = trim($_POST['codigo_verificacao'] ?? '');

        if (empty($_SESSION['cadastro_pendente'])) {
            $erro = "Sessão expirada. Por favor, reinicie o cadastro.";
            $etapa = 'cadastro';
        } elseif (empty($codigoInserido)) {
            $erro = "Por favor, insira o código de verificação.";
            $etapa = 'verificacao';
        } else {
            $pendente = $_SESSION['cadastro_pendente'];

            // Verifica expiração
            if (time() > $pendente['expiracao']) {
                $erro = "O código de verificação expirou. Por favor, reinicie o cadastro.";
                unset($_SESSION['cadastro_pendente']);
                $etapa = 'cadastro';
            }
            // Limita tentativas
            elseif ($pendente['tentativas'] >= 5) {
                $erro = "Número máximo de tentativas excedido. Reinicie o cadastro.";
                unset($_SESSION['cadastro_pendente']);
                $etapa = 'cadastro';
            }
            // Verifica código
            elseif ($codigoInserido !== $pendente['codigo']) {
                $_SESSION['cadastro_pendente']['tentativas']++;
                $restantes = 5 - $_SESSION['cadastro_pendente']['tentativas'];
                $erro = "Código incorreto. Você tem mais $restantes tentativa(s).";
                $etapa = 'verificacao';
            } else {
                // Código correto – cria a conta
                try {
                    // Verifica novamente se o e-mail não foi cadastrado enquanto isso
                    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE LOWER(email) = LOWER(:email) LIMIT 1");
                    $stmt->bindValue(':email', $pendente['email']);
                    $stmt->execute();

                    if ($stmt->fetch()) {
                        $erro = "Este e-mail já foi cadastrado por outro processo. Tente fazer login.";
                        unset($_SESSION['cadastro_pendente']);
                        $etapa = 'cadastro';
                    } else {
                        $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, funcao) VALUES (:nome, :email, :senha, 'Administrador')");
                        $stmtInsert->execute([
                            ':nome'  => $pendente['nome'],
                            ':email' => $pendente['email'],
                            ':senha' => $pendente['senha_hash'],
                        ]);

                        unset($_SESSION['cadastro_pendente']);
                        $sucesso = "Conta criada com sucesso! Você já pode fazer login.";
                        $etapa = 'sucesso';
                    }
                } catch (PDOException $e) {
                    $erro = "Erro ao criar conta: " . $e->getMessage();
                    $etapa = 'verificacao';
                }
            }
        }
    }

    // ── Reenviar código ───────────────────────────────────────────────────
    elseif ($acao === 'reenviar_codigo') {
        if (!empty($_SESSION['cadastro_pendente'])) {
            $pendente = &$_SESSION['cadastro_pendente'];

            // Gera novo código
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $pendente['codigo'] = $codigo;
            $pendente['expiracao'] = time() + 900;
            $pendente['tentativas'] = 0;

            $nome = $pendente['nome'];
            $email = $pendente['email'];

            $assunto = "Felicite-se - Novo Código de Verificação: $codigo";
            $mensagemHtml = "
            <html>
            <body style='font-family: Inter, Arial, sans-serif; background: #f8fafc; padding: 40px 0;'>
                <div style='max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px;'>
                    <div style='text-align: center; margin-bottom: 24px;'>
                        <h2 style='color: #0f172a; font-size: 22px; font-weight: 800; margin: 0;'>Felicite-se</h2>
                        <p style='color: #64748b; font-size: 13px; margin-top: 4px;'>Verificação de Conta</p>
                    </div>
                    <p style='color: #334155; font-size: 14px; line-height: 1.7;'>
                        Olá <strong>$nome</strong>, aqui está o seu novo código de verificação:
                    </p>
                    <div style='text-align: center; margin: 28px 0;'>
                        <span style='display: inline-block; background: #005691; color: #fff; font-size: 32px; font-weight: 800; letter-spacing: 8px; padding: 16px 32px; border-radius: 12px;'>$codigo</span>
                    </div>
                    <p style='color: #64748b; font-size: 12px; text-align: center;'>
                        Este código expira em <strong>15 minutos</strong>.
                    </p>
                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;'>
                    <p style='color: #94a3b8; font-size: 11px; text-align: center;'>
                        &copy; 2026 Clube Felicite-se &middot; IFSul Campus Venâncio Aires
                    </p>
                </div>
            </body>
            </html>";

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: Felicite-se <noreply@felicite-se.org>\r\n";

            @mail($email, $assunto, $mensagemHtml, $headers);

            $etapa = 'verificacao';
            $sucesso = "Novo código enviado para <strong>" . htmlspecialchars($email) . "</strong>.";

            if (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])) {
                $erro = "Ambiente local – Novo código de teste: <strong>$codigo</strong>";
            }
        } else {
            $erro = "Sessão expirada. Por favor, reinicie o cadastro.";
            $etapa = 'cadastro';
        }
    }

    // ── Cancelar cadastro ─────────────────────────────────────────────────
    elseif ($acao === 'cancelar') {
        unset($_SESSION['cadastro_pendente']);
        $etapa = 'cadastro';
    }
}

$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$emailPendente = $_SESSION['cadastro_pendente']['email'] ?? '';
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

                    <?php if ($etapa === 'cadastro'): ?>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold">Criar Conta</h2>
                        <p class="p-base text-sm text-slate-500">
                            Preencha os dados abaixo. Um <strong>código de verificação</strong> será enviado para o seu e-mail.
                        </p>
                    <?php elseif ($etapa === 'verificacao'): ?>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold">Verificar E-mail</h2>
                        <p class="p-base text-sm text-slate-500">
                            Enviamos um código de 6 dígitos para <strong class="text-purple"><?= htmlspecialchars($emailPendente) ?></strong>. Insira-o abaixo para confirmar.
                        </p>
                    <?php elseif ($etapa === 'sucesso'): ?>
                        <div class="flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mx-auto mb-2">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold text-center">Conta Criada!</h2>
                    <?php endif; ?>
                </div>

                <?php if ($erro): ?>
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold leading-relaxed">
                        <?= $erro ?>
                    </div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold leading-relaxed">
                        <?= $sucesso ?>
                    </div>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 1: Formulário de Cadastro ═══════════ -->
                <?php if ($etapa === 'cadastro'): ?>
                <form action="criar-usuario.php" method="POST" class="space-y-4">
                    <input type="hidden" name="acao" value="enviar_codigo">

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
                        Enviar Código de Verificação
                    </button>
                </form>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 2: Verificação por Código ═══════════ -->
                <?php if ($etapa === 'verificacao'): ?>
                <form action="criar-usuario.php" method="POST" class="space-y-4">
                    <input type="hidden" name="acao" value="verificar_codigo">

                    <div>
                        <label for="codigo_verificacao" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Código de Verificação (6 dígitos)
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="codigo_verificacao"
                                name="codigo_verificacao"
                                required
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                placeholder="000000"
                                autocomplete="one-time-code"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm font-mono tracking-[0.3em] text-center focus:outline-none focus:border-purple focus:bg-white transition-all text-lg">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Verifique sua caixa de entrada e a pasta de spam.</span>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm">
                        Confirmar e Criar Conta
                    </button>
                </form>

                <!-- Ações secundárias -->
                <div class="flex items-center justify-between gap-2">
                    <form action="criar-usuario.php" method="POST" class="inline">
                        <input type="hidden" name="acao" value="reenviar_codigo">
                        <button type="submit" class="text-xs font-bold text-purple hover:underline transition-colors">
                            Reenviar código
                        </button>
                    </form>
                    <form action="criar-usuario.php" method="POST" class="inline">
                        <input type="hidden" name="acao" value="cancelar">
                        <button type="submit" class="text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors">
                            Cancelar cadastro
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 3: Sucesso ═══════════ -->
                <?php if ($etapa === 'sucesso'): ?>
                <a href="login.php" class="block w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm text-center">
                    Ir para o Login
                </a>
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

        // Auto-focus no campo de código
        const codigoInput = document.getElementById('codigo_verificacao');
        if (codigoInput) {
            codigoInput.focus();
        }
    </script>
</body>

</html>

