<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    require '../../global/connection.php';

    $c_ruc = trim($_POST['nc_ruc']);
    $c_razsoc = trim($_POST['nc_nombre']);
    // Usar operador ternario para campos opcionales por si no vienen en el POST
    $c_direcc = isset($_POST['nc_direccion']) ? trim($_POST['nc_direccion']) : '';
    $c_telfij = isset($_POST['nc_telefono']) ? trim($_POST['nc_telefono']) : '';
    
    $c_fecreg = date("Y-m-d");
    
    // 1. Verificar si el cliente ya existe por RUC o Nombre
    $sqlCheck = $pdo->prepare("SELECT client_id FROM tbl_customer WHERE ruc=:ruc OR business_name=:razsoc");
    $sqlCheck->bindParam("ruc", $c_ruc, PDO::PARAM_STR);
    $sqlCheck->bindParam("razsoc", $c_razsoc, PDO::PARAM_STR);
    $sqlCheck->execute();
    
    if ($sqlCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El cliente ya existe en la base de datos.']);
    } else {
        // 2. Insertar el nuevo cliente
        // Se llenan los campos obligatorios y se dejan vacíos los que no se piden en el modal rápido
        $sqlInsert = $pdo->prepare("INSERT INTO tbl_customer (ruc, business_name, trade_name, email, phone, cellphone, address, department_id, province_id, district_id, contact1_name, contact1_phone, contact2_name, contact2_phone, commission, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Valores por defecto para campos no capturados en el modal
        $c_nomcom = $c_razsoc; // Usamos la razón social como nombre comercial por defecto
        $c_email = '';
        $c_cellphone = '';
        $c_dept = 0;
        $c_prov = 0;
        $c_dist = 0;
        $c_cont1 = '';
        $c_cont1_phone = '';
        $c_cont2 = '';
        $c_cont2_phone = '';
        $c_comm = 0;
        
        if ($sqlInsert->execute([$c_ruc, $c_razsoc, $c_nomcom, $c_email, $c_telfij, $c_cellphone, $c_direcc, $c_dept, $c_prov, $c_dist, $c_cont1, $c_cont1_phone, $c_cont2, $c_cont2_phone, $c_comm, $c_fecreg])) {
            $lastId = $pdo->lastInsertId();
            echo json_encode(['status' => 'success', 'id' => $lastId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al insertar en la base de datos.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no permitido.']);
}
?>