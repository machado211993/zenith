<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-hand-holding-usd"></i> Gestión de Cuentas Corrientes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Cta. Cte.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Estado de Cuentas por Cliente</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Buscar cliente...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>RUC/DNI</th>
                                        <th>Teléfono</th>
                                        <th class="text-right">Deuda Total</th>
                                        <th class="text-right">Pagado</th>
                                        <th class="text-right">Saldo Pendiente</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_ctacte_body">
                                    <!-- Aquí se cargarían los datos vía AJAX o PHP -->
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Seleccione un cliente o utilice el buscador para ver detalles.
                                            <br>
                                            <small>(Funcionalidad de listado pendiente de implementación backend)</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo $functions->direct_sistema(); ?>/ajax/gestion-ctacte.js"></script>