<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, nome_unidade FROM unidades ORDER BY nome_unidade");
    $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($unidades);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao listar unidades.']);
}
