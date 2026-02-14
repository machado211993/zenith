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

        <!-- Caja Chica -->
        <div class="col-lg-4 col-6">
          <div class="small-box bg-info">
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
      </div>
      <!-- /.row -->

      <!-- Accesos Secundarios -->
      <div class="row">
          <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-th"></i> Otros Accesos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <a href="<?php echo $functions->direct_paginas()."clientes/registro-cliente" ?>" class="btn btn-default btn-block text-left">
                                <i class="fas fa-users text-primary"></i>&nbsp; Clientes
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <a href="<?php echo $functions->direct_paginas()."productos/listado-producto" ?>" class="btn btn-default btn-block text-left">
                                <i class="fas fa-boxes text-success"></i>&nbsp; Productos
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <a href="<?php echo $functions->direct_paginas()."facturacion/registro-factura" ?>" class="btn btn-default btn-block text-left">
                                <i class="fas fa-file-invoice text-danger"></i>&nbsp; Facturación
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12 mb-2">
                            <a href="<?php echo $functions->direct_paginas()."facturacion/resumen-facturas" ?>" class="btn btn-default btn-block text-left">
                                <i class="fas fa-list-alt text-info"></i>&nbsp; Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
          </div>
      </div>

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->