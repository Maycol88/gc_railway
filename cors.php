<?php
// Permite requisições do domínio frontend (coloque sua URL do Vercel aqui)
header("Access-Control-Allow-Origin: https://gcgit.vercel.app");

// Permite os métodos HTTP usados pela sua API
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Permite os headers que sua aplicação vai usar
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Permite enviar cookies ou credenciais (se usar)
// header("Access-Control-Allow-Credentials: true");

// Responde a requisições OPTIONS (preflight) imediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
