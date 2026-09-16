<?php
/**
 * Arquivo de Conexão com o Banco de Dados (MariaDB) via PDO
 * Projeto: Felicite-se - IFSul Campus Venâncio Aires
 */

// Define BASE_URL dinamicamente ou por variável de ambiente
if (!defined('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL') ?: (strpos($_SERVER['REQUEST_URI'] ?? '', '/felicitese') !== false ? '/felicitese/' : '/'));
}

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'clube_felicitese';
$usuario = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$opcoes = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Tentativas de conexão com retry para aguardar o container MariaDB inicializar
$pdo = null;
$tentativas = 0;
$maxTentativas = 5;

while ($tentativas < $maxTentativas) {
    try {
        $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
        break;
    } catch (PDOException $e) {
        $tentativas++;
        // Se o banco ainda não existir, tenta criá-lo
        if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
            try {
                $pdoRoot = new PDO("mysql:host=$host;charset=utf8mb4", $usuario, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
                break;
            } catch (PDOException $e2) {
                // Continua para o próximo retry
            }
        }

        if ($tentativas >= $maxTentativas) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            die("<div style='font-family: sans-serif; padding: 30px; background: #fff3f3; color: #a94442; border: 1px solid #ebccd1; margin: 30px auto; max-width: 600px; border-radius: 8px;'><h2>Erro de Conexão com o Banco de Dados</h2><p>Não foi possível conectar ao banco de dados: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p><p>Verifique se o serviço MySQL/MariaDB ou os containers do Docker estão em execução.</p></div>");
        }
        sleep(1);
    }
}

// Auto-criação de tabelas e categorias iniciais caso o banco seja novo
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        funcao VARCHAR(50) DEFAULT 'Administrador',
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        conteudo LONGTEXT NOT NULL,
        imagem VARCHAR(255) NULL,
        pdf_anexo VARCHAR(255) NULL,
        categoria_id INT NULL,
        autor_id INT NULL,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $countCat = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    if ($countCat == 0) {
        $pdo->exec("INSERT INTO categorias (id, nome) VALUES 
            (1, 'Psicologia e Saúde Mental'),
            (2, 'Vivências Lúdicas'),
            (3, 'Artigos Científicos'),
            (4, 'Oficinas e Eventos')");
    }
} catch (Exception $e) {
    // Continua caso já existam restrições
}