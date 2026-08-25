<?php
/**
 * Exclusão de Publicação - Painel Administrativo
 * Projeto: Felicite-se - IFSul
 */
session_start();

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario_nome'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../../controllers/PostController.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $controller = new PostController($pdo);
    $controller->deletar($id);
} else {
    header("Location: index.php");
    exit;
}

