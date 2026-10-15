<?php
/**
 * Componente: Aba Gestão de Publicações
 */
?>
<!-- Banner Azul -->
<div class="admin-hero-banner">
    <div class="admin-hero-greeting">Bom te ver por aqui 👋</div>
    <h1 class="admin-hero-title">
        Olá, <?= htmlspecialchars($usuarioPrimeiroNome) ?> — <span style="color: #fbbf24;"><?= htmlspecialchars($usuarioFuncao) ?></span>
    </h1>
    <p class="admin-hero-subtitle">
        Gerencie os conteúdos que fazem a diferença na vida de tantos jovens.
    </p>
</div>

<!-- Cabeçalho Publicações Recentes -->
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
                                <a href="../artigo.php?id=<?= $post['id'] ?>" target="_blank" class="action-icon-btn" title="Visualizar no site">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="post-editar.php?id=<?= $post['id'] ?>" class="action-icon-btn" title="Editar publicação">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
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