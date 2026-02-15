<?php
require '../../global/connection.php';

// Consulta para obtener todos los clientes ordenados por nombre
$sqlStatement = $pdo->prepare("SELECT * FROM tbl_customer ORDER BY business_name ASC");
$sqlStatement->execute();
$rowsNumber = $sqlStatement->rowCount();
$DATA = array();

if ($rowsNumber > 0) {
    while ($LST = $sqlStatement->fetch()) {
        $ID_CLI = $LST["client_id"];
        $NOM_CLI = $LST["business_name"];
        $RUC_CLI = $LST["ruc"];
        $DIR_CLI = $LST["address"];
        
        $ROW = [
            "id" => $ID_CLI,
            "text" => $NOM_CLI . " - " . $RUC_CLI, // Formato visual para el Select2
            "ruc" => $RUC_CLI,
            "address" => $DIR_CLI
        ];
        array_push($DATA, $ROW);
    }
}

echo json_encode($DATA);
?>