<?php
// modules/caja/listar-jornadas.php

// Mover el header al principio para asegurar que se envíe antes que cualquier otra cosa.
header('Content-Type: application/json');

require_once '../../global/connection.php'; // Este archivo define $pdo

$response = array(
    'success' => false,
    'jornadas' => []
);

// El objeto $pdo se crea en connection.php. Si falla, connection.php ya imprime un error.
// Aquí verificamos si la variable existe para proceder.
if (!isset($pdo)) {
    $response['error'] = 'Error: No se pudo establecer la conexión con la base de datos.';
    echo json_encode($response);
    exit;
}

try {
    $query = "SELECT id, jornada_id, fecha_apertura, estado FROM caja_jornadas ORDER BY fecha_apertura DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $jornadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si hay jornadas o no, la consulta fue exitosa.
    $response['success'] = true;
    $response['jornadas'] = $jornadas;

} catch (PDOException $e) {
    // Si hay un error, lo capturamos y lo devolvemos como JSON
    $response['success'] = false;
    $response['error'] = 'Error al consultar las jornadas: ' . $e->getMessage();
    // Sobrescribimos jornadas para no enviar data parcial
    $response['jornadas'] = []; 
}

echo json_encode($response);

?>
