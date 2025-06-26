<?php
require_once '../config/db.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");


$unidade_id = $_GET['unidade_id'] ?? null;

if (!$unidade_id) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT u.id, u.nome, e.data, e.turno
  FROM usuarios u
  LEFT JOIN escala e ON u.id = e.user_id AND e.unidade_id = :unidade_id
  WHERE u.unidade_id = :unidade_id
  ORDER BY u.nome, e.data
");

$stmt->execute([':unidade_id' => $unidade_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por usuário
$resultado = [];
foreach ($rows as $r) {
  if (!isset($resultado[$r['id']])) {
    $resultado[$r['id']] = [
      'id' => $r['id'],
      'nome' => $r['nome'],
      'turnos' => []
    ];
  }

  if ($r['data']) {
    $resultado[$r['id']]['turnos'][] = [
      'data' => $r['data'],
      'turno' => $r['turno']
    ];
  }
}

echo json_encode(array_values($resultado));
