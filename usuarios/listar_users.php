// File: usuarios/listar_users.php
<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';


try {
    $stmt = $pdo->query("SELECT id, nome FROM users ORDER BY nome");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao listar usuários: ' . $e->getMessage()]);
}

