<?php
require '../../global/connection.php';

$search = $_POST['search'] ?? '';

// Consulta agrupada por cliente para obtener totales
$sql = "SELECT 
            c.client_id, 
            c.business_name, 
            c.ruc, 
            c.phone,
            SUM(CASE WHEN m.tipo = 'DEUDA' THEN m.monto ELSE 0 END) as total_deuda,
            SUM(CASE WHEN m.tipo = 'PAGO' THEN m.monto ELSE 0 END) as total_pagado
        FROM cta_cte_movimientos m
        INNER JOIN tbl_customer c ON m.cliente_id = c.client_id
        GROUP BY c.client_id, c.business_name, c.ruc, c.phone";

// Si hay búsqueda, filtramos (implementación básica, idealmente se hace en el WHERE)
if (!empty($search)) {
    $sql = "SELECT 
            c.client_id, 
            c.business_name, 
            c.ruc, 
            c.phone,
            SUM(CASE WHEN m.tipo = 'DEUDA' THEN m.monto ELSE 0 END) as total_deuda,
            SUM(CASE WHEN m.tipo = 'PAGO' THEN m.monto ELSE 0 END) as total_pagado
        FROM cta_cte_movimientos m
        INNER JOIN tbl_customer c ON m.cliente_id = c.client_id
        WHERE c.business_name LIKE :search OR c.ruc LIKE :search
        GROUP BY c.client_id, c.business_name, c.ruc, c.phone";
}

$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolvemos JSON directo
echo json_encode($results);
?>
