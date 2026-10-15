<?php
/**
 * Componente: Sidebar Esquerda
 */
?>
<aside class="admin-sidebar">
    <div>
        <!-- Brand Header -->
        <div class="admin-sidebar-header">
            <a href="../blog.php" class="brand-logo" title="Ir para o Portal Felicite-se">
                <img src="../assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="brand-logo-img">
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