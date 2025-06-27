<?php
session_start();
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
