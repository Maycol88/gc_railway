<?php
require_once '../config/db.php';
require_once __DIR__ . '/../cors.php';


$unidadeId = $_GET['unidade_id'] ?? null;

if (!$unidadeId) {
  http_response_code(400); // <-- mover antes
  echo json_encode(["error" => "Unidade ID não fornecido"]);
  exit;
}

try {
  $sql = "
    SELECT u.id, u.nome
    FROM users u
    JOIN unidades_user uu ON uu.user_id = u.id
    WHERE uu.unidade_id = ?
    ORDER BY u.nome
";


  $stmt = $pdo->prepare($sql);
  $stmt->execute([$unidadeId]);
  $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

  http_response_code(200); // <-- mover antes
  echo json_encode($usuarios);
} catch (Exception $e) {
  http_response_code(500); // <-- mover antes
  echo json_encode([
    "error" => "Erro ao buscar usuários vinculados",
    "details" => $e->getMessage()
  ]);
}
