<?php
require_once '../config/db.php';

// Iniciar sessão
session_start();

// CORS + segurança
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    http_response_code(200);
    exit();
}


// Captura e valida dados JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['cpf']) || !isset($data['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos ou incompletos"]);
    exit();
}

$cpf = $data["cpf"];
$senha = $data["senha"];

// Busca usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE cpf = ?");
$stmt->execute([$cpf]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($senha, $user["senha"])) {
    // Proteção contra fixação de sessão
    session_regenerate_id(true);

    // Hash única para validar navegador
    $hash = hash('sha256', $user['id'] . $_SERVER['HTTP_USER_AGENT']);

    // Armazena na sessão
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_nome'] = $user['nome'];
    $_SESSION['hash'] = $hash;

    echo json_encode([
    "message" => "Login realizado com sucesso",
    "usuario" => [
        "id" => $user['id'],
        "nome" => $user['nome'],
        "cpf" => $user['cpf'],
        "role" => $user['role'],
        "senha_temporaria" => (bool)$user['senha_temporaria'], // CAST para bool
    ]
]);


} else {
    http_response_code(401);
    echo json_encode(["error" => "CPF ou senha inválidos"]);
}

$pdo = null;
// Finaliza a conexão com o banco
session_write_close(); // Garante que a sessão seja salva corretamente