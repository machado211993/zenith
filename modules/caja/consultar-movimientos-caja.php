<?php
// modules/caja/consultar-movimientos-caja.php
session_start();

// Mover el header y el include al principio
header('Content-Type: application/json');
require_once '../../global/connection.php'; // Este archivo define $pdo

// Validar que se ha recibido el jornada_id
if (!isset($_GET['jornada_id']) || empty($_GET['jornada_id'])) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No se ha especificado una jornada para consultar.'
    ]);
    exit;
}

// Verificar que la conexión se estableció
if (!isset($pdo)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: No se pudo establecer la conexión con la base de datos.'
    ]);
    exit;
}

$jornada_id_consulta = $_GET['jornada_id'];
$response = [
    'exito' => false,
    'mensaje' => 'Ocurrió un error inesperado.',
    'data' => null
];

try {
    // 1. Obtener la lista de movimientos para la jornada
    $sql_mov = "SELECT 
                    mc.fecha, mc.tipo, mc.monto, mc.descripcion,
                    u.username AS usuario_nombre 
                FROM movimientos_caja mc 
                LEFT JOIN tbl_user u ON mc.usuario_id = u.id 
                WHERE mc.jornada_id = :jornada_id 
                ORDER BY mc.fecha DESC";

    $stmt_mov = $pdo->prepare($sql_mov);
    $stmt_mov->bindParam(':jornada_id', $jornada_id_consulta, PDO::PARAM_STR);
    $stmt_mov->execute();
    $movimientos = $stmt_mov->fetchAll(PDO::FETCH_ASSOC);

    // 2. Obtener el resumen (Totales) de la jornada
    $resumen = [
        'TotalIngreso' => 0.00,
        'TotalEgreso' => 0.00,
        'TotalCaja' => 0.00
    ];

    // Obtener el monto inicial de la tabla de jornadas
    $sql_inicial = "SELECT monto_inicial FROM caja_jornadas WHERE jornada_id = :jornada_id";
    $stmt_inicial = $pdo->prepare($sql_inicial);
    $stmt_inicial->bindParam(':jornada_id', $jornada_id_consulta, PDO::PARAM_STR);
    $stmt_inicial->execute();
    $monto_inicial = $stmt_inicial->fetchColumn();
    $monto_inicial = $monto_inicial ? (float)$monto_inicial : 0;

    // Calcular totales de movimientos de ingreso y egreso
    $sql_resumen = "SELECT 
        SUM(CASE WHEN tipo = 'INGRESO' THEN monto ELSE 0 END) AS TotalIngresosMov, 
        SUM(CASE WHEN tipo = 'EGRESO' THEN monto ELSE 0 END) AS TotalEgresosMov
    FROM movimientos_caja WHERE jornada_id = :jornada_id AND tipo IN ('INGRESO', 'EGRESO')";

    $stmt_res = $pdo->prepare($sql_resumen);
    $stmt_res->bindParam(':jornada_id', $jornada_id_consulta, PDO::PARAM_STR);
    $stmt_res->execute();
    $totales_mov = $stmt_res->fetch(PDO::FETCH_ASSOC);

    $ingresos_mov = $totales_mov ? (float)($totales_mov['TotalIngresosMov'] ?? 0) : 0;
    $egresos_mov = $totales_mov ? (float)($totales_mov['TotalEgresosMov'] ?? 0) : 0;

    $resumen['TotalIngreso'] = $monto_inicial + $ingresos_mov;
    $resumen['TotalEgreso'] = $egresos_mov;
    $resumen['TotalCaja'] = $resumen['TotalIngreso'] - $resumen['TotalEgreso'];
    
    // 3. Preparar la respuesta final
    $response = [
        'exito' => true,
        'mensaje' => 'Datos cargados correctamente.',
        'data' => [
            'jornada_id' => $jornada_id_consulta,
            'movimientos' => $movimientos,
            'resumen' => $resumen
        ]
    ];

} catch (PDOException $e) {
    $response['exito'] = false;
    $response['mensaje'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>