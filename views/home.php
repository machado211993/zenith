<?php
// --- Lógica para verificar estado de Caja ---
require_once __DIR__ . '/../global/connection.php';

$caja_estado = 'CERRADA';
$caja_color = 'danger'; // Rojo por defecto
$caja_icono = 'fa-lock';
$caja_mensaje = 'La caja se encuentra CERRADA. Debe abrirla para realizar operaciones.';
$caja_boton = 'Abrir Caja Ahora';

try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT estado FROM caja_jornadas ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && $resultado['estado'] === 'ABIERTA') {
            $caja_estado = 'ABIERTA';
            $caja_color = 'success'; // Verde
            $caja_icono = 'fa-cash-register';
            $caja_mensaje = 'La caja está ABIERTA y operativa.';
            $caja_boton = 'Ir a Caja';
        }
    }
} catch (Exception $e) {}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Panel de Control</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active">Panel Principal</li>
          </ol>
        </div> 
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      
      <div class="row mb-3">
        <div class="col-12">
            <div class="callout callout-info">
                <h5><i class="fas fa-user-circle"></i> ¡Hola, <?php echo $_SESSION['loggedInUser']['EMPLOYEE_NAME']; ?>!</h5>
                <p>Bienvenido al sistema Zenith. Aquí tienes los accesos rápidos para tu gestión diaria.</p>
            </div>

            <!-- ALERTA DE ESTADO DE CAJA -->
            <div class="alert alert-<?php echo $caja_color; ?> alert-dismissible">
                <h5><i class="icon fas <?php echo $caja_icono; ?>"></i> Estado de Caja: <b><?php echo $caja_estado; ?></b></h5>
                <p class="mb-0">
                    <?php echo $caja_mensaje; ?>
                    <a href="<?php echo $functions->direct_paginas()."caja/caja-chica" ?>" class="btn btn-light btn-sm text-<?php echo $caja_color; ?> font-weight-bold ml-3" style="text-decoration: none;">
                        <?php echo $caja_boton; ?> <i class="fas fa-arrow-right"></i>
                    </a>
                </p>
            </div>
        </div>
      </div>

      <div class="row">
        <!-- Venta Rápida (Prioridad Alta) -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>Venta Rápida</h3>
              <p>Nueva Venta (POS)</p>
            </div>
            <div class="icon">
              <i class="fas fa-cash-register"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."facturacion/registro-venta-rapida" ?>" class="small-box-footer">
              Ir a Venta Rápida <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Cta. Cte. (Nuevo Módulo) -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-primary">
            <div class="inner">
              <h3>Cta. Cte.</h3>
              <p>Gestión de Créditos</p>
            </div>
            <div class="icon">
              <i class="fas fa-hand-holding-usd"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."ctacte/gestion-ctacte" ?>" class="small-box-footer">
              Gestionar Cuentas <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Resumen de Facturas (Historial) -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>Historial</h3>
              <p>Resumen de Facturas</p>
            </div>
            <div class="icon">
              <i class="fas fa-file-invoice"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."facturacion/resumen-facturas" ?>" class="small-box-footer">
              Ver Movimientos <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Caja Chica -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h3>Caja Chica</h3>
              <p>Apertura / Cierre / Movimientos</p>
            </div>
            <div class="icon">
              <i class="fas fa-wallet"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."caja/caja-chica" ?>" class="small-box-footer">
              Gestionar Caja <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Caja Consulta -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>Consulta Caja</h3>
              <p>Ver Totales y Resumen</p>
            </div>
            <div class="icon">
              <i class="fas fa-chart-pie"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."caja/caja-consulta" ?>" class="small-box-footer">
              Ver Reporte <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Listado de Productos -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>Inventario</h3>
              <p>Listado de Productos</p>
            </div>
            <div class="icon">
              <i class="fas fa-boxes"></i>
            </div>
            <a href="<?php echo $functions->direct_paginas()."productos/listado-producto" ?>" class="small-box-footer">
              Ver Inventario <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- /.row -->

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->