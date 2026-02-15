<?php
session_start();
require '../../global/connection.php';

header('Content-Type: application/json');

if (empty($_SESSION['loggedInUser']['USERID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión expirada']);
    exit;
}

$cliente_id = $_POST['pago_cliente_id'];
$monto = floatval($_POST['pago_monto']);
$metodo = $_POST['pago_metodo'];
$descripcion = trim($_POST['pago_descripcion']);
$usuario_id = $_SESSION['loggedInUser']['USERID'];

if ($monto <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'El monto debe ser mayor a 0']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Verificar Caja Abierta
    $stmtCaja = $pdo->prepare("SELECT jornada_id FROM caja_jornadas WHERE estado = 'ABIERTA' ORDER BY id DESC LIMIT 1");
    $stmtCaja->execute();
    $jornada = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$jornada) {
        throw new Exception("No hay una jornada de caja abierta. Abra la caja antes de recibir pagos.");
    }
    $jornada_id = $jornada['jornada_id'];

    // 2. Registrar en Cta. Cte. (Tipo PAGO)
    $desc_ctacte = "Abono en " . $metodo . ($descripcion ? ": " . $descripcion : "");
    $sqlCC = "INSERT INTO cta_cte_movimientos (cliente_id, fecha, tipo, monto, descripcion) VALUES (?, NOW(), 'PAGO', ?, ?)";
    $stmtCC = $pdo->prepare($sqlCC);
    $stmtCC->execute([$cliente_id, $monto, $desc_ctacte]);

    // 3. Registrar en Caja (Tipo INGRESO)
    // Si es TRANSFERENCIA, se registra igual como INGRESO para historial, 
    // pero el reporte de caja lo separará del efectivo físico.
    $desc_caja = "Cobro Cta. Cte. Cliente ID: " . $cliente_id . ($descripcion ? " - " . $descripcion : "");
    
    $sqlMov = "INSERT INTO movimientos_caja (fecha, tipo, monto, descripcion, usuario_id, jornada_id, metodo_pago) 
               VALUES (NOW(), 'INGRESO', ?, ?, ?, ?, ?)";
    $stmtMov = $pdo->prepare($sqlMov);
    $stmtMov->execute([$monto, $desc_caja, $usuario_id, $jornada_id, $metodo]);

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Abono registrado correctamente']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>