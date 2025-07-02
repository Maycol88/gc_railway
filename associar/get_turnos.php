<?php
require_once __DIR__ . '/../cors.php';

require_once '../config/db.php';


if (!isset($_GET['unidade_id'])) {
    echo json_encode(["erro" => "unidade_id não informado"]);
    exit;
}

$unidadeId = intval($_GET['unidade_id']);

try {
    $stmt = $pdo->prepare("SELECT user_id, data, turno FROM escala WHERE unidade_id = :unidade_id");
    $stmt->execute([':unidade_id' => $unidadeId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(["erro" => "Erro na consulta: " . $e->getMessage()]);
}
