<?php
/**
 * Componente: Aba Gestão de Usuários
 */
?>
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
                                <button type="button" class="action-icon-btn" title="Editar usuário" onclick="openEditUserModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nome']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($u['email']), ENT_QUOTES) ?>');">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
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