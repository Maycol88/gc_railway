<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Content-Type: application/json");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nomeUnidade = $data["nome_unidade"];
$userId = $data["user_id"];

$sql = "INSERT INTO unidades (user_id, nome_unidade) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $nomeUnidade]);

echo json_encode(["message" => "Unidade criada com sucesso"]);
