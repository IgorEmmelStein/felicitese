<?php
/**
 * Dashboard Principal - Painel Administrativo
 * Projeto: Clube Felicite-se - IFSul
 */
session_start();

// Proteção de rota: se não houver sessão ativa, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$controller = new PostController($pdo);
$posts = $controller->listarPublicos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Clube Felicite-se</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <div class="container">
            <h1>Painel Administrativo - Clube Felicite-se</h1>
            <nav>
                <a href="../../blog.php" target="_blank">Ver Site Público</a>
                <a href="logout.php" class="btn-login">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-dashboard-container">
            <div class="admin-welcome">
                <h2>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?> (<?= htmlspecialchars($_SESSION['usuario_funcao']) ?>)</h2>
                <p>Gerencie as publicações e conteúdos do portal de saúde mental do IFSul.</p>
            </div>

            <div class="admin-acoes-rapidas">
                <a href="post-criar.php" class="btn-salvar">Criar Nova Publicação</a>
            </div>

            <div class="admin-secao-tabela">
                <h3>Publicações Recentes</h3>
                <?php if (!empty($posts)): ?>
                    <table class="tabela-admin">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?= htmlspecialchars($post['titulo']) ?></td>
                                    <td><span class="tag-categoria"><?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($post['data_criacao'])) ?></td>
                                    <td>
                                        <a href="post-editar.php?id=<?= $post['id'] ?>" class="btn-acao editar">Editar</a>
                                        <a href="post-excluir.php?id=<?= $post['id'] ?>" class="btn-acao excluir" onclick="return confirm('Tem certeza que deseja eliminar esta publicação?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="sem-posts">Nenhuma publicação registrada no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Clube Felicite-se - IFSul Campus Venâncio Aires</p>
        </div>
    </footer>

</body>
</html>