<?php
header('Content-Type: application/json');
require_once "conexion.php";

// Verifica si se seleccionó al menos un ciclo
if (!isset($_GET['ciclo_id']) || empty($_GET['ciclo_id'])) {
    echo json_encode([]);
    exit();
}

$ciclo_id = intval($_GET['ciclo_id']);

try {
    $pdoStatement = $pdo->prepare("
        SELECT DISTINCT 
            m.id_modulo,
            m.name as nombre,
            m.curso,
            CONCAT(m.curso, ' - ', m.name) as codigo
        FROM modulo m
        INNER JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo
        WHERE ctm.id_ciclo = ?
        ORDER BY m.curso, m.name
    ");

    $pdoStatement->execute([$ciclo_id]);
    $modulos = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

    if (empty($modulos)) {
        error_log("No se encontraron módulos para el ciclo_id: " . $ciclo_id);
    }

    echo json_encode($modulos);
} catch (PDOException $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los módulos']);
}