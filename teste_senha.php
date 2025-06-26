<?php
require_once 'config/db.php'; // Garante que a conexão está funcionando

$cpf = '12345678900';
$senhaDigitada = 'minha_senha_teste';

$stmt = $pdo->prepare("SELECT senha FROM users WHERE cpf = ?");
$stmt->execute([$cpf]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if (password_verify($senhaDigitada, $user['senha'])) {
        echo "✅ Senha correta!";
    } else {
        echo "❌ Senha incorreta.";
    }
} else {
    echo "Usuário não encontrado.";
}
