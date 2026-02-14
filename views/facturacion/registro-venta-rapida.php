<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-0">
                <div class="col-md-12">
                    <div class="m-0 text-dark text-center text-lg">
                        <i class="fas fa-bolt"></i>&nbsp;&nbsp;Venta Rápida
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content">
        <div class="container-fluid">
            <div style="max-width: 1140px; margin: 0 auto;">
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button type="button" id="btn-nueva-venta-rapida" class="btn btn-primary btn-block">
                            <i class="fa fa-plus fa-1x"></i>&nbsp;&nbsp;
                            <font>Nueva Venta Rápida</font>
                        </button>
                    </div>
                </div>

                <form id="FRM_INSERT_FACTURA_RAPIDA" method="post" action="<?php echo $functions->direct_sistema(); ?>/modules/facturacion/insert-factura-rapida.php" enctype="multipart/form-data">
                    
                    <!-- Card de información básica -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-info-circle"></i>&nbsp;&nbsp;Información de Venta
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Serie</label>
                                        <select class="form-control" name="vrapida_series" required>
                                            <option value="F001" selected>F001</option>
                                            <option value="F002">F002</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>N° Factura</label>
                                        <input type="text" class="form-control" name="vrapida_nro" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" name="vrapida_fecha" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Vendedor</label>
                                        <input type="hidden" name="vrapida_usuarioid">
                                        <select class="select2 form-control" name="vrapida_usuario" required></select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tipo de Moneda por defecto: MN -->
                                <input type="hidden" name="vrapida_tipmon" value="MN">
                                
                                <!-- Forma de Pago por defecto: Contado (0) -->
                                <input type="hidden" name="vrapida_formpago" value="0">
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Medio de Pago</label>
                                        <select name="vrapida_mediopago" class="form-control" required>
                                            <option value="EFECTIVO" selected>Efectivo / Contado</option>
                                            <option value="MERCADOPAGO">Mercado Pago</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos ocultos con valores por defecto -->
                            <input type="hidden" name="vrapida_cliruc" value="00000000000">
                            <input type="hidden" name="vrapida_clinom" value="Cliente Genérico">
                            <input type="hidden" name="vrapida_clidirecc" value="Sin dirección">
                        </div>
                    </div>

                    <!-- Card de productos -->
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-shopping-cart"></i>&nbsp;&nbsp;Agregar Productos
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Producto</label>
                                        <select class="form-control select2" name="vrapida_producto">
                                            <option value="" selected>Seleccione un producto</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="vrapida_nameprod">
                                <input type="hidden" name="vrapida_codeprod">
                                <input type="hidden" name="vrapida_proddesc">
                                <input type="hidden" name="vrapida_stockprod">

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Precio</label>
                                        <input type="number" step="0.01" class="form-control" name="vrapida_prodprecio" value="0.00" readonly>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>Cant.</label>
                                        <input type="number" min="1" class="form-control" name="vrapida_prodcant" value="1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" id="btn-add-prodtofactura-rapida" class="btn btn-success btn-block">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de productos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Haga doble clic sobre un producto para eliminarlo de la lista
                            </div>
                            
                            <div class="table-responsive">
                                <table id="table-products-rapida" class="table table-bordered table-hover table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Descripción</th>
                                            <th>P. Unit.</th>
                                            <th>Cant.</th>
                                            <th>Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- Totales -->
                            <div class="row mt-4">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <div class="row mb-2">
                                        <div class="col-md-6 text-right">
                                            <strong>Op. Gravada:</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" name="vrapida_opergrab" step="0.01" class="form-control text-right" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6 text-right">
                                            <strong class="text-lg">TOTAL:</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="vrapida_total" class="form-control text-right font-weight-bold" style="font-size: 1.2rem;" value="0.00" readonly required>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-2">
                                        <div class="col-md-6 text-right">
                                            <strong class="text-primary">Paga con:</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" name="vrapida_paga" step="0.01" min="0" class="form-control text-right" value="0.00" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6 text-right">
                                            <strong class="text-success">Vuelto:</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="vrapida_vuelto" class="form-control text-right font-weight-bold text-success" style="font-size: 1.1rem; background-color: #d4edda;" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón guardar -->
                            <div class="row mt-4">
                                <div id="col-btn-save-factura-rapida" class="col-md-12">
                                    <button type="submit" id="btn-save-factura-rapida" class="btn btn-success btn-lg btn-block">
                                        <i class="fa fa-save fa-1x"></i>&nbsp;&nbsp;
                                        <font><b>GRABAR VENTA RÁPIDA</b></font>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>