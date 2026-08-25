<?php
/**
 * Dashboard Principal - Painel Administrativo
 * Projeto: Felicite-se - IFSul
 */
session_start();

// Proteção de rota: se não houver sessão ativa, redireciona para o login
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_nome'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$controller = new PostController($pdo);

// Filtros de busca no painel administrativo
$busca = trim(filter_input(INPUT_GET, 'busca', FILTER_DEFAULT) ?? '');
$categoriaId = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: null;

$categorias = $controller->listarCategorias();
$posts = $controller->listarPublicos($busca, $categoriaId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Felicite-se</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <div class="container header-container">
            <h1 class="logo"><a href="index.php">Painel Administrativo</a></h1>
            <nav class="nav-menu">
                <a href="../../blog.php" target="_blank">Ver Site Público</a>
                <a href="logout.php" class="btn-login">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-dashboard-container">
            <div class="admin-welcome">
                <h2>Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Administrador') ?> (<?= htmlspecialchars($_SESSION['usuario_funcao'] ?? 'Administrador') ?>)</h2>
                <p>Gerencie as publicações e conteúdos do portal de saúde mental do IFSul.</p>
            </div>

            <?php
            $status = $_GET['status'] ?? null;
            if ($status === 'sucesso'): ?>
                <div class="alerta-sucesso">✓ Publicação cadastrada com sucesso!</div>
            <?php elseif ($status === 'atualizado'): ?>
                <div class="alerta-sucesso">✓ Publicação atualizada com sucesso!</div>
            <?php elseif ($status === 'excluido'): ?>
                <div class="alerta-sucesso">✓ Publicação e arquivos físicos removidos com sucesso!</div>
            <?php elseif ($status === 'erro'): ?>
                <div class="alerta-erro">✕ Ocorreu um erro ao processar a solicitação.</div>
            <?php endif; ?>

            <div class="admin-toolbar">
                <form action="index.php" method="GET" class="admin-busca-form">
                    <input 
                        type="text" 
                        name="busca" 
                        placeholder="Pesquisar por título ou conteúdo..." 
                        value="<?= htmlspecialchars($busca) ?>"
                        class="admin-input-busca"
                    >
                    <select name="categoria" class="admin-select-filtro">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($categoriaId == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-salvar" style="padding: 8px 18px; font-size: 0.9rem;">Buscar</button>

                    <?php if (!empty($busca) || !empty($categoriaId)): ?>
                        <a href="index.php" class="btn-limpar-busca" style="margin-left: 0;">✕ Limpar</a>
                    <?php endif; ?>
                </form>

                <div>
                    <a href="post-criar.php" class="btn-salvar">+ Nova Publicação</a>
                </div>
            </div>

            <div class="admin-secao-tabela">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <h3>
                        Publicações Recentes 
                        <span class="badge-contagem"><?= count($posts) ?></span>
                    </h3>
                    <?php if (!empty($busca) || !empty($categoriaId)): ?>
                        <span style="font-size: 0.88rem; color: var(--texto-secundario);">
                            Resultados para <?= !empty($busca) ? '"<em>' . htmlspecialchars($busca) . '</em>"' : '' ?>
                            <?= (!empty($busca) && !empty($categoriaId)) ? ' e ' : '' ?>
                            <?= !empty($categoriaId) ? 'categoria selecionada' : '' ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($posts)): ?>
                    <table class="tabela-admin">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Data de Publicação</th>
                                <th style="text-align: right;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($post['titulo']) ?></strong>
                                    </td>
                                    <td><span class="tag-categoria"><?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?></span></td>
                                    <td style="color: var(--texto-secundario); font-size: 0.9rem;">
                                        <?= date('d/m/Y H:i', strtotime($post['data_criacao'])) ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <a href="../../artigo.php?id=<?= $post['id'] ?>" target="_blank" class="btn-acao" style="background-color: #f1f5f9; color: var(--texto-principal);">Ver</a>
                                        <a href="post-editar.php?id=<?= $post['id'] ?>" class="btn-acao editar">Editar</a>
                                        <a href="post-excluir.php?id=<?= $post['id'] ?>" class="btn-acao excluir" onclick="return confirm('Tem certeza que deseja eliminar esta publicação e todos os seus arquivos anexos?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: var(--texto-secundario); background: #fafbfc; border-radius: var(--radius); border: 1px dashed var(--borda-cinza); margin-top: 15px;">
                        <p style="font-size: 1.1rem; margin-bottom: 10px;">Nenhuma publicação encontrada<?= (!empty($busca) || !empty($categoriaId)) ? ' com os filtros selecionados' : '' ?>.</p>
                        <?php if (!empty($busca) || !empty($categoriaId)): ?>
                            <a href="index.php" class="btn-ler-mais">← Limpar filtros de pesquisa</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Felicite-se - IFSul Campus Venâncio Aires</p>
        </div>
    </footer>

</body>
</html>