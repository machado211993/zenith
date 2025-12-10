<?php
// modules/caja/consultar-estado-caja.php
session_start();
require_once '../../global/connection.php';

header('Content-Type: application/json');

// Verificar que la conexión se estableció
if (!isset($pdo)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: No se pudo establecer la conexión con la base de datos.'
    ]);
    exit;
}

try {
    $usuario_id = $_SESSION['loggedInUser']['USERID'] ?? 0;

    if ($usuario_id == 0) {
        throw new Exception("Usuario no autenticado.");
    }

    // Correcto: Buscar la última jornada registrada por ID para obtener su estado real.
    $sql = "SELECT estado, monto_inicial FROM caja_jornadas ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $jornada = $stmt->fetch(PDO::FETCH_ASSOC);

    $estado = 'NO_INICIADA'; // Estado por defecto si la tabla está vacía
    $monto_inicial = 0;

    if ($jornada) { // Si se encontró al menos una jornada...
        if ($jornada['estado'] === 'ABIERTA') {
            $estado = 'ABIERTA';
            $monto_inicial = (float)$jornada['monto_inicial'];
        } else {
            // Si la última jornada está CERRADA, el estado general es que se puede iniciar una nueva.
            $estado = 'CERRADA';
        }
    }

    echo json_encode([
        'exito' => true,
        'estado' => $estado,
        'monto_inicial' => $monto_inicial
    ]);

} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>