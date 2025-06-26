<?php
require_once '../config/db.php';
require_once '../email/email.php';  // Inclui o arquivo com as funções de email

// CORS + segurança
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['id']) || empty($data['nova_senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados incompletos"]);
    exit();
}

$id = $data['id'];
$novaSenha = password_hash($data['nova_senha'], PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET senha = ?, senha_temporaria = FALSE WHERE id = ?");
    $stmt->execute([$novaSenha, $id]);

    // Buscar email e nome do usuário
    $stmtEmail = $pdo->prepare("SELECT email, nome FROM users WHERE id = ?");
    $stmtEmail->execute([$id]);
    $user = $stmtEmail->fetch(PDO::FETCH_ASSOC);

    if ($user && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        enviar_email_confirmacao_senha($user['email'], $user['nome']);
    }

    echo json_encode(["message" => "Senha atualizada com sucesso"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao atualizar senha: " . $e->getMessage()]);
}
