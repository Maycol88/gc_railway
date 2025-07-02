<?php
header("Access-Control-Allow-Origin: https://gcgit.vercel.app");
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

$unidade_id = $_GET['unidade_id'] ?? null;
$mes = $_GET['mes'] ?? null;  // no formato YYYY-MM, ex: 2025-06

if (!$unidade_id || !$mes) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros unidade_id e mes são obrigatórios']);
    exit;
}

try {
    $sql = "
        SELECT p.usuario_id, u.nome AS nome_usuario, p.data, p.turno
        FROM planilha_mensal p
        JOIN users u ON u.id = p.usuario_id
        WHERE p.unidade_id = :unidade_id
          AND TO_CHAR(p.data, 'YYYY-MM') = :mes
        ORDER BY u.nome, p.data
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':unidade_id' => $unidade_id,
        ':mes' => $mes,
    ]);
    $escala = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($escala);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao listar escala: ' . $e->getMessage()]);
}
