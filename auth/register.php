<?php
require_once '../config/db.php';
require_once '../email/email.php'; // aqui importamos a função

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data["nome"]) ||
    empty($data["cpf"]) ||
    empty($data["email"]) ||
    empty($data["role"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos ou incompletos"]);
    exit();
}

$nome = $data["nome"];
$cpf = $data["cpf"];
$email = $data["email"];
$role = $data["role"];

function gerarSenha($tamanho = 8) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%';
    return substr(str_shuffle($caracteres), 0, $tamanho);
}

$senhaTemporaria = gerarSenha();
$senhaHash = password_hash($senhaTemporaria, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO users (nome, cpf, email, senha, role, senha_temporaria)
        VALUES (?, ?, ?, ?, ?, TRUE)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $cpf, $email, $senhaHash, $role]);

    $id = $pdo->lastInsertId();

    // Busca nome e e-mail confirmando
    $stmt = $pdo->prepare("SELECT nome, email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && enviar_email_senha($user['email'], $user['nome'], $senhaTemporaria)) {
        http_response_code(201);
        echo json_encode(["message" => "Usuário cadastrado e e-mail enviado com sucesso"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Usuário criado, mas falha ao enviar e-mail"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao cadastrar usuário: " . $e->getMessage()]);
}
$pdo = null; // Fecha a conexão com o banco de dados
exit();