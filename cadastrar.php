<?php
require 'vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

$pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
$tfa = new TwoFactorAuth('Felicitese');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];

    if ($email && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Chave secreta única do usuário para o app autenticador
        $secret2FA = $tfa->createSecret();

        $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha, secret_2fa, status_2fa) VALUES (?, ?, ?, 1)");
        $stmt->execute([$email, $senhaHash, $secret2FA]);

        // Exibe o QR Code para o usuário escanear no aplicativo (Google Authenticator)
        $qrCodeData = $tfa->getQRCodeImageAsDataUri($email, $secret2FA);
        echo "<p>Usuário cadastrado com sucesso!</p>";
        echo "<p>Escaneie este QR Code no seu aplicativo de autenticação:</p>";
        echo "<img src='{$qrCodeData}'>";
    }
}
?>