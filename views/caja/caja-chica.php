<?php 
// Este archivo debe estar rodeado por tu plantilla main.php o similar
// Aquí va la lógica de PHP al inicio si es necesaria (ej. verificación de sesión)
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cash-register"></i> Caja Chica</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo $functions->direct_paginas()."home" ?>">Inicio</a></li>
                        <li class="breadcrumb-item active">Caja Chica</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Registro de Movimiento de Caja</h3>
            </div>
            <form id="formCajaChica">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="tipo_movimiento">Acción / Tipo de Movimiento</label>
                            <select id="tipo_movimiento" name="tipo_movimiento" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="INGRESO">Ingreso (Entrada de Dinero)</option>
                                <option value="EGRESO">Egreso (Salida de Dinero/Gasto)</option>
                                <option value="INICIO">Inicio de Caja</option>
                                <option value="CIERRE">Cierre de Caja</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="monto" id="label_monto">Monto $</label>
                            <input type="number" id="monto" name="monto" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                            <small id="monto_helper" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="metodo_pago">Forma de Pago</label>
                            <select id="metodo_pago" name="metodo_pago" class="form-control">
                                <option value="CONTADO">Efectivo / Contado</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="TARJETA">Tarjeta</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Concepto / Descripción del Movimiento</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Ej: Billete para cambio, Pago de flete, Retiro de efectivo..." required></textarea>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Movimiento</button>
                    <button type="reset" class="btn btn-default">Cancelar</button>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="<?php echo $functions->direct_sistema(); ?>/ajax/registro-caja.js"></script>