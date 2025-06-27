<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    http_response_code(200);
    exit();
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
