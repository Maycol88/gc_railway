<?php
// editar_user.php
require_once '../config/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);

    $id = $input["id"] ?? null;
    $nome = $input["nome"] ?? null;

    if ($id && $nome) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET nome = :nome WHERE id = :id");
            $stmt->execute([':nome' => $nome, ':id' => $id]);
            echo json_encode(["status" => "sucesso", "mensagem" => "Usuário atualizado."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "erro", "mensagem" => "Erro ao atualizar: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "erro", "mensagem" => "Dados incompletos."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "erro", "mensagem" => "Método não permitido."]);
}
?>
