<?php
/**
 * Módulo de Autenticação - Login do Painel Administrativo
 * Projeto: Clube Felicite-se - IFSul
 */
session_start();
require_once __DIR__ . '/../../config/conexao.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        // Utiliza Prepared Statements para prevenir SQL Injection na busca do utilizador
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        // Valida se o utilizador existe e se a palavra-passe confere com o hash armazenado
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_funcao'] = $usuario['funcao'];

            // Redireciona para o painel principal
            header("Location: index.php");
            exit;
        } else {
            $erro = "E-mail ou palavra-passe incorretos. Tente novamente.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo - Clube Felicite-se</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

    <div class="admin-container login-container">
        <h2 class="login-titulo">Clube Felicite-se</h2>
        <p class="login-subtitulo">Área de Gestão Restrita</p>

        <?php if (!empty($erro)): ?>
            <div class="alerta-erro">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail de Acesso:</label>
                <input type="email" id="email" name="email" required placeholder="ex: admin@felicitese.com">
            </div>

            <div class="form-group">
                <label for="senha">Palavra-passe:</label>
                <input type="password" id="senha" name="senha" required placeholder="********">
            </div>

            <button type="submit" class="btn-salvar btn-full">Entrar no Sistema</button>
        </form>

        <div class="login-footer-link">
            <a href="../../blog.php" class="btn-ler-mais">← Voltar para a Central de Conteúdo</a>
        </div>
    </div>

</body>
</html>