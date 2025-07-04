<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';


$raw = file_get_contents("php://input");
error_log("RAW INPUT: " . $raw);
$data = json_decode($raw, true);

if (
    !isset($data['user_id']) ||
    !isset($data['unidade_id']) ||
    !isset($data['acao'])
) {
    echo json_encode(['erro' => 'Parâmetros insuficientes']);
    exit;
}

$user_id = $data['user_id'];
$unidade_id = $data['unidade_id'];
$acao = $data['acao'];

try {
    if ($acao === 'adicionar') {
        // Insere vínculo, mas evita duplicata
        $stmt = $pdo->prepare("INSERT INTO unidades_user (user_id, unidade_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
        $stmt->execute([$user_id, $unidade_id]);
    } elseif ($acao === 'remover') {
        // Remove vínculo
        $stmt = $pdo->prepare("DELETE FROM unidades_user WHERE user_id = ? AND unidade_id = ?");
        $stmt->execute([$user_id, $unidade_id]);
    } else {
        echo json_encode(['erro' => 'Ação inválida']);
        exit;
    }

    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao atualizar vínculo: ' . $e->getMessage()]);
}
