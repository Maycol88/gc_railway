// File: usuarios/remover_user.php
<?php

require_once __DIR__ . '/../cors.php';
// Conexão com o banco
require_once("../config/db.php"); // Ajuste o caminho conforme sua estrutura


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: https://gcgit.vercel.app");
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
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // Pega o ID da URL
    $data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

    // Verifica se o ID foi fornecido
     if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "erro", "mensagem" => "ID não fornecido."]);
        exit();
    }

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "sucesso", "mensagem" => "Usuário removido com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "erro", "mensagem" => "Erro ao remover usuário."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "erro", "mensagem" => "ID não fornecido."]);
    }
}
?>
