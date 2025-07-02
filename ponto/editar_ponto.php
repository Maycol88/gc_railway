<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID é obrigatório."]);
    exit();
}

$id = $data['id'];
$entrada = $data['entrada'] ?? null;
$entrada_almoco = $data['entrada_almoco'] ?? null;
$saida_almoco = $data['saida_almoco'] ?? null;
$saida = $data['saida'] ?? null;

try {
    $stmt = $pdo->prepare("
        UPDATE ponto SET
            entrada = :entrada,
            entrada_almoco = :entrada_almoco,
            saida_almoco = :saida_almoco,
            saida = :saida
        WHERE id = :id
    ");

    $stmt->execute([
        ':entrada' => $entrada,
        ':entrada_almoco' => $entrada_almoco,
        ':saida_almoco' => $saida_almoco,
        ':saida' => $saida,
        ':id' => $id
    ]);

    echo json_encode(["success" => true, "message" => "Ponto atualizado com sucesso."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
