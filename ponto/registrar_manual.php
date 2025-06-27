<?php
// registrar_manual.php

require_once __DIR__ . "/../config/db.php";

// CORS Headers
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Content-Type: application/json");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos ou não enviados em JSON."]);
    exit;
}

// Campos obrigatórios básicos
$required = ['user_id', 'unidade_id', 'entrada', 'saida', 'tipo_jornada'];

// Se tipo_jornada == 8, almoço é obrigatório
if (isset($data['tipo_jornada']) && intval($data['tipo_jornada']) === 8) {
    $required[] = 'entrada_almoco';
    $required[] = 'saida_almoco';
}

// Verifica os campos obrigatórios
foreach ($required as $field) {
    if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(["error" => "Campo obrigatório faltando: $field"]);
        exit;
    }
}

// Sanitização simples
$user_id = intval($data['user_id']);
$unidade_id = intval($data['unidade_id']);
$tipo_jornada = intval($data['tipo_jornada']);

function validarTimestamp($ts) {
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $ts);
    return $d && $d->format('Y-m-d H:i:s') === $ts;
}

// Campos de timestamp que podem ou não existir
$campos_timestamps = ['entrada', 'entrada_almoco', 'saida_almoco', 'saida'];

// Validar timestamps somente se estiverem presentes e não nulos
foreach ($campos_timestamps as $campo) {
    if (isset($data[$campo]) && $data[$campo] !== null && $data[$campo] !== '') {
        if (!validarTimestamp($data[$campo])) {
            http_response_code(400);
            echo json_encode(["error" => "Timestamp inválido para campo $campo. Formato esperado: YYYY-MM-DD HH:MM:SS"]);
            exit;
        }
    }
}

// Prepara os valores para bind (usar null se não enviado)
$entrada = $data['entrada'];
$entrada_almoco = $data['entrada_almoco'] ?? null;
$saida_almoco = $data['saida_almoco'] ?? null;
$saida = $data['saida'];

try {
    $sql = "INSERT INTO ponto 
            (user_id, unidade_id, entrada, entrada_almoco, saida_almoco, saida, tipo_jornada, 
             entrada_ts, entrada_almoco_ts, saida_almoco_ts, saida_ts)
            VALUES 
            (:user_id, :unidade_id, :entrada, :entrada_almoco, :saida_almoco, :saida, :tipo_jornada,
             :entrada_ts, :entrada_almoco_ts, :saida_almoco_ts, :saida_ts)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':unidade_id', $unidade_id, PDO::PARAM_INT);
    $stmt->bindValue(':entrada', $entrada);
    $stmt->bindValue(':entrada_almoco', $entrada_almoco);
    $stmt->bindValue(':saida_almoco', $saida_almoco);
    $stmt->bindValue(':saida', $saida);
    $stmt->bindValue(':tipo_jornada', $tipo_jornada, PDO::PARAM_INT);
    $stmt->bindValue(':entrada_ts', $entrada);
    $stmt->bindValue(':entrada_almoco_ts', $entrada_almoco);
    $stmt->bindValue(':saida_almoco_ts', $saida_almoco);
    $stmt->bindValue(':saida_ts', $saida);

    $stmt->execute();

    http_response_code(201);
    echo json_encode(["message" => "Ponto registrado com sucesso."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao registrar ponto: " . $e->getMessage()]);
}
