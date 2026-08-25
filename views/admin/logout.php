<?php
/**
 * Encerramento de Sessão (Logout)
 * Projeto: Felicite-se - IFSul
 */
session_start();
session_unset();
session_destroy();
header("Location: ../../login.php");
exit;