<?php
/**
 * Componente: Modals de Usuário (Criar e Editar)
 */
?>
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

    window.addEventListener('click', function(e) {
        const createModal = document.getElementById('userModal');
        if (e.target === createModal) closeUserModal();
        
        const editModal = document.getElementById('editUserModal');
        if (e.target === editModal) closeEditUserModal();
    });
</script>