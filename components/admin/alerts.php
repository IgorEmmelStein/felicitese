<?php
/**
 * Componente: Alertas de Notificação
 */
if (!$statusMsg) return;
?>
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