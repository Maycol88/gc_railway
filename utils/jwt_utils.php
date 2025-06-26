<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once '../vendor/autoload.php';
use \Firebase\JWT\JWT;

function gerarJWT($id, $role) {
    $key = "secreta";
    $payload = [
        "user_id" => $id,
        "role" => $role,
        "exp" => time() + (60*60*2)
    ];
    return JWT::encode($payload, $key, 'HS256');
}
function validarJWT($token) {
    $key = "secreta";
    try {
        return JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
    } catch (Exception $e) {
        error_log("Erro ao validar JWT: " . $e->getMessage());
        return false;
    }
}
function obterDadosUsuario($token) {
    $payload = validarJWT($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido ou expirado"]);
    exit();
}

$userId = $payload->user_id ?? null;
if (!$userId) {
    http_response_code(401);    
    return [
        "id" => $payload->user_id,
        "role" => $payload->role
    ];
}
    return null;
}   