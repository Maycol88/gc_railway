<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';
try {
    $stmt = $pdo->query("SELECT id, nome_unidade FROM unidades ORDER BY nome_unidade");
    $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($unidades);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao listar unidades.']);
}
