<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';






$input = json_decode(file_get_contents('php://input'), true);

// Corrigir nomes para refletir os usados no front-end
$user_id = $input['user_id'] ?? null;
$unidade_id = $input['unidade_id'] ?? null;
$data = $input['data'] ?? null;
$turno = $input['turno'] ?? null;

if (!$user_id || !$unidade_id || !$data) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros user_id, unidade_id e data são obrigatórios']);
    exit;
}

try {
    if ($turno === '' || $turno === null) {
        // Deletar turno
        $stmt = $pdo->prepare("DELETE FROM escala WHERE user_id = :user_id AND unidade_id = :unidade_id AND data = :data");
        $stmt->execute([
            ':user_id' => $user_id,
            ':unidade_id' => $unidade_id,
            ':data' => $data,
        ]);
        echo json_encode(['mensagem' => 'Turno removido']);
        exit;
    }

    // Verificar se já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM escala WHERE user_id = :user_id AND unidade_id = :unidade_id AND data = :data");
    $stmtCheck->execute([
        ':user_id' => $user_id,
        ':unidade_id' => $unidade_id,
        ':data' => $data,
    ]);

    if ($stmtCheck->rowCount() > 0) {
        // Atualizar turno
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $stmtUpdate = $pdo->prepare("UPDATE escala SET turno = :turno WHERE id = :id");
        $stmtUpdate->execute([
            ':turno' => $turno,
            ':id' => $row['id'],
        ]);
        echo json_encode(['mensagem' => 'Turno atualizado']);
    } else {
        // Inserir novo turno
        $stmtInsert = $pdo->prepare("INSERT INTO escala (user_id, unidade_id, data, turno) VALUES (:user_id, :unidade_id, :data, :turno)");
        $stmtInsert->execute([
            ':user_id' => $user_id,
            ':unidade_id' => $unidade_id,
            ':data' => $data,
            ':turno' => $turno,
        ]);
        echo json_encode(['mensagem' => 'Turno inserido']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar turno: ' . $e->getMessage()]);
}
