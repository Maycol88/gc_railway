<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID é obrigatório"]);
    exit();
}

$sql = "DELETE FROM unidades WHERE id = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$id]);

if ($success) {
    echo json_encode(["message" => "Unidade excluída com sucesso"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao excluir unidade"]);
}
