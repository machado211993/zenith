// ajax/registro-caja.js

document.addEventListener("DOMContentLoaded", () => {
  const formCajaChica = document.getElementById("formCajaChica");
  const tipoMovimientoSelect = document.getElementById("tipo_movimiento");
  const montoInput = document.getElementById("monto");
  const montoLabel = document.getElementById("label_monto");
  const montoHelper = document.getElementById("monto_helper");
  const descripcionInput = document.getElementById("descripcion");
  const metodoPagoSelect = document.getElementById("metodo_pago");
  const submitButton = formCajaChica.querySelector('button[type="submit"]');

  function manejarCambioTipoMovimiento() {
    const tipo = tipoMovimientoSelect.value;
    
    montoLabel.textContent = "Monto $";
    montoHelper.textContent = "";
    descripcionInput.disabled = false;
    metodoPagoSelect.disabled = false;

    if (tipo === 'INICIO') {
        montoLabel.textContent = "Monto Inicial $";
        montoHelper.textContent = "Dinero con el que se abre la caja.";
        descripcionInput.value = "Apertura de caja";
        metodoPagoSelect.disabled = true;
    } else if (tipo === 'CIERRE') {
        montoLabel.textContent = "Monto Final Real $";
        montoHelper.textContent = "Dinero contado físicamente en caja.";
        descripcionInput.value = "Cierre de caja.";
        descripcionInput.disabled = true;
        metodoPagoSelect.disabled = true;
    } else if (tipo === 'INGRESO' || tipo === 'EGRESO') {
        if(descripcionInput.value === "Apertura de caja" || descripcionInput.value === "Cierre de caja.") {
            descripcionInput.value = "";
        }
    }
  }

  function actualizarEstadoCajaUI() {
    fetch("../../modules/caja/consultar-estado-caja.php")
      .then(response => response.json())
      .then(data => {
        if (!data.exito) {
          throw new Error(data.mensaje || 'No se pudo consultar el estado de la caja.');
        }

        formCajaChica.reset();
        [tipoMovimientoSelect, montoInput, descripcionInput, metodoPagoSelect, submitButton].forEach(el => el.disabled = false);
        Array.from(tipoMovimientoSelect.options).forEach(opt => opt.disabled = false);
        manejarCambioTipoMovimiento();

        switch (data.estado) {
          case 'ABIERTA':
            tipoMovimientoSelect.querySelector('option[value="INICIO"]').disabled = true;
            montoInput.placeholder = "Ej: 25.50";
            if (tipoMovimientoSelect.value === 'INICIO') tipoMovimientoSelect.value = '';
            break;

          case 'CERRADA':
          case 'NO_INICIADA':
            Array.from(tipoMovimientoSelect.options).forEach(opt => {
              if (opt.value && opt.value !== 'INICIO') {
                opt.disabled = true;
              }
            });
            tipoMovimientoSelect.value = 'INICIO';
            montoInput.placeholder = "Ingrese el monto inicial";
            break;
        }
        // Disparar el evento de cambio manualmente para ajustar el texto del label/helper
        manejarCambioTipoMovimiento();
      })
      .catch(error => {
        console.error("Error al actualizar UI de caja:", error);
        Swal.fire({
          type: "error",
          title: "Error de Carga",
          text: error.message,
        });
        [tipoMovimientoSelect, montoInput, descripcionInput, metodoPagoSelect, submitButton].forEach(el => el.disabled = true);
      });
  }

  if (formCajaChica) {
    actualizarEstadoCajaUI();
    tipoMovimientoSelect.addEventListener('change', manejarCambioTipoMovimiento);

    formCajaChica.addEventListener("submit", function (e) {
      e.preventDefault();
      const datos = new FormData(this);
      const tipoMovimiento = datos.get('tipo_movimiento');

      submitButton.disabled = true;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';

      fetch("../../modules/caja/insertar-movimiento-caja.php", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: datos,
      })
      .then(response => response.json())
      .then(data => {
        if (data.exito) {
          // Marcar que los datos de caja han cambiado para que la otra vista se actualice
          localStorage.setItem('cajaDataDirty', 'true');

          Swal.fire({
            type: "success",
            title: "Éxito",
            text: data.mensaje,
            timer: 2500,
            showConfirmButton: false
          });

          if (tipoMovimiento === 'INICIO' || tipoMovimiento === 'CIERRE') {
            setTimeout(actualizarEstadoCajaUI, 1000);
          } else {
             // No resetear todo el formulario, solo los campos relevantes
             montoInput.value = '';
             descripcionInput.value = '';
             tipoMovimientoSelect.value = '';
          }
          
          if (typeof cargarMovimientosCaja === "function") {
            cargarMovimientosCaja();
          }

        } else {
          Swal.fire({
            type: "error",
            title: "Error",
            text: data.mensaje || "Ocurrió un error al registrar el movimiento.",
          });
        }
      })
      .catch(error => {
        console.error("Error en la petición:", error);
        Swal.fire({
          type: "error",
          title: "Error de Conexión",
          text: "No se pudo comunicar con el servidor.",
        });
      })
      .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-save"></i> Registrar Movimiento';
      });
    });
  }
});
