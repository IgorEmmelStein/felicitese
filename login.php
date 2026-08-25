<?php
/**
 * Módulo de Autenticação (Login)
 * Projeto: Clube Felicite-se
 */
session_start();
require_once __DIR__ . '/config/conexao.php';

// Inicialização automática das tabelas e garantia de senha sincronizada
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        funcao VARCHAR(50) DEFAULT 'Administrador',
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Garante que o campo senha suporte o tamanho completo do hash bcrypt
    $pdo->exec("ALTER TABLE usuarios MODIFY COLUMN senha VARCHAR(255) NOT NULL");

    $hashAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $stmtCheck = $pdo->prepare("SELECT id, senha FROM usuarios WHERE LOWER(email) = 'admin@ifsul.edu.br' LIMIT 1");
    $stmtCheck->execute();
    $adminUser = $stmtCheck->fetch();

    if (!$adminUser) {
        $stmtIns = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, funcao) VALUES ('Administrador Felicite-se', 'admin@ifsul.edu.br', :senha, 'Administrador')");
        $stmtIns->execute([':senha' => $hashAdmin]);
    } else {
        // Se a senha não bater com admin123, reseta para garantir o acesso do desenvolvedor
        if (!password_verify('admin123', $adminUser['senha']) && $adminUser['senha'] !== 'admin123') {
            $stmtUpd = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $stmtUpd->execute([':senha' => $hashAdmin, ':id' => $adminUser['id']]);
        }
    }
} catch (Exception $e) {
    // Continua caso já existam restrições
}

// Se já estiver logado, redireciona direto para o painel
if (isset($_SESSION['usuario_id'])) {
    header('Location: views/admin/index.php');
    exit;
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!empty($email) && !empty($senha)) {
        try {
            // Busca o usuário no banco (case-insensitive)
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE LOWER(email) = LOWER(:email) LIMIT 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch();

            // Valida a senha
            $senhaValida = false;
            if ($usuario) {
                if (password_verify($senha, $usuario['senha'])) {
                    $senhaValida = true;
                } elseif ($senha === $usuario['senha']) {
                    $senhaValida = true;
                } elseif (md5($senha) === $usuario['senha']) {
                    $senhaValida = true;
                } elseif (sha1($senha) === $usuario['senha']) {
                    $senhaValida = true;
                } elseif (strtolower($email) === 'admin@ifsul.edu.br' && $senha === 'admin123') {
                    // Força sincronização e validação caso seja o admin padrão
                    $senhaValida = true;
                    $novoHash = password_hash('admin123', PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id")->execute([':senha' => $novoHash, ':id' => $usuario['id']]);
                }
            }

            if ($senhaValida) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Administrador';
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_funcao'] = $usuario['funcao'] ?? $usuario['cargo'] ?? $usuario['tipo'] ?? 'Administrador';

                header('Location: views/admin/index.php');
                exit;
            } else {
                $erro = "E-mail ou senha incorretos. Utilize as credenciais abaixo para entrar.";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao autenticar: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo Felicite-se</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

    <div class="login-container">
        <h2 class="login-titulo">Área Administrativa</h2>
        <p class="login-subtitulo">Clube Felicite-se • IFSul</p>

        <?php if ($erro): ?>
            <div class="alerta-erro">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail Institucional:</label>
                <input type="email" id="email" name="email" value="admin@ifsul.edu.br" required placeholder="admin@ifsul.edu.br">
            </div>

            <div class="form-group">
                <label for="senha">Senha de Acesso:</label>
                <input type="password" id="senha" name="senha" value="admin123" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-salvar btn-full">Entrar no Sistema</button>
        </form>

        <div style="margin-top: 20px; padding: 12px 15px; background: #e8f1f8; border-radius: 6px; font-size: 0.85rem; color: #005691; border: 1px solid #c9dfec;">
            <div style="font-weight: 600; margin-bottom: 4px;">🔑 Credenciais Administrativas:</div>
            <div>E-mail: <code>admin@ifsul.edu.br</code></div>
            <div>Senha: <code>admin123</code></div>
        </div>

        <div class="login-footer-link">
            <a href="blog.php" class="btn-ler-mais">← Voltar para a Central de Conteúdo</a>
        </div>
    </div>

</body>
</html>