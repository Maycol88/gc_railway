<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['unidade_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "unidade_id não informado"]);
    exit;
}

$unidade_id = $data['unidade_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM escala WHERE unidade_id = :unidade_id");
    $stmt->bindParam(':unidade_id', $unidade_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
