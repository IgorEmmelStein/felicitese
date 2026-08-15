<?php
/**
 * Encerramento de Sessão (Logout)
 * Projeto: Clube Felicite-se - IFSul
 */
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;