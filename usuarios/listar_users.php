<?php
// Conexão com o banco
require_once("../config/db.php"); // Ajuste o caminho conforme sua estrutura
// api/usuarios/listar_users.php

// Cabeçalhos CORS
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
// Incluir arquivo de configuração do banco de dados
header("Content-Type: application/json");



try {
    $stmt = $pdo->query("SELECT id, nome, role FROM users");

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
} catch (PDOException $e) {
    echo json_encode(["erro" => "Erro ao listar usuários: " . $e->getMessage()]);
    http_response_code(500);
}
