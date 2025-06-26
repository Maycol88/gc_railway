<?php
require_once '../config/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Content-Type: application/json");

// Preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;
$nome = $data["nome_unidade"] ?? null;

if (!$id || !$nome) {
    http_response_code(400);
    echo json_encode(["error" => "ID e nome_unidade são obrigatórios"]);
    exit();
}

$sql = "UPDATE unidades SET nome_unidade = :nome_unidade, registro_unidade = NOW() WHERE id = :id";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$nome, $id]);

if ($success) {
    echo json_encode(["message" => "Unidade atualizada com sucesso"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao atualizar unidade"]);
}
