<?php
require_once __DIR__ . '/../cors.php';
require_once '../config/db.php';
// Função auxiliar para converter data/hora no formato 'd/m/Y H:i:s' para timestamp
function parseDataHora($valor) {
    if (empty($valor)) return false;
    $dt = DateTime::createFromFormat('d/m/Y H:i:s', $valor);
    return $dt ? $dt->getTimestamp() : false;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Retornar unidades
    if (isset($_GET['nome_unidade'])) {
        try {
            $stmt = $pdo->query("SELECT id, nome_unidade FROM unidades ORDER BY nome_unidade");
            $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($unidades);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao buscar unidades", "detalhe" => $e->getMessage()]);
        }
        exit();
    }

    // Parâmetros de filtro
    $userId = $_GET['user_id'] ?? null;
    $mes = $_GET['mes'] ?? date("m");
    $ano = $_GET['ano'] ?? date("Y");

    try {
        if ($userId) {
            $stmt = $pdo->prepare("
                SELECT 
                    p.id,
                    p.entrada,
                    p.entrada_almoco,
                    p.saida_almoco,
                    p.saida,
                    p.tipo_jornada,
                    u.nome_unidade,
                    us.nome AS nome_usuario
                FROM ponto p
                JOIN unidades u ON u.id = p.unidade_id
                JOIN users us ON us.id = p.user_id
                WHERE p.user_id = ?
                  AND EXTRACT(MONTH FROM p.entrada) = ?
                  AND EXTRACT(YEAR FROM p.entrada) = ?
                ORDER BY p.entrada
            ");
            $stmt->execute([$userId, $mes, $ano]);
        } else {
            $stmt = $pdo->prepare("
                SELECT 
                    p.id,
                    p.entrada,
                    p.entrada_almoco,
                    p.saida_almoco,
                    p.saida,
                    p.tipo_jornada,
                    u.nome_unidade,
                    us.nome AS nome_usuario
                FROM ponto p
                JOIN unidades u ON u.id = p.unidade_id
                JOIN users us ON us.id = p.user_id
                WHERE EXTRACT(MONTH FROM p.entrada) = ?
                  AND EXTRACT(YEAR FROM p.entrada) = ?
                ORDER BY p.entrada
            ");
            $stmt->execute([$mes, $ano]);
        }

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as &$row) {
            // Formata os campos de data/hora
            foreach (['entrada', 'entrada_almoco', 'saida_almoco', 'saida'] as $campo) {
                if (!empty($row[$campo])) {
                    $dataHora = new DateTime($row[$campo]);
                    $row[$campo] = $dataHora->format('d/m/Y H:i:s');
                }
            }

            // Se nenhum horário foi preenchido
            if (empty($row['entrada']) && empty($row['saida']) && empty($row['entrada_almoco']) && empty($row['saida_almoco'])) {
                $row['horas_trabalhadas'] = null;
                continue;
            }

            // Convertendo para timestamp
            $entrada = parseDataHora($row['entrada']);
            $saida = parseDataHora($row['saida']);
            $entradaAlmoco = parseDataHora($row['entrada_almoco']);
            $saidaAlmoco = parseDataHora($row['saida_almoco']);

            // Corrige virada de dia
            if ($entrada && $saida && $saida <= $entrada) {
                $saida += 86400;
            }
            if ($entradaAlmoco && $saidaAlmoco && $saidaAlmoco <= $entradaAlmoco) {
                $saidaAlmoco += 86400;
            }

            // Calcula tempo de trabalho e almoço
            $tempoTrabalho = ($entrada && $saida) ? ($saida - $entrada) : 0;
            $tempoAlmoco = ($entradaAlmoco && $saidaAlmoco) ? ($saidaAlmoco - $entradaAlmoco) : 0;

            $horasTrabalhadas = ($tempoTrabalho - $tempoAlmoco) / 3600.0;
            $row['horas_trabalhadas'] = ($horasTrabalhadas >= 0) ? round($horasTrabalhadas, 2) : null;
        }

        echo json_encode($resultados);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao buscar registros", "detalhe" => $e->getMessage()]);
    }

    exit();
}

http_response_code(405);
ob_clean();
echo json_encode(["erro" => "Método HTTP não permitido"]);
