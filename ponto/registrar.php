<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . "/../config/db.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data["user_id"] ?? null;
$unidadeId = $data["unidade_id"] ?? null;
$tipoJornada = $data["tipo_jornada"] ?? null; // 8 ou 12 apenas no primeiro registro

if (!$userId || !$unidadeId) {
    http_response_code(400);
    echo json_encode(["erro" => "user_id e unidade_id são obrigatórios."]);
    exit;
}

// Considera o timestamp atual (data + hora)
$timestampAgora = date('Y-m-d H:i:s');

// Buscando ponto do usuário no dia atual: para isso, verifica registros cuja data da entrada está entre 00:00 e 23:59 do dia atual
$stmt = $pdo->prepare("SELECT * FROM ponto WHERE user_id = ? AND entrada >= ? AND entrada < ?");
$dataHoje = date('Y-m-d');
$inicioDia = $dataHoje . " 00:00:00";
$fimDia = $dataHoje . " 23:59:59";
$stmt->execute([$userId, $inicioDia, $fimDia]);
$ponto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ponto) {
    // Novo registro do dia - tipo_jornada deve ser 8 ou 12
    if (!in_array($tipoJornada, [8, 12])) {
        http_response_code(400);
        echo json_encode(["erro" => "tipo_jornada deve ser 8 ou 12 no primeiro registro do dia."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO ponto (user_id, unidade_id, entrada, tipo_jornada) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $unidadeId, $timestampAgora, $tipoJornada]);
    echo json_encode(["mensagem" => "Entrada registrada com sucesso.", "campo_registrado" => "entrada", "tipo" => $tipoJornada]);
    exit;
}

$tipoJornada = $ponto["tipo_jornada"];

if ($tipoJornada == 12) {
    if (is_null($ponto["saida"])) {
        $stmt = $pdo->prepare("UPDATE ponto SET saida = ? WHERE id = ?");
        $stmt->execute([$timestampAgora, $ponto["id"]]);
        echo json_encode(["mensagem" => "Saída registrada. Bom descanso!", "campo_registrado" => "saida"]);
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Todos os registros de 12 horas já foram feitos."]);
    }
    exit;
}

// tipo_jornada == 8
if (is_null($ponto["entrada_almoco"])) {
    $stmt = $pdo->prepare("UPDATE ponto SET entrada_almoco = ? WHERE id = ?");
    $stmt->execute([$timestampAgora, $ponto["id"]]);
    echo json_encode(["mensagem" => "Entrada para almoço registrada.", "campo_registrado" => "entrada_almoco"]);
} elseif (is_null($ponto["saida_almoco"])) {
    $stmt = $pdo->prepare("UPDATE ponto SET saida_almoco = ? WHERE id = ?");
    $stmt->execute([$timestampAgora, $ponto["id"]]);
    echo json_encode(["mensagem" => "Saída do almoço registrada.", "campo_registrado" => "saida_almoco"]);
} elseif (is_null($ponto["saida"])) {
    $stmt = $pdo->prepare("UPDATE ponto SET saida = ? WHERE id = ?");
    $stmt->execute([$timestampAgora, $ponto["id"]]);
    echo json_encode(["mensagem" => "Saída registrada. Bom descanso!", "campo_registrado" => "saida"]);
} else {
    http_response_code(400);
    echo json_encode(["erro" => "Todos os registros de 8 horas já foram feitos."]);
}
exit;
