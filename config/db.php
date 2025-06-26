<?php
$host = "localhost";
$port = "5432";
$db_name = "Sys_escala";
$username = "postgres";
$password = "12032016";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erro ao conectar: " . $e->getMessage()]);
    exit();
}
// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo'); 