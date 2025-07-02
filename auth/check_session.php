<?php
require_once __DIR__ . '/../cors.php';
session_start();


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
