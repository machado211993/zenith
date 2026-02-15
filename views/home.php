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
        <div class="col-lg-6 col-6">
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
        <div class="col-lg-6 col-6">
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
      </div>
      <!-- /.row -->

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->