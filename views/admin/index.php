<?php
/**
 * Dashboard Principal - Painel Administrativo 2.0
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

// Controle de abas: 'usuarios' ou 'publicacoes' (padrão 'usuarios')
$aba = filter_input(INPUT_GET, 'aba', FILTER_DEFAULT) ?? 'usuarios';
if ($aba !== 'publicacoes' && $aba !== 'usuarios') {
    $aba = 'usuarios';
}

$statusMsg = filter_input(INPUT_GET, 'status', FILTER_DEFAULT);
$busca = trim(filter_input(INPUT_GET, 'busca', FILTER_DEFAULT) ?? '');

// ==========================================================
// Ações de Gestão de Usuários
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'criar_usuario') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar_usuario') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($id && !empty($nome) && !empty($email)) {
        // Verifica se o e-mail já pertence a outro usuário
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

            // Se for o usuário logado, atualiza também a sessão
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

if (isset($_GET['acao']) && $_GET['acao'] === 'excluir_usuario') {
    $uid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($uid && $uid != ($_SESSION['usuario_id'] ?? 0)) {
        $stmtDel = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmtDel->execute([':id' => $uid]);
        header("Location: index.php?aba=usuarios&status=usuario_excluido");
        exit;
    }
}

// Carregamento de dados para Usuários
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

// Carregamento de dados para Publicações
$posts = [];
if ($aba === 'publicacoes') {
    $posts = $controller->listarPublicos($busca);
}

// Dados do Usuário Logado
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Igor Nascimento';
$usuarioFuncao = $_SESSION['usuario_funcao'] ?? 'Administrador';
$partesNome = explode(' ', trim($usuarioNome));
$usuarioPrimeiroNome = $partesNome[0];

// Helpers de Formatação
function getInitials($fullName) {
    $parts = explode(' ', trim($fullName));
    $initials = '';
    if (!empty($parts[0])) {
        $initials .= mb_strtoupper(mb_substr($parts[0], 0, 1));
    }
    if (count($parts) > 1 && !empty($parts[count($parts) - 1])) {
        $initials .= mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return $initials ?: 'US';
}

function formatPostDate($dateString) {
    if (!$dateString) return 'Data não informada';
    $time = strtotime($dateString);
    $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $dia = date('d', $time);
    $mes = $meses[(int)date('m', $time) - 1] ?? 'Mês';
    $ano = date('Y', $time);
    return "$dia $mes $ano";
}

function getCategoryBadgeClass($categoryName) {
    $name = mb_strtolower(trim($categoryName));
    if (strpos($name, 'artigo') !== false) return 'artigos';
    if (strpos($name, 'evento') !== false || strpos($name, 'oficina') !== false) return 'eventos';
    if (strpos($name, 'notícia') !== false || strpos($name, 'noticia') !== false) return 'noticias';
    if (strpos($name, 'psico') !== false || strpos($name, 'saúde') !== false || strpos($name, 'saude') !== false) return 'psico';
    if (strpos($name, 'vivência') !== false || strpos($name, 'vivencia') !== false || strpos($name, 'lúdica') !== false || strpos($name, 'ludica') !== false) return 'vivencias';
    return 'artigos';
}

$usuarioIniciais = getInitials($usuarioNome);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($aba === 'usuarios') ? 'Gestão de Usuários' : 'Dashboard' ?> - Felicite-se</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css?v=2.2">
</head>
<body>

    <div class="admin-layout-wrapper">
        
        <!-- SIDEBAR ESQUERDA: Apenas Usuários e Publicações -->
        <aside class="admin-sidebar">
            <div>
                <!-- Brand Header -->
                <div class="admin-sidebar-header">
                    <a href="../../blog.php" class="brand-logo" title="Ir para o Portal Felicite-se">
                        <img src="../../assets/images/felicitese-logo.png" alt="Logo Felicite-se" class="brand-logo-img">
                        <span class="brand-text">Felicite<span class="brand-dash">-</span><span class="brand-accent">se</span></span>
                    </a>
                </div>

                <!-- Menu de Navegação -->
                <nav class="admin-sidebar-nav">
                    <a href="index.php?aba=publicacoes" class="sidebar-nav-item <?= ($aba === 'publicacoes') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Publicações</span>
                    </a>

                    <a href="index.php?aba=usuarios" class="sidebar-nav-item <?= ($aba === 'usuarios') ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Usuários</span>
                    </a>
                </nav>
            </div>

            <!-- Rodapé da Sidebar: Card Usuário Logado e Sair -->
            <div class="admin-sidebar-footer">
                <div class="sidebar-user-card">
                    <div class="user-avatar-circle">
                        <?= htmlspecialchars($usuarioIniciais) ?>
                    </div>
                    <div class="sidebar-user-info">
                        <span class="sidebar-user-name" title="<?= htmlspecialchars($usuarioNome) ?>">
                            <?= htmlspecialchars($usuarioNome) ?>
                        </span>
                        <span class="sidebar-user-role"><?= htmlspecialchars($usuarioFuncao) ?></span>
                    </div>
                </div>
                <a href="logout.php" class="sidebar-logout-btn" title="Encerrar Sessão">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL -->
        <main class="admin-main-area">
            
            <!-- Topbar Superior -->
            <header class="admin-topbar">
                <div class="topbar-title">
                    <?= ($aba === 'usuarios') ? 'Gestão de Usuários' : 'Dashboard' ?>
                </div>

                <div class="topbar-actions">
                    <form action="index.php" method="GET" class="topbar-search-box">
                        <input type="hidden" name="aba" value="<?= htmlspecialchars($aba) ?>">
                        <input 
                            type="text" 
                            name="busca" 
                            class="topbar-search-input" 
                            placeholder="Buscar..." 
                            value="<?= htmlspecialchars($busca) ?>"
                        >
                    </form>

                    <button type="button" class="topbar-icon-btn" title="Notificações" onclick="alert('Nenhuma notificação recente.');">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <div class="admin-content-body">
                
                <!-- Alertas de Notificação -->
                <?php if ($statusMsg === 'sucesso'): ?>
                    <div class="alerta-sucesso">✓ Publicação cadastrada com sucesso!</div>
                <?php elseif ($statusMsg === 'atualizado'): ?>
                    <div class="alerta-sucesso">✓ Publicação atualizada com sucesso!</div>
                <?php elseif ($statusMsg === 'excluido'): ?>
                    <div class="alerta-sucesso">✓ Publicação removida com sucesso!</div>
                <?php elseif ($statusMsg === 'usuario_criado'): ?>
                    <div class="alerta-sucesso">✓ Novo usuário cadastrado com sucesso!</div>
                <?php elseif ($statusMsg === 'usuario_atualizado'): ?>
                    <div class="alerta-sucesso">✓ Dados do usuário atualizados com sucesso!</div>
                <?php elseif ($statusMsg === 'usuario_excluido'): ?>
                    <div class="alerta-sucesso">✓ Usuário removido com sucesso!</div>
                <?php elseif ($statusMsg === 'erro_email'): ?>
                    <div class="alerta-erro">✕ Este e-mail já está em uso por outro usuário.</div>
                <?php elseif ($statusMsg === 'erro'): ?>
                    <div class="alerta-erro">✕ Ocorreu um erro ao processar a solicitação.</div>
                <?php endif; ?>

                <?php if ($aba === 'usuarios'): ?>
                    <!-- ==========================================
                         ABA 1: GESTÃO DE USUÁRIOS (CONTROLE DE ACESSO)
                         ========================================== -->
                    <div class="section-header-row">
                        <h2 class="section-header-title">
                            Controle de Acesso
                            <span class="section-info-icon" title="Gerencie acessos e membros do Clube Felicite-se">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                            </span>
                        </h2>

                        <button type="button" class="btn-admin-primary" onclick="openUserModal();">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                            <span>Novo usuário</span>
                        </button>
                    </div>

                    <div class="admin-card-table">
                        <table class="admin-modern-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Função</th>
                                    <th style="text-align: right;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <div class="user-avatar-circle soft">
                                                        <?= htmlspecialchars(getInitials($u['nome'])) ?>
                                                    </div>
                                                    <span class="user-name-text"><?= htmlspecialchars($u['nome']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="user-email-text"><?= htmlspecialchars($u['email']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge-role <?= (strtolower($u['funcao']) === 'administrador') ? 'admin' : 'editor' ?>">
                                                    <?= htmlspecialchars($u['funcao']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-icons-row">
                                                    <!-- Editar -->
                                                    <button type="button" class="action-icon-btn" title="Editar usuário" onclick="openEditUserModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nome']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($u['email']), ENT_QUOTES) ?>');">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                    </button>
                                                    <!-- Excluir -->
                                                    <?php if ($u['id'] != ($_SESSION['usuario_id'] ?? 0)): ?>
                                                        <a href="index.php?aba=usuarios&acao=excluir_usuario&id=<?= $u['id'] ?>" class="action-icon-btn delete" title="Remover usuário" onclick="return confirm('Deseja realmente remover o usuário <?= htmlspecialchars(addslashes($u['nome'])) ?>?');">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            </svg>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--texto-secundario);">
                                            Nenhum usuário encontrado.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <!-- ==========================================
                         ABA 2: GESTÃO DE PUBLICAÇÕES (DASHBOARD)
                         ========================================== -->
                    <!-- Banner Azul (Imagem 4) -->
                    <div class="admin-hero-banner">
                        <div class="admin-hero-greeting">Bom te ver por aqui 👋</div>
                        <h1 class="admin-hero-title">
                            Olá, <?= htmlspecialchars($usuarioPrimeiroNome) ?> — <span style="color: #fbbf24;"><?= htmlspecialchars($usuarioFuncao) ?></span>
                        </h1>
                        <p class="admin-hero-subtitle">
                            Gerencie os conteúdos que fazem a diferença na vida de tantos jovens.
                        </p>
                    </div>

                    <!-- Cabeçalho Publicações Recentes (Imagens 2 e 3) -->
                    <div class="section-header-row">
                        <h2 class="section-header-title">Publicações Recentes</h2>
                        <a href="post-criar.php" class="btn-admin-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Nova Publicação</span>
                        </a>
                    </div>

                    <div class="admin-card-table">
                        <table class="admin-modern-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Autor</th>
                                    <th style="text-align: right;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($posts)): ?>
                                    <?php foreach ($posts as $post): ?>
                                        <tr>
                                            <td>
                                                <div class="post-title-cell">
                                                    <span class="post-title-main"><?= htmlspecialchars($post['titulo']) ?></span>
                                                    <span class="post-date-sub"><?= formatPostDate($post['data_criacao']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-cat <?= getCategoryBadgeClass($post['categoria_nome'] ?? '') ?>">
                                                    <?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="post-author-cell">
                                                    <?= htmlspecialchars(!empty($post['autor_nome']) ? $post['autor_nome'] : 'Equipe Felicite-se') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-icons-row">
                                                    <!-- Visualizar no Portal -->
                                                    <a href="../../artigo.php?id=<?= $post['id'] ?>" target="_blank" class="action-icon-btn" title="Visualizar no site">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                    </a>
                                                    <!-- Editar (Lápis) -->
                                                    <a href="post-editar.php?id=<?= $post['id'] ?>" class="action-icon-btn" title="Editar publicação">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                    </a>
                                                    <!-- Excluir (Lixeira) -->
                                                    <a href="post-excluir.php?id=<?= $post['id'] ?>" class="action-icon-btn delete" title="Excluir publicação" onclick="return confirm('Tem certeza que deseja excluir esta publicação e seus arquivos anexos?');">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--texto-secundario);">
                                            Nenhum publicação encontrada<?= !empty($busca) ? ' para "' . htmlspecialchars($busca) . '"' : '' ?>.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </main>

    </div>

    <!-- MODAL NOVO USUÁRIO -->
    <div id="userModal" class="admin-modal-overlay">
        <div class="admin-modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Novo Usuário</h3>
                <button type="button" class="modal-close-btn" onclick="closeUserModal();">&times;</button>
            </div>
            <form action="index.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="aba" value="usuarios">
                <input type="hidden" name="acao" value="criar_usuario">

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="modalNome">Nome Completo</label>
                    <input type="text" id="modalNome" name="nome" required placeholder="Ex: Mariana Silva">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="modalEmail">E-mail</label>
                    <input type="email" id="modalEmail" name="email" required placeholder="mariana@felicite-se.org">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="modalSenha">Senha Provisória</label>
                    <input type="password" id="modalSenha" name="senha" required value="123456" placeholder="••••••••">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="modalFuncao">Função</label>
                    <select id="modalFuncao" name="funcao">
                        <option value="Editor" selected>Editor</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <button type="button" class="btn-salvar" style="background-color: #f1f5f9; color: #475569 !important;" onclick="closeUserModal();">Cancelar</button>
                    <button type="submit" class="btn-admin-primary">Salvar Usuário</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR USUÁRIO -->
    <div id="editUserModal" class="admin-modal-overlay">
        <div class="admin-modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Editar Usuário</h3>
                <button type="button" class="modal-close-btn" onclick="closeEditUserModal();">&times;</button>
            </div>
            <form action="index.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="aba" value="usuarios">
                <input type="hidden" name="acao" value="editar_usuario">
                <input type="hidden" name="id" id="editUserId" value="">

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="editUserNome">Nome Completo</label>
                    <input type="text" id="editUserNome" name="nome" required placeholder="Nome do usuário">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="editUserEmail">E-mail</label>
                    <input type="email" id="editUserEmail" name="email" required placeholder="email@felicite-se.org">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <button type="button" class="btn-salvar" style="background-color: #f1f5f9; color: #475569 !important;" onclick="closeEditUserModal();">Cancelar</button>
                    <button type="submit" class="btn-admin-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts de Interação -->
    <script>
        function openUserModal() {
            const modal = document.getElementById('userModal');
            if (modal) modal.classList.add('open');
        }

        function closeUserModal() {
            const modal = document.getElementById('userModal');
            if (modal) modal.classList.remove('open');
        }

        function openEditUserModal(id, nome, email) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserNome').value = nome;
            document.getElementById('editUserEmail').value = email;
            const modal = document.getElementById('editUserModal');
            if (modal) modal.classList.add('open');
        }

        function closeEditUserModal() {
            const modal = document.getElementById('editUserModal');
            if (modal) modal.classList.remove('open');
        }

        // Fecha ao clicar fora da caixa do modal
        window.addEventListener('click', function(e) {
            const createModal = document.getElementById('userModal');
            if (e.target === createModal) {
                closeUserModal();
            }
            const editModal = document.getElementById('editUserModal');
            if (e.target === editModal) {
                closeEditUserModal();
            }
        });
    </script>

</body>
</html>