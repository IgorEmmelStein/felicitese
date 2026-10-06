<?php
/**
 * Componente: Topbar Superior
 */
?>
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