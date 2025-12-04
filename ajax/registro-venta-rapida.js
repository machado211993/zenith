// Ocultar botones de estado al inicio
$("#col-btn-anular-factura-rapida").hide();
$("#col-btn-pendiente-factura-rapida").hide();
$("#col-btn-cancelar-factura-rapida").hide();

$("#btn-save-factura-rapida").prop("disabled", true);
$("#btn-add-prodtofactura-rapida").prop("disabled", true);
$('input[name="vrapida_prodcant"]').prop("disabled", true);

$(document).ready(function () {
  $("#m_registro_venta_rapida").attr("class", "nav-link active");
  $("#m_facturacion").attr("class", "nav-link active");
  $("#m_facturacion").parent().attr("class", "nav-item has-treeview menu-open");
  $(document).prop("title", "Venta Rápida - Zenith Group");
});

// Cargar serie y correlativo automáticamente
buscarCorrelativoRapido();

// Listar usuarios vendedores
$.post("../../modules/usuarios/listar-usuarios-xtipo.php", function (data) {
  mydata = JSON.parse(data);
  data_users = mydata[0];
  user_id = mydata[1];
  user_job = mydata[2];

  $('select[name="vrapida_usuario"]').empty();
  $('select[name="vrapida_usuario"]').select2({
    data: data_users,
  });

  $('select[name="vrapida_usuario"]').val(user_id);
  $('select[name="vrapida_usuario"]').trigger("change");
  $('input[name="vrapida_usuarioid"]').val(user_id);

  if (user_job != "Secretaria" && user_job != "Secretario") {
    $('select[name="vrapida_usuario"]').prop("disabled", true);
  }
});

$('select[name="vrapida_usuario"]').on("change", function () {
  $('input[name="vrapida_usuarioid"]').val($(this).val());
});

// Listar productos
$.post(
  "../../modules/productos/listar-productos-xprov.php",
  { ESTADO: 1 },
  function (data) {
    $('select[name="vrapida_producto"]').empty();
    $('select[name="vrapida_producto"]').select2({
      data: JSON.parse(data),
    });
  }
);

// Al seleccionar producto
$('select[name="vrapida_producto"]').on("change", function () {
  DATA_ID = $(this).val();
  $('input[name="vrapida_prodcant"]').val(1);
  $('input[name="vrapida_nameprod"]').val("");
  $('input[name="vrapida_proddesc"]').val("");
  $('input[name="vrapida_prodprecio"]').val("");
  $('input[name="vrapida_stockprod"]').val("");
  $('input[name="vrapida_codeprod"]').val("");

  if (DATA_ID != "" && DATA_ID != null) {
    $('input[name="vrapida_prodcant"]').prop("disabled", false);
    $.post(
      "../../modules/productos/consultar-productos.php",
      { FILTER: DATA_ID, ESTADO: "1" },
      function (data) {
        var mydata = JSON.parse(data);
        stock_producto = parseInt(mydata[0]["CANTIDAD"]);
        $('input[name="vrapida_codeprod"]').val(mydata[0]["CODPROD"]);
        $('input[name="vrapida_nameprod"]').val(mydata[0]["NOMBRE"]);
        $('input[name="vrapida_proddesc"]').val(mydata[0]["DESCRIPTION"]);
        $('input[name="vrapida_prodprecio"]').val(mydata[0]["PRECIO"]);
        $('input[name="vrapida_stockprod"]').val(mydata[0]["CANTIDAD"]);

        if (stock_producto <= 0) {
          $.Notification.notify(
            "error",
            "bottom-right",
            "Stock agotado",
            "Producto seleccionado no cuenta con existencias"
          );
          $("#btn-add-prodtofactura-rapida").prop("disabled", true);
        } else {
          $("#btn-add-prodtofactura-rapida").prop("disabled", false);
        }
      }
    );
  } else {
    $('input[name="vrapida_prodcant"]').prop("disabled", true);
  }
});

// Validar cantidad
$('input[name="vrapida_prodcant"]').on("change", function () {
  cant_prod = parseInt($(this).val());
  stock_prod = parseInt($('input[name="vrapida_stockprod"]').val());
  select_prod = $('select[name="vrapida_producto"]').val();

  if (cant_prod <= stock_prod && cant_prod > 0) {
    tbl_data = tbl_prodrapida.rows().data().toArray();
    var cantidad_final = cant_prod;

    if (tbl_data.length > 0) {
      for (i = 0; i < tbl_data.length; i++) {
        id_prod = tbl_data[i][0];
        cant_agreg = parseInt(tbl_data[i][5]);
        if (select_prod == id_prod) {
          cantidad_final += cant_agreg;
        }
      }

      if (cantidad_final > stock_prod) {
        $("#btn-add-prodtofactura-rapida").prop("disabled", true);
        $.Notification.notify(
          "error",
          "bottom-right",
          "Stock insuficiente",
          "Producto no cuenta con stock suficiente"
        );
      } else {
        $("#btn-add-prodtofactura-rapida").prop("disabled", false);
      }
    } else {
      $("#btn-add-prodtofactura-rapida").prop("disabled", false);
    }
  } else {
    $("#btn-add-prodtofactura-rapida").prop("disabled", true);
    $.Notification.notify(
      "error",
      "bottom-right",
      "Stock insuficiente",
      "Producto no cuenta con stock suficiente"
    );
  }
});

// Tabla de productos
var tbl_prodrapida = $("#table-products-rapida").DataTable({
  language: { url: "../../plugins/datatables/Spanish.json" },
  paging: false,
  searching: false,
  info: false,
});

var total_temporal = 0;
tbl_prodrapida.columns([0]).visible(false);

// Agregar producto a la tabla
$("#btn-add-prodtofactura-rapida").click(function () {
  idprod = $('select[name="vrapida_producto"]').val();
  cod_prod = $('input[name="vrapida_codeprod"]').val();
  producto = $('input[name="vrapida_nameprod"]').val();
  descripcion = $('input[name="vrapida_proddesc"]').val();
  precio = parseFloat($('input[name="vrapida_prodprecio"]').val());
  cantidad = parseInt($('input[name="vrapida_prodcant"]').val());
  importe = precio * cantidad;
  var importe_actual = importe;

  if (idprod != "" && cantidad != "" && cantidad > 0) {
    $("#btn-add-prodtofactura-rapida").prop("disabled", true);

    tbl_prodrapida
      .rows(function (idx, data, node) {
        old_importe = data[6];
        old_cantidad = parseInt(data[5]);
        if (data[2] === producto) {
          importe += old_importe;
          cantidad += old_cantidad;
        }
        return data[2] === producto;
      })
      .remove()
      .draw();

    tbl_prodrapida.rows
      .add([
        {
          0: idprod,
          1: cod_prod,
          2: producto,
          3: descripcion,
          4: precio.toFixed(2),
          5: cantidad,
          6: importe.toFixed(2),
        },
      ])
      .draw();

    tbl_data = tbl_prodrapida.rows().data().toArray();

    opergrab =
      $('input[name="vrapida_opergrab"]').val() != ""
        ? $('input[name="vrapida_opergrab"]').val()
        : 0;
    importe_totactual = parseFloat(opergrab);
    importe_totactual += importe_actual;
    new_igv = importe_totactual * 0.18;
    new_total = importe_totactual + new_igv;

    total_temporal = new_total;

    $('input[name="vrapida_opergrab"]').val(importe_totactual.toFixed(2));
    $('input[name="vrapida_igv"]').val(new_igv.toFixed(2));
    $('input[name="vrapida_total"]').val(new_total.toFixed(2));

    // Recalcular vuelto si ya hay un monto ingresado
    $('input[name="vrapida_paga"]').trigger("change");

    $('input[name="vrapida_prodcant"]').val(1);
    $('select[name="vrapida_producto"]').val("").trigger("change");

    $.Notification.notify(
      "success",
      "bottom-right",
      "Producto añadido",
      "El producto ha sido agregado correctamente"
    );

    if (tbl_data.length > 0) {
      $("#btn-save-factura-rapida").prop("disabled", false);
    } else {
      $("#btn-save-factura-rapida").prop("disabled", true);
      total_temporal = 0;
    }
  } else {
    $('select[name="vrapida_producto"]').focus();
    $.Notification.notify(
      "error",
      "bottom-right",
      "Error al añadir",
      "Seleccione un producto de la lista"
    );
  }
});

// Eliminar producto de la tabla (doble clic)
$("#table-products-rapida").on("dblclick", "tr", function () {
  var data_row = tbl_prodrapida.row(this).data();
  if (!data_row) return;

  var row_id = data_row[0];
  var importe_prod = data_row[6];

  opergrab =
    $('input[name="vrapida_opergrab"]').val() != ""
      ? $('input[name="vrapida_opergrab"]').val()
      : 0;
  importe_totactual = parseFloat(opergrab);
  importe_totactual -= importe_prod;
  new_igv = importe_totactual * 0.18;
  new_total = importe_totactual + new_igv;

  total_temporal = new_total;

  $('input[name="vrapida_opergrab"]').val(importe_totactual.toFixed(2));
  $('input[name="vrapida_igv"]').val(new_igv.toFixed(2));
  $('input[name="vrapida_total"]').val(new_total.toFixed(2));

  // Recalcular vuelto si ya hay un monto ingresado
  $('input[name="vrapida_paga"]').trigger("change");

  tbl_prodrapida.rows(tbl_prodrapida.row(this)).remove().draw();

  tbl_data = tbl_prodrapida.rows().data().toArray();

  $('input[name="vrapida_prodcant"]').val(1);
  $("#btn-add-prodtofactura-rapida").prop("disabled", true);

  $.Notification.notify(
    "success",
    "bottom-right",
    "Producto eliminado",
    "El producto ha sido eliminado correctamente"
  );

  if (tbl_data.length == 0) {
    $("#btn-save-factura-rapida").prop("disabled", true);
    total_temporal = 0;
  }
});

// Calcular vuelto automáticamente
$('input[name="vrapida_paga"]').on("input change", function () {
  var paga = parseFloat($(this).val()) || 0;
  var total = parseFloat($('input[name="vrapida_total"]').val()) || 0;
  var vuelto = paga - total;

  if (vuelto < 0) {
    vuelto = 0;
  }

  $('input[name="vrapida_vuelto"]').val(vuelto.toFixed(2));

  // Cambiar color si hay vuelto
  if (vuelto > 0) {
    $('input[name="vrapida_vuelto"]').css("background-color", "#d4edda");
  } else {
    $('input[name="vrapida_vuelto"]').css("background-color", "#fff");
  }
});

// Guardar venta rápida
$("#FRM_INSERT_FACTURA_RAPIDA").submit(function (e) {
  e.preventDefault();
  var form = $(this);
  var idform = form.attr("id");
  var url = form.attr("action");
  tbl_data = tbl_prodrapida.rows().data().toArray();

  if (tbl_data.length == 0) {
    $.Notification.notify(
      "error",
      "bottom-right",
      "Sin productos",
      "Debe agregar al menos un producto"
    );
    return;
  }

  var formElement = document.getElementById(idform);
  var formData_rec = new FormData(formElement);
  formData_rec.append("vrapida_prods", JSON.stringify(tbl_data));

  $.ajax({
    type: "POST",
    url: url,
    data: formData_rec,
    contentType: false,
    cache: false,
    processData: false,
    beforeSend: function () {
      Swal.fire({
        html: "<h4>Guardando venta rápida</h4>",
        allowOutsideClick: false,
        onBeforeOpen: () => {
          Swal.showLoading();
        },
      });
    },
    success: function (data) {
      var response = $.trim(data);
      if (response == "ERROR_CAJA_CERRADA") {
        $.Notification.notify(
          "error",
          "top center",
          "Error: Caja Cerrada",
          "No se puede registrar la venta porque la caja está cerrada. Por favor, inicie una jornada de caja."
        );
        Swal.close();
        return;
      }
      if (response == "ERROR") {
        $.Notification.notify(
          "error",
          "bottom-right",
          "Error de guardado",
          "No se pudo guardar la venta"
        );
        Swal.close();
      } else if (response == "OK_INSERT") {
        $.Notification.notify(
          "success",
          "bottom-right",
          "Venta guardada",
          "Venta rápida registrada correctamente"
        );

        Swal.close();

        // Limpiar formulario
        setTimeout(function () {
          location.reload();
        }, 1000);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      Swal.close(); // ¡Importante! Cerrar el diálogo de carga
      alert('La petición AJAX falló. Razón: ' + textStatus + '\nError: ' + errorThrown);
      console.log(jqXHR);
    }
  });
});

// Botón nueva venta
$("#btn-nueva-venta-rapida").click(function (e) {
  e.preventDefault();
  location.reload();
});

// Función para buscar correlativo
function buscarCorrelativoRapido() {
  serieFactura = $('select[name="vrapida_series"]').val();

  $.post(
    "../../modules/facturacion/obtener-correlativo-doc.php",
    { TIPO_DOC: "INVOICE", SERIE: serieFactura },
    function (data) {
      if (data != "" && data != null) {
        $('input[name="vrapida_nro"]').val(data);
      }
    }
  );
}

$('select[name="vrapida_series"]').change(function () {
  buscarCorrelativoRapido();
});
