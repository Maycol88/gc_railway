<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");


require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$nomeUnidade = $data["nome_unidade"];
$userId = $data["user_id"];

$sql = "INSERT INTO unidades (user_id, nome_unidade) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $nomeUnidade]);

echo json_encode(["message" => "Unidade criada com sucesso"]);
