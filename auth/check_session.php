<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['hash'])) {
    http_response_code(401);
    echo json_encode(["error" => "Sessão não autenticada"]);
    exit();
}

// Revalida com hash vinculada ao navegador
$hashEsperada = hash('sha256', $_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']);
if ($_SESSION['hash'] !== $hashEsperada) {
    session_destroy();
    http_response_code(401);
    echo json_encode(["error" => "Sessão inválida"]);
    exit();
}

// OK
echo json_encode([
    "usuario" => [
        "id" => $_SESSION['user_id'],
        "role" => $_SESSION['user_role'],
        "nome" => $_SESSION['user_nome']
    ]
]);
