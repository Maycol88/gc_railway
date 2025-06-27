<?php
require_once '../config/db.php';
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


// ========================
// MÉTODO GET – Buscar cargas
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'] ?? null;

    if ($userId) {
        // Buscar carga de um único usuário
        $stmt = $pdo->prepare("
            SELECT COALESCE(carga, 0) AS carga 
            FROM carga_horaria 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($result ?: ["carga" => 0]);
        exit;
    } else {
        // Buscar todas as cargas
        $query = "
            SELECT u.id, u.nome, 
                   COALESCE(ch.carga, 0) AS carga
            FROM users u
            LEFT JOIN carga_horaria ch ON u.id = ch.user_id
            ORDER BY u.nome
        ";
        $users = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
        exit;
    }
}


// ========================
// MÉTODO POST – Salvar carga
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = $data["user_id"] ?? null;
    $cargaHoras = $data["carga"] ?? null;

    if (!$userId || !is_numeric($cargaHoras)) {
        http_response_code(400);
        echo json_encode(["erro" => "Dados inválidos."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM carga_horaria WHERE user_id = ?");
        $stmt->execute([$userId]);

        if ($stmt->fetch()) {
            // Atualiza
            $stmt = $pdo->prepare("UPDATE carga_horaria SET carga = ? WHERE user_id = ?");
            $stmt->execute([$cargaHoras, $userId]);
        } else {
            // Insere novo
            $stmt = $pdo->prepare("INSERT INTO carga_horaria (user_id, carga) VALUES (?, ?)");
            $stmt->execute([$userId, $cargaHoras]);
        }

        echo json_encode(["mensagem" => "Carga salva."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro no banco: " . $e->getMessage()]);
    }
    exit;
}

// ========================
// MÉTODO NÃO PERMITIDO
// ========================
http_response_code(405);
echo json_encode(["erro" => "Método não permitido."]);
exit;