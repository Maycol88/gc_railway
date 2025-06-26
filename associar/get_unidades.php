<?php
require_once '../config/db.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$sql = "SELECT id, nome_unidade FROM unidades ORDER BY nome_unidade";
$stmt = $pdo->query($sql);
$unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($unidades);
