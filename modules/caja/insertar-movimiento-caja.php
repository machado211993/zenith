<?php
// modules/caja/insertar-movimiento-caja.php
session_start();
require_once '../../global/connection.php';

header('Content-Type: application/json');

// --- Helper Functions ---

/**
 * Busca la única jornada que debería estar abierta.
 */
function getJornadaAbierta($pdo) {
    $sql = "SELECT * FROM caja_jornadas WHERE estado = 'ABIERTA' ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // Si hay más de una, es un estado de error, pero nos enfocamos en la última.
    return $result;
}

/**
 * Genera un nuevo ID de jornada secuencial y único.
 */
function generarNuevoJornadaId($pdo) {
    $sql = "SELECT jornada_id FROM caja_jornadas ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ultimo_id = $stmt->fetchColumn();

    if ($ultimo_id && preg_match('/^J-(\d+)$/', $ultimo_id, $matches)) {
        $numero = (int) $matches[1];
        $nuevo_numero = $numero + 1;
        return 'J-' . str_pad($nuevo_numero, 4, '0', STR_PAD_LEFT);
    } else {
        // Si no hay jornadas o el formato no coincide, empezamos de 1.
        return 'J-0001';
    }
}


// --- Main Logic ---

try {
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        throw new Exception("Acceso no permitido.");
    }
    
    if (!isset($pdo)) {
        throw new Exception("No se pudo establecer la conexión con la base de datos.");
    }

    $tipo_movimiento = $_POST['tipo_movimiento'] ?? '';
    $monto_str = str_replace(',', '.', ($_POST['monto'] ?? '0'));
    $monto = (float)$monto_str;
    $descripcion = $_POST['descripcion'] ?? 'Sin descripción';
    $metodo_pago = $_POST['metodo_pago'] ?? 'CONTADO';
    $usuario_id = $_SESSION['loggedInUser']['USERID'] ?? 0;
    
    if ($usuario_id == 0) throw new Exception("Usuario no autenticado. Por favor, inicie sesión de nuevo.");
    if (empty($tipo_movimiento)) throw new Exception("El tipo de movimiento es requerido.");

    $pdo->beginTransaction();

    switch ($tipo_movimiento) {
        case 'INICIO':
            if ($monto <= 0) throw new Exception("Para iniciar la caja, se requiere un monto inicial mayor a cero.");
            
            $jornada_abierta = getJornadaAbierta($pdo);
            if ($jornada_abierta) {
                throw new Exception("Ya existe una jornada abierta. No puede iniciar una nueva hasta cerrar la actual.");
            }

            // Generar el nuevo ID de jornada único
            $jornada_id_nuevo = generarNuevoJornadaId($pdo);

            // Crear la nueva jornada
            $sql_jornada = "INSERT INTO caja_jornadas (jornada_id, fecha_apertura, monto_inicial, estado, usuario_apertura_id) 
                           VALUES (?, NOW(), ?, 'ABIERTA', ?)";
            $stmt_jornada = $pdo->prepare($sql_jornada);
            $stmt_jornada->execute([$jornada_id_nuevo, $monto, $usuario_id]);

            // Insertar el movimiento de inicio
            $sql_mov = "INSERT INTO movimientos_caja (fecha, tipo, monto, descripcion, usuario_id, jornada_id, metodo_pago) 
                        VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([$tipo_movimiento, $monto, $descripcion, $usuario_id, $jornada_id_nuevo, 'N/A']);
            
            $mensaje_exito = "Caja iniciada correctamente con Jornada ID: $jornada_id_nuevo.";
            break;

        case 'INGRESO':
        case 'EGRESO':
            $jornada_abierta = getJornadaAbierta($pdo);
            if (!$jornada_abierta) throw new Exception("No hay una jornada de caja abierta. Debe iniciar la caja.");
            if ($monto <= 0) throw new Exception("El monto para un ingreso o egreso debe ser mayor a cero.");

            $jornada_id_actual = $jornada_abierta['jornada_id'];
            $sql_mov = "INSERT INTO movimientos_caja (fecha, tipo, monto, descripcion, usuario_id, jornada_id, metodo_pago) 
                        VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([$tipo_movimiento, $monto, $descripcion, $usuario_id, $jornada_id_actual, $metodo_pago]);
            
            $mensaje_exito = "Movimiento registrado correctamente.";
            break;

        case 'CIERRE':
            $jornada_abierta = getJornadaAbierta($pdo);
            if (!$jornada_abierta) throw new Exception("No se puede cerrar la caja porque no hay una jornada abierta.");

            $jornada_id_actual = $jornada_abierta['jornada_id'];
            $monto_final_real = $monto; 
            
            // Recalcular totales basados en la jornada correcta
            $sql_recalculo = "SELECT 
                                SUM(CASE WHEN tipo = 'INGRESO' THEN monto ELSE 0 END) AS TotalIngresosMov,
                                SUM(CASE WHEN tipo = 'EGRESO' THEN monto ELSE 0 END) AS TotalEgresosMov
                              FROM movimientos_caja 
                              WHERE jornada_id = ? AND tipo IN ('INGRESO', 'EGRESO')";
            $stmt_recalculo = $pdo->prepare($sql_recalculo);
            $stmt_recalculo->execute([$jornada_id_actual]);
            $totales_mov = $stmt_recalculo->fetch(PDO::FETCH_ASSOC);

            $monto_inicial = (float) $jornada_abierta['monto_inicial'];
            $ingresos_mov = (float)($totales_mov['TotalIngresosMov'] ?? 0);
            $egresos_mov = (float)($totales_mov['TotalEgresosMov'] ?? 0);

            $total_ingresos = $monto_inicial + $ingresos_mov;
            $total_egresos = $egresos_mov;
            $monto_final_esperado = $total_ingresos - $total_egresos;
            $diferencia = $monto_final_real - $monto_final_esperado;

            // Actualizar la jornada a 'CERRADA'
            $sql_cierre = "UPDATE caja_jornadas SET fecha_cierre = NOW(), total_ingresos = ?, total_egresos = ?, monto_final_esperado = ?, monto_final_real = ?, diferencia = ?, estado = 'CERRADA', usuario_cierre_id = ? WHERE id = ?";
            $stmt_cierre = $pdo->prepare($sql_cierre);
            $stmt_cierre->execute([$total_ingresos, $total_egresos, $monto_final_esperado, $monto_final_real, $diferencia, $usuario_id, $jornada_abierta['id']]);

            $mensaje_exito = "Jornada de caja cerrada correctamente.";
            break;
        
        default:
            throw new Exception("Tipo de movimiento no válido.");
    }

    $pdo->commit();
    echo json_encode(['exito' => true, 'mensaje' => $mensaje_exito]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['exito' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
?>
