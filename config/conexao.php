<?php
/**
 * Arquivo de Conexão com o Banco de Dados (MariaDB) via PDO
 * Projeto: Felicite-se - IFSul Campus Venâncio Aires
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
    // Se o banco de dados ainda não existir, tenta criá-lo automaticamente
    if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
        try {
            $pdoRoot = new PDO("mysql:host=$host;charset=utf8mb4", $usuario, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
        } catch (PDOException $e2) {
            error_log("Erro ao criar base de dados: " . $e2->getMessage());
            die("<div style='font-family: sans-serif; padding: 30px; background: #fff3f3; color: #a94442; border: 1px solid #ebccd1; margin: 30px auto; max-width: 600px; border-radius: 8px;'><h2>Erro de Conexão com o Banco de Dados</h2><p>Não foi possível conectar ao MariaDB/MySQL no XAMPP: <strong>" . htmlspecialchars($e2->getMessage()) . "</strong></p><p>Verifique se o serviço MySQL está iniciado no Painel de Controle do XAMPP.</p></div>");
        }
    } else {
        error_log("Erro de conexão com o MariaDB: " . $e->getMessage());
        die("<div style='font-family: sans-serif; padding: 30px; background: #fff3f3; color: #a94442; border: 1px solid #ebccd1; margin: 30px auto; max-width: 600px; border-radius: 8px;'><h2>Erro de Conexão com o Banco de Dados</h2><p>Não foi possível conectar ao MariaDB/MySQL no XAMPP: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p><p>Verifique se o serviço MySQL está iniciado no Painel de Controle do XAMPP.</p></div>");
    }
}