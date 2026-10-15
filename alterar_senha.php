<?php

/**
 * Módulo de Recuperação de Senha com Link Mágico por E-mail
 * Projeto: Clube Felicite-se - IFSul
 *
 * Fluxo em 3 etapas:
 * 1. Usuário informa apenas o e-mail.
 * 2. Um link único de recuperação é enviado por e-mail.
 * 3. Usuário clica no link e define a nova senha.
 *
 * Usa tabela `tokens_recuperacao` para persistir tokens (funciona entre dispositivos).
 */
session_start();
require_once __DIR__ . '/config/conexao.php';

// ── Auto-criação da tabela de tokens ──────────────────────────────────────
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS tokens_recuperacao (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        token VARCHAR(128) NOT NULL UNIQUE,
        expiracao DATETIME NOT NULL,
        usado TINYINT(1) DEFAULT 0,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token),
        INDEX idx_usuario (usuario_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
}

$erro = null;
$sucesso = null;
$etapa = 'email'; // 'email', 'enviado', 'nova_senha', 'sucesso'
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);
$linkRecuperacao = null;

// ── Detecta se o acesso é via link mágico (GET com token) ─────────────────
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET['token']);

    try {
        $stmt = $pdo->prepare("
            SELECT tr.*, u.nome, u.email
            FROM tokens_recuperacao tr
            JOIN usuarios u ON u.id = tr.usuario_id
            WHERE tr.token = :token
            LIMIT 1
        ");
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        $tokenData = $stmt->fetch();

        if (!$tokenData) {
            $erro = "Link inválido ou já utilizado. Solicite uma nova recuperação.";
            $etapa = 'email';
        } elseif ($tokenData['usado'] == 1) {
            $erro = "Este link já foi utilizado. Solicite uma nova recuperação.";
            $etapa = 'email';
        } elseif (strtotime($tokenData['expiracao']) < time()) {
            $erro = "Este link expirou. Solicite uma nova recuperação.";
            $etapa = 'email';
        } else {
            // Token válido – mostra formulário de nova senha
            $etapa = 'nova_senha';
            $_SESSION['recuperacao_token'] = $token;
            $_SESSION['recuperacao_usuario_id'] = $tokenData['usuario_id'];
            $_SESSION['recuperacao_nome'] = $tokenData['nome'];
        }
    } catch (PDOException $e) {
        $erro = "Erro ao verificar o link: " . $e->getMessage();
    }
}

// ── Processar POST ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ── ETAPA 1: Enviar link de recuperação por e-mail ────────────────────
    if ($acao === 'enviar_link') {
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '');

        if (empty($email)) {
            $erro = "Por favor, insira um e-mail válido.";
        } else {
            try {
                // Verifica se o e-mail existe no banco
                $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE LOWER(email) = LOWER(:email) LIMIT 1");
                $stmt->bindValue(':email', $email);
                $stmt->execute();
                $usuario = $stmt->fetch();

                if (!$usuario) {
                    $erro = "Não encontramos uma conta com este e-mail.";
                } else {
                    // Invalida tokens anteriores deste usuário
                    $pdo->prepare("UPDATE tokens_recuperacao SET usado = 1 WHERE usuario_id = :uid AND usado = 0")
                        ->execute([':uid' => $usuario['id']]);

                    // Gera token único de 64 caracteres
                    $token = bin2hex(random_bytes(32));
                    $expiracao = date('Y-m-d H:i:s', time() + 1800); // 30 minutos

                    // Salva no banco
                    $stmtToken = $pdo->prepare("INSERT INTO tokens_recuperacao (usuario_id, token, expiracao) VALUES (:uid, :token, :exp)");
                    $stmtToken->execute([
                        ':uid'   => $usuario['id'],
                        ':token' => $token,
                        ':exp'   => $expiracao,
                    ]);

                    // Monta a URL do link mágico
                    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $caminho = dirname($_SERVER['SCRIPT_NAME']);
                    $caminho = rtrim($caminho, '/');
                    $linkRecuperacao = "$protocolo://$host$caminho/alterar_senha.php?token=$token";

                    $nome = htmlspecialchars($usuario['nome']);

                    // Envia e-mail com o link
                    $assunto = "Felicite-se - Recuperação de Senha";
                    $mensagemHtml = "
                    <html>
                    <body style='font-family: Inter, Arial, sans-serif; background: #f8fafc; padding: 40px 0;'>
                        <div style='max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px;'>
                            <div style='text-align: center; margin-bottom: 24px;'>
                                <h2 style='color: #0f172a; font-size: 22px; font-weight: 800; margin: 0;'>Felicite-se</h2>
                                <p style='color: #64748b; font-size: 13px; margin-top: 4px;'>Recuperação de Senha</p>
                            </div>
                            <p style='color: #334155; font-size: 14px; line-height: 1.7;'>
                                Olá <strong>$nome</strong>, recebemos uma solicitação para recuperar a senha da sua conta.
                            </p>
                            <p style='color: #334155; font-size: 14px; line-height: 1.7;'>
                                Clique no botão abaixo para definir uma nova senha:
                            </p>
                            <div style='text-align: center; margin: 28px 0;'>
                                <a href='$linkRecuperacao' style='display: inline-block; background: #005691; color: #ffffff; font-size: 15px; font-weight: 700; padding: 14px 36px; border-radius: 50px; text-decoration: none;'>Redefinir Minha Senha</a>
                            </div>
                            <p style='color: #64748b; font-size: 12px; text-align: center;'>
                                Este link expira em <strong>30 minutos</strong> e só pode ser usado uma vez.
                            </p>
                            <p style='color: #94a3b8; font-size: 11px; margin-top: 20px;'>
                                Se o botão não funcionar, copie e cole o link abaixo no seu navegador:
                            </p>
                            <p style='color: #0369a1; font-size: 11px; word-break: break-all;'>
                                $linkRecuperacao
                            </p>
                            <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;'>
                            <p style='color: #94a3b8; font-size: 11px; text-align: center;'>
                                Se você não solicitou esta recuperação, ignore este e-mail. Sua senha permanecerá a mesma.
                            </p>
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

                    $etapa = 'enviado';
                    $sucesso = "Enviamos um link de recuperação para <strong>" . htmlspecialchars($email) . "</strong>.";
                }
            } catch (PDOException $e) {
                $erro = "Erro ao processar solicitação: " . $e->getMessage();
            }
        }
    }

    // ── ETAPA 3: Salvar nova senha ────────────────────────────────────────
    elseif ($acao === 'alterar_senha') {
        $novaSenha = trim($_POST['nova_senha'] ?? '');
        $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');
        $tokenSessao = $_SESSION['recuperacao_token'] ?? '';
        $usuarioId = $_SESSION['recuperacao_usuario_id'] ?? 0;

        if (empty($tokenSessao) || empty($usuarioId)) {
            $erro = "Sessão inválida. Por favor, utilize o link enviado por e-mail novamente.";
            $etapa = 'email';
        } elseif (empty($novaSenha) || empty($confirmarSenha)) {
            $erro = "Por favor, preencha todos os campos.";
            $etapa = 'nova_senha';
        } elseif (strlen($novaSenha) < 6) {
            $erro = "A nova senha deve ter pelo menos 6 caracteres.";
            $etapa = 'nova_senha';
        } elseif ($novaSenha !== $confirmarSenha) {
            $erro = "A senha e a confirmação não coincidem.";
            $etapa = 'nova_senha';
        } else {
            try {
                // Verifica se o token ainda é válido no banco
                $stmtCheck = $pdo->prepare("SELECT id FROM tokens_recuperacao WHERE token = :token AND usuario_id = :uid AND usado = 0 AND expiracao > NOW() LIMIT 1");
                $stmtCheck->execute([':token' => $tokenSessao, ':uid' => $usuarioId]);

                if (!$stmtCheck->fetch()) {
                    $erro = "Link expirado ou já utilizado. Solicite uma nova recuperação.";
                    unset($_SESSION['recuperacao_token'], $_SESSION['recuperacao_usuario_id'], $_SESSION['recuperacao_nome']);
                    $etapa = 'email';
                } else {
                    // Atualiza a senha
                    $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id")
                        ->execute([':senha' => $novaSenhaHash, ':id' => $usuarioId]);

                    // Marca o token como usado
                    $pdo->prepare("UPDATE tokens_recuperacao SET usado = 1 WHERE token = :token")
                        ->execute([':token' => $tokenSessao]);

                    // Limpa sessão de recuperação
                    unset($_SESSION['recuperacao_token'], $_SESSION['recuperacao_usuario_id'], $_SESSION['recuperacao_nome']);

                    $sucesso = "Senha alterada com sucesso! Você já pode fazer login com a nova senha.";
                    $etapa = 'sucesso';
                }
            } catch (PDOException $e) {
                $erro = "Erro ao atualizar senha: " . $e->getMessage();
                $etapa = 'nova_senha';
            }
        }
    }

    // ── Reenviar link ─────────────────────────────────────────────────────
    elseif ($acao === 'reenviar') {
        // Redireciona de volta para a etapa de e-mail
        $etapa = 'email';
    }
}

$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$nomeUsuario = $_SESSION['recuperacao_nome'] ?? '';
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
                    Recupere o <span class="text-purple-300">acesso</span> à sua conta.
                </h1>

                <p class="p-base text-slate-200">
                    Enviaremos um link seguro para o seu e-mail cadastrado para que você possa <strong>redefinir sua senha</strong> em poucos cliques.
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

                    <!-- Indicador de Etapas -->
                    <?php if (!in_array($etapa, ['sucesso'])): ?>
                    <div class="flex items-center gap-2 mb-4">
                        <?php
                        $etapasMap = ['email' => 1, 'enviado' => 2, 'nova_senha' => 3];
                        $etapaAtual = $etapasMap[$etapa] ?? 1;
                        for ($i = 1; $i <= 3; $i++):
                            $ativa = $i <= $etapaAtual;
                        ?>
                            <div class="flex-1 h-1.5 rounded-full <?= $ativa ? 'bg-purple' : 'bg-slate-200' ?> transition-all"></div>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($etapa === 'email'): ?>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold">Recuperar Senha</h2>
                        <p class="p-base text-sm text-slate-500">
                            Informe o <strong>e-mail da sua conta</strong> e enviaremos um link para redefinir sua senha.
                        </p>

                    <?php elseif ($etapa === 'enviado'): ?>
                        <div class="flex items-center justify-center w-16 h-16 bg-purple-suave rounded-full mx-auto mb-2">
                            <svg class="w-8 h-8 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold text-center">Verifique seu E-mail</h2>
                        <p class="p-base text-sm text-slate-500 text-center">
                            Enviamos um link de recuperação. <strong>Clique no link</strong> que você recebeu para definir uma nova senha.
                        </p>

                    <?php elseif ($etapa === 'nova_senha'): ?>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold">Nova Senha</h2>
                        <p class="p-base text-sm text-slate-500">
                            <?php if ($nomeUsuario): ?>
                                Olá <strong class="text-purple"><?= htmlspecialchars($nomeUsuario) ?></strong>! Defina a sua nova senha de acesso.
                            <?php else: ?>
                                Defina a sua <strong>nova senha de acesso</strong>.
                            <?php endif; ?>
                        </p>

                    <?php elseif ($etapa === 'sucesso'): ?>
                        <div class="flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mx-auto mb-2">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="t-h2 text-slate-900 text-2xl font-bold text-center">Senha Alterada!</h2>
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

                <!-- ═══════════ ETAPA 1: Solicitar E-mail ═══════════ -->
                <?php if ($etapa === 'email'): ?>
                <form action="alterar_senha.php" method="POST" class="space-y-4">
                    <input type="hidden" name="acao" value="enviar_link">

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            E-mail da Conta
                        </label>
                        <div class="relative">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                                autofocus
                                placeholder="seuemail@felicite-se.org"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm">
                        Enviar Link de Recuperação
                    </button>
                </form>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 2: Link Enviado ═══════════ -->
                <?php if ($etapa === 'enviado'): ?>

                    <!-- Ambiente Local: mostra o link para teste -->
                    <?php if ($isLocal && $linkRecuperacao): ?>
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-bold text-amber-800">Ambiente Local — Link de Teste</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-amber-200">
                            <p class="text-[11px] text-slate-500 mb-1.5 font-semibold">Clique no link abaixo ou copie para o navegador:</p>
                            <a href="<?= htmlspecialchars($linkRecuperacao) ?>" class="text-xs text-purple hover:underline font-mono break-all leading-relaxed block">
                                <?= htmlspecialchars($linkRecuperacao) ?>
                            </a>
                        </div>
                        <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($linkRecuperacao, ENT_QUOTES) ?>'); this.textContent='✓ Copiado!'; setTimeout(() => this.textContent='Copiar Link', 2000);"
                                class="w-full py-2.5 rounded-xl font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 transition-all text-xs">
                            Copiar Link
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="space-y-3 text-center">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <p class="text-xs text-slate-600 leading-relaxed">
                                <strong>Não recebeu o e-mail?</strong> Verifique a pasta de <strong>spam</strong> ou lixo eletrônico. O link expira em <strong>30 minutos</strong>.
                            </p>
                        </div>

                        <form action="alterar_senha.php" method="POST" class="inline">
                            <input type="hidden" name="acao" value="reenviar">
                            <button type="submit" class="text-xs font-bold text-purple hover:underline transition-colors">
                                Enviar novamente para outro e-mail
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 3: Nova Senha ═══════════ -->
                <?php if ($etapa === 'nova_senha'): ?>
                <form action="alterar_senha.php" method="POST" class="space-y-4">
                    <input type="hidden" name="acao" value="alterar_senha">

                    <!-- Campo Nova Senha -->
                    <div>
                        <label for="nova_senha" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nova Senha
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="nova_senha"
                                name="nova_senha"
                                required
                                minlength="6"
                                autofocus
                                placeholder="••••••••"
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-purple focus:bg-white transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <button type="button" class="togglePasswordBtn absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" data-target="nova_senha">
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
                            Confirmar Nova Senha
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

                    <button type="submit" class="w-full py-4 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-sm transition-all text-sm mt-2">
                        Salvar Nova Senha
                    </button>
                </form>
                <?php endif; ?>

                <!-- ═══════════ ETAPA 4: Sucesso ═══════════ -->
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
    </script>
</body>

</html>