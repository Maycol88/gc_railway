<?php
require_once __DIR__ . '/../cors.php';
session_start();




$_SESSION = [];
session_destroy();

echo json_encode(["message" => "Sessão encerrada com sucesso"]);
