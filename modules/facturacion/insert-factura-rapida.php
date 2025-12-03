<?php
session_start();

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    require '../../global/connection.php';

    try {
        // Recibir datos del formulario
        $v_series = $_POST['vrapida_series'];
        $v_numero = $_POST['vrapida_nro'];
        $v_fecha = date("Y-m-d", strtotime($_POST['vrapida_fecha']));
        $v_seller_id = $_POST['vrapida_usuarioid'];
        
        $v_cli_ruc = trim($_POST['vrapida_cliruc']);
        $v_clinom = trim($_POST['vrapida_clinom']);
        $v_clidirecc = trim($_POST['vrapida_clidirecc']);
        $v_tipmon = $_POST['vrapida_tipmon'];
        $v_formpago = $_POST['vrapida_formpago'];
        
        $v_opergrab = $_POST['vrapida_opergrab'];
        $v_igv = $_POST['vrapida_igv'];
        $v_total = $_POST['vrapida_total'];
        
        $v_lst_prods = $_POST['vrapida_prods'];
        $v_lst_prods = json_decode($v_lst_prods);
        
        $v_user_id = $_SESSION['loggedInUser']['USERID'];
        $v_fecreg = date("Y-m-d H:i:s");
        
        // Estado: 1 = Vigente (venta rápida siempre es vigente)
        $v_estado = 1;
        
        // Cliente ID siempre 0 para venta rápida (cliente genérico)
        $v_cliente_id = 0;
        
        // Fecha de entrega = fecha de venta
        $v_fecha_entrega = $v_fecha;
        
        // Descuentos en 0 para venta rápida
        $v_desc_rate = 0;
        $v_desc_val = 0;
        
        // Quotation ID en 0 (no viene de cotización)
        $v_cotiz_id = 0;

        // Insertar factura
        $sqlStatement = $pdo->prepare("INSERT INTO tbl_invoice (
            series,
            number,
            status,
            quotation_id,
            customer_id,
            ruc,
            name,
            address,
            reference,
            payment_days,
            date,
            delivery_date,
            currency,
            discount_rate,
            discount_value,
            total_sub,
            total_tax,
            total_net,
            seller_id,
            user_id,
            registration_date,
            last_update
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$sqlStatement) {
            throw new Exception("Error preparando consulta");
        }

        $sqlStatement->execute([
            $v_series,
            $v_numero,
            $v_estado,
            $v_cotiz_id,
            $v_cliente_id,
            $v_cli_ruc,
            $v_clinom,
            $v_clidirecc,
            '', // referencia vacía
            $v_formpago,
            $v_fecha,
            $v_fecha_entrega,
            $v_tipmon,
            $v_desc_rate,
            $v_desc_val,
            $v_opergrab,
            $v_igv,
            $v_total,
            $v_seller_id,
            $v_user_id,
            $v_fecreg,
            $v_fecreg
        ]);

        // Obtener ID de factura insertada
        $LSTMAXID = $pdo->prepare("SELECT MAX(id) AS MAXID FROM tbl_invoice ORDER BY id DESC");
        $LSTMAXID->execute();
        $LMI = $LSTMAXID->fetch();
        $id_factura = $LMI["MAXID"];

        // Insertar detalle de productos
        if ($v_lst_prods != "" && count($v_lst_prods) > 0) {
            foreach ($v_lst_prods as $key => $value) {
                $id_prod = "";
                $cod_prod = "";
                $nom_prod = "";
                $desc_prod = "";
                $prec_prod = 0;
                $cant_prod = 0;
                $import_prod = 0;

                foreach ($value as $k => $v) {
                    switch ($k) {
                        case "0":
                            $id_prod = $v;
                            break;
                        case "1":
                            $cod_prod = $v;
                            break;
                        case "2":
                            $nom_prod = $v;
                            break;
                        case "3":
                            $desc_prod = $v;
                            break;
                        case "4":
                            $prec_prod = $v;
                            break;
                        case "5":
                            $cant_prod = $v;
                            break;
                        case "6":
                            $import_prod = $v;
                            break;
                    }
                }

                // Actualizar stock del producto
                $lstprodxid = $pdo->prepare("SELECT * FROM tbl_product WHERE id = ?");
                $lstprodxid->execute([$id_prod]);
                
                if ($lstprodxid->rowCount() > 0) {
                    $lpxi = $lstprodxid->fetch();
                    $stock_actual = $lpxi["stock_quantity"];
                    $new_stock = $stock_actual - $cant_prod;

                    // Actualizar stock
                    $update_producto = $pdo->prepare("UPDATE tbl_product SET stock_quantity = ? WHERE id = ?");
                    $update_producto->execute([$new_stock, $id_prod]);
                }

                // Insertar detalle de factura
                $sqlDetail = $pdo->prepare("INSERT INTO tbl_invoice_detail (
                    item_description,
                    item_id,
                    item_code,
                    item_name,
                    item_quantity,
                    item_unit_price,
                    invoice_id
                ) VALUES (?,?,?,?,?,?,?)");
                
                $sqlDetail->execute([
                    $desc_prod,
                    $id_prod,
                    $cod_prod,
                    $nom_prod,
                    $cant_prod,
                    $prec_prod,
                    $id_factura
                ]);
            }
        }

        echo "OK_INSERT";

    } catch (Exception $e) {
        error_log("Error en venta rápida: " . $e->getMessage());
        echo "ERROR";
    }

} else {
    echo "ERROR";
}
?>