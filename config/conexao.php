<?php
/**
 * Arquivo de Conexão com o Banco de Dados (MariaDB) via PDO
 * Projeto: Clube Felicite-se - IFSul Campus Venâncio Aires
 */

// Parâmetros de conexão com o servidor local (XAMPP)
$host = 'localhost';
$dbname = 'clube_felicitese';
$usuario = 'root';
$senha = '';

try {
    // Configuração da DSN (Data Source Name) com charset utf8mb4 para suporte completo a caracteres especiais
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    // Parâmetros de segurança e comportamento do PDO
    $opcoes = [
        // Ativa o lançamento de exceções em caso de erros no banco
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Define o modo padrão de fetch como array associativo (facilita a manipulação dos dados)
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Desativa a emulação de prepared statements nativos, garantindo máxima segurança contra SQL Injection
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // Instancia o objeto PDO para estabelecer a conexão
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);

} catch (PDOException $e) {
    // Tratamento seguro de erro: oculta detalhes sensíveis do servidor e registra o erro internamente
    error_log("Erro de conexão com o MariaDB: " . $e->getMessage());
    
    // Encerra a execução exibindo uma mensagem amigável para o usuário final
    die("Desculpe, ocorreu um erro técnico ao tentar conectar com a base de dados do Clube Felicite-se. Tente novamente mais tarde.");
}