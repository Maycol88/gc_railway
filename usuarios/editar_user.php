// File: usuarios/editar_user.php
<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    exit;
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
