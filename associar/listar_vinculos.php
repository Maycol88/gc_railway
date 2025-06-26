<?php
require_once '../config/db.php';
// Permite acesso de qualquer origem (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);



if (!isset($_GET['user_id'])) {
    echo json_encode(['erro' => 'Parâmetro user_id ausente']);
    exit;
}

$user_id = $_GET['user_id'];

try {
    // Busca as unidades vinculadas ao usuário
    $stmt = $pdo->prepare("SELECT unidade_id FROM unidades_user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $vinculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($vinculos);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao buscar vínculos: ' . $e->getMessage()]);
}
