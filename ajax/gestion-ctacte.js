$(document).ready(function () {
  // Cargar lista al iniciar
  listarCuentas();

  // Funcionalidad de búsqueda
  $('input[name="table_search"]').on("keyup", function () {
    listarCuentas($(this).val());
  });
});

function listarCuentas(search = "") {
  $.post(
    "../../modules/clientes/listar-estados-cuenta.php",
    { search: search },
    function (data) {
      let accounts = [];
      try {
        accounts = JSON.parse(data);
      } catch (e) {
        console.error("Error parsing JSON", e);
        return;
      }

      let tbody = $("#tabla_ctacte_body");
      tbody.empty();

      if (accounts.length > 0) {
        accounts.forEach((acc) => {
          let deuda = parseFloat(acc.total_deuda);
          let pagado = parseFloat(acc.total_pagado);
          let saldo = deuda - pagado;

          // Color rojo si debe, verde si está al día
          let colorSaldo = saldo > 0 ? "text-danger" : "text-success";

          let row = `<tr>
                    <td>${acc.client_id}</td>
                    <td>${acc.business_name}</td>
                    <td>${acc.ruc}</td>
                    <td>${acc.phone || "-"}</td>
                    <td class="text-right">$ ${deuda.toFixed(2)}</td>
                    <td class="text-right">$ ${pagado.toFixed(2)}</td>
                    <td class="text-right font-weight-bold ${colorSaldo}">$ ${saldo.toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info" onclick="verDetalle(${acc.client_id})" title="Ver Detalle"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-success" onclick="registrarPago(${acc.client_id}, '${acc.business_name.replace(/'/g, "\\'")}')" title="Registrar Pago"><i class="fas fa-money-bill-wave"></i></button>
                    </td>
                </tr>`;
          tbody.append(row);
        });
      } else {
        tbody.append(
          '<tr><td colspan="8" class="text-center">No se encontraron cuentas corrientes con movimientos.</td></tr>',
        );
      }
    },
  );
}

// Funciones placeholder para los botones (se implementarán luego)
function verDetalle(id) {
  alert("Aquí se mostrará el detalle de movimientos del cliente ID: " + id);
}

function registrarPago(id, nombre) {
  $("#formRegistrarPago")[0].reset();
  $("#pago_cliente_id").val(id);
  $("#pago_cliente_nombre").val(nombre);
  $("#modalRegistrarPago").modal("show");
}

// Enviar formulario de pago
$("#formRegistrarPago").submit(function (e) {
  e.preventDefault();
  var formData = $(this).serialize();

  $.ajax({
    type: "POST",
    url: "../../modules/clientes/insertar-abono.php",
    data: formData,
    dataType: "json",
    beforeSend: function () {
      Swal.fire({
        html: "<h4>Procesando pago...</h4>",
        allowOutsideClick: false,
        onBeforeOpen: () => {
          Swal.showLoading();
        },
      });
    },
    success: function (response) {
      Swal.close();
      if (response.status === "success") {
        $("#modalRegistrarPago").modal("hide");
        $.Notification.notify(
          "success",
          "bottom-right",
          "Pago Registrado",
          response.message,
        );
        listarCuentas(); // Recargar tabla
      } else {
        Swal.fire("Error", response.message, "error");
      }
    },
    error: function () {
      Swal.close();
      Swal.fire("Error", "Ocurrió un error de conexión", "error");
    },
  });
});
