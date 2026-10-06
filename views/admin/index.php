<?php
/**
 * Dashboard Principal - Painel Administrativo 2.0
 * Projeto: Felicite-se - IFSul
 * Caminho: views/admin/index.php
 */
session_start();

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_nome'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';
require_once __DIR__ . '/../../components/admin/helpers.php';

$controller = new PostController($pdo);

$aba = filter_input(INPUT_GET, 'aba', FILTER_DEFAULT) ?? 'usuarios';
if ($aba !== 'publicacoes' && $aba !== 'usuarios') {
    $aba = 'usuarios';
}

$statusMsg = filter_input(INPUT_GET, 'status', FILTER_DEFAULT);
$busca = trim(filter_input(INPUT_GET, 'busca', FILTER_DEFAULT) ?? '');

// ==========================================================
// Ações de Gestão de Usuários
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // Criar Usuário
    if ($_POST['acao'] === 'criar_usuario') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '123456');
        $funcao = trim($_POST['funcao'] ?? 'Editor');

        if (!empty($nome) && !empty($email)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmtIns = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, funcao) VALUES (:nome, :email, :senha, :funcao)");
            try {
                $stmtIns->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':senha' => $hash,
                    ':funcao' => $funcao
                ]);
                header("Location: index.php?aba=usuarios&status=usuario_criado");
                exit;
            } catch (Exception $e) {
                header("Location: index.php?aba=usuarios&status=erro");
                exit;
            }
        }
    }

    // Editar Usuário
    if ($_POST['acao'] === 'editar_usuario') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($id && !empty($nome) && !empty($email)) {
            $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE LOWER(email) = LOWER(:email) AND id != :id LIMIT 1");
            $stmtCheck->execute([':email' => $email, ':id' => $id]);
            if ($stmtCheck->fetch()) {
                header("Location: index.php?aba=usuarios&status=erro_email");
                exit;
            }

            $stmtUpd = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id");
            try {
                $stmtUpd->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':id' => $id
                ]);

                if ($id == ($_SESSION['usuario_id'] ?? 0)) {
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                }

                header("Location: index.php?aba=usuarios&status=usuario_atualizado");
                exit;
            } catch (Exception $e) {
                header("Location: index.php?aba=usuarios&status=erro");
                exit;
            }
        }
    }
}

// Excluir Usuário
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir_usuario') {
    $uid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($uid && $uid != ($_SESSION['usuario_id'] ?? 0)) {
        $stmtDel = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmtDel->execute([':id' => $uid]);
        header("Location: index.php?aba=usuarios&status=usuario_excluido");
        exit;
    }
}

// ==========================================================
// Carregamento de Dados
// ==========================================================
$usuarios = [];
if ($aba === 'usuarios') {
    $sqlUsuarios = "SELECT id, nome, email, funcao, data_criacao FROM usuarios";
    if (!empty($busca)) {
        $sqlUsuarios .= " WHERE nome LIKE :busca OR email LIKE :busca";
        $stmtUsuarios = $pdo->prepare($sqlUsuarios . " ORDER BY id ASC");
        $stmtUsuarios->execute([':busca' => "%$busca%"]);
    } else {
        $stmtUsuarios = $pdo->query($sqlUsuarios . " ORDER BY id ASC");
    }
    $usuarios = $stmtUsuarios->fetchAll();
}

$posts = [];
if ($aba === 'publicacoes') {
    $posts = $controller->listarPublicos($busca);
}

// Dados do Usuário Logado
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Igor Nascimento';
$usuarioFuncao = $_SESSION['usuario_funcao'] ?? 'Administrador';
$partesNome = explode(' ', trim($usuarioNome));
$usuarioPrimeiroNome = $partesNome[0];
$usuarioIniciais = getInitials($usuarioNome);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($aba === 'usuarios') ? 'Gestão de Usuários' : 'Dashboard' ?> - Felicite-se</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css?v=2.2">
</head>
<body>

    <div class="admin-layout-wrapper">
        
        <?php include __DIR__ . '/../../components/admin/sidebar.php'; ?>

        <main class="admin-main-area">
            
            <?php include __DIR__ . '/../../components/admin/topbar.php'; ?>

            <div class="admin-content-body">
                
                <?php include __DIR__ . '/../../components/admin/alerts.php'; ?>

                <?php if ($aba === 'usuarios'): ?>
                    <?php include __DIR__ . '/../../components/admin/tab-users.php'; ?>
                <?php else: ?>
                    <?php include __DIR__ . '/../../components/admin/tab-publications.php'; ?>
                <?php endif; ?>

            </div>
        </main>

    </div>

    <!-- Componente Modals de Usuário -->
    <?php include __DIR__ . '/../../components/admin/user-modals.php'; ?>

</body>
</html>