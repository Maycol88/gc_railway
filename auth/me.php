<?php
require_once '../utils/jwt_utils.php';
require_once '../config/db.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Token não fornecido"]);
    exit();
}

$token = $matches[1];

$payload = validarJWT($token);  // sua função que valida o token e retorna payload

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido ou expirado"]);
    exit();
}

// Pega o id do usuário no payload
$userId = $payload->user_id ?? null;


if (!$userId) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido"]);
    exit();
}

// Busca dados do usuário no banco
$stmt = $pdo->prepare("SELECT id, nome, cpf, role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo json_encode(["error" => "Usuário não encontrado"]);
    exit();
}

echo json_encode($user);
