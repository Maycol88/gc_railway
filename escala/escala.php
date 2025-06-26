<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    exit(0);
}

require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data["user_id"];
$unidadeId = $data["unidade_id"];
$dataDia = $data["data"];
$turno = $data["turno"];

$horasMap = ["B1"=>8, "B2"=>8, "N"=>12, "P"=>12, "M"=>6, "T"=>6];
$horas = $horasMap[$turno];

$sql = "INSERT INTO escala (user_id, unidade_id, data, turno, horas)
        VALUES (?, ?, ?, ?, ?)
        ON CONFLICT (user_id, unidade_id, data)
        DO UPDATE SET turno = EXCLUDED.turno, horas = EXCLUDED.horas";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $unidadeId, $dataDia, $turno, $horas]);

echo json_encode(["message" => "Turno salvo"]);
