<?php
/**
 * Arquivo de Conexão com o Banco de Dados (MariaDB) via PDO
 * Projeto: Clube Felicite-se - IFSul Campus Venâncio Aires
 */

// Parâmetros de conexão com o servidor local (XAMPP)
define('BASE_URL', '/felicitese/');

$host = 'localhost';
$dbname = 'clube_felicitese';
$usuario = 'root';
$senha = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $opcoes = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
} catch (PDOException $e) {
    error_log("Erro de conexão com o MariaDB: " . $e->getMessage());
    die("Desculpe, ocorreu um erro técnico ao tentar conectar com a base de dados do Clube Felicite-se.");
}