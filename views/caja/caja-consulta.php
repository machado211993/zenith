<?php 
// Este archivo debe estar rodeado por tu plantilla main.php o similar
// Aquí va la lógica de PHP al inicio si es necesaria (ej. verificación de sesión)
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-search-dollar"></i> Consulta de Caja</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo $functions->direct_paginas()."home" ?>">Inicio</a></li>
                        <li class="breadcrumb-item active">Caja Consulta</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="jornada_selector">Seleccionar Jornada:</label>
                        <select id="jornada_selector" class="form-control">
                            <!-- Opciones de jornadas se cargarán aquí -->
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <h4 class="mb-3">Jornada Actual: <span id="jornada_actual" class="badge badge-info">Cargando...</span></h4>
                </div>
                
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="total_ingreso">$ 0.00</h3>
                            <p>Total Ingresos</p>
                        </div>
                        <div class="icon"><i class="fas fa-arrow-circle-up"></i></div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="total_egreso">$ 0.00</h3>
                            <p>Total Egresos (Gastos/Retiros)</p>
                        </div>
                        <div class="icon"><i class="fas fa-arrow-circle-down"></i></div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="total_caja">$ 0.00</h3>
                            <p>Efectivo en Caja</p>
                        </div>
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="total_mercadopago">$ 0.00</h3>
                            <p>Mercado Pago</p>
                        </div>
                        <div class="icon"><i class="fas fa-mobile-alt"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="total_ctacte" style="color: white;">$ 0.00</h3>
                            <p style="color: white;">Ventas Cta. Cte.</p>
                        </div>
                        <div class="icon"><i class="fas fa-mobile-alt"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalle de Movimientos del Día</h3>
                </div>
                <div class="card-body">
                    <table id="tabla_movimientos_caja" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5">Cargando movimientos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo $functions->direct_sistema(); ?>/ajax/resumen-caja.js"></script>