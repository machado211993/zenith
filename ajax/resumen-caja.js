// ajax/resumen-caja.js

document.addEventListener("DOMContentLoaded", function () {
  cargarJornadas();

  document
    .getElementById("jornada_selector")
    .addEventListener("change", function () {
      const jornadaId = this.value;
      if (jornadaId) {
        cargarDatosJornada(jornadaId);
      }
    });

  // Polling con localStorage para detectar cambios desde otras vistas.
  setInterval(function () {
    if (localStorage.getItem("cajaDataDirty") === "true") {
      // Limpiar la bandera inmediatamente para evitar múltiples recargas
      localStorage.removeItem("cajaDataDirty");

      // Recargar los datos de la jornada actual
      const selector = document.getElementById("jornada_selector");
      if (selector && selector.value) {
        cargarDatosJornada(selector.value);
      }
    }
  }, 1500); // Revisar cada 1.5 segundos
});

function cargarJornadas() {
  fetch("../../modules/caja/listar-jornadas.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.jornadas.length > 0) {
        const selector = document.getElementById("jornada_selector");
        selector.innerHTML = ""; // Limpiar opciones anteriores

        data.jornadas.forEach((jornada) => {
          const option = document.createElement("option");
          option.value = jornada.jornada_id;

          const fecha = new Date(jornada.fecha_apertura);
          const fechaFormateada = fecha.toLocaleDateString("es-ES", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
          });

          option.textContent = `Jornada #${jornada.jornada_id} (${fechaFormateada}) - ${jornada.estado}`;
          selector.appendChild(option);
        });

        // Cargar datos de la primera jornada (la más reciente) por defecto
        const primeraJornadaId = data.jornadas[0].jornada_id;
        if (primeraJornadaId) {
          cargarDatosJornada(primeraJornadaId);
        }
      } else {
        mostrarError("No se encontraron jornadas registradas.");
      }
    })
    .catch((error) => {
      console.error(error);
      mostrarError("Error de conexión al cargar las jornadas.");
    });
}

function cargarDatosJornada(jornadaId) {
  // Mostrar feedback de carga
  document.getElementById("jornada_actual").textContent = "Cargando...";
  document.getElementById("total_ingreso").textContent = "$ 0.00";
  document.getElementById("total_egreso").textContent = "$ 0.00";
  document.getElementById("total_caja").textContent = "$ 0.00";
  document.getElementById("total_mercadopago").textContent = "$ 0.00";
  document.getElementById("total_ctacte").textContent = "$ 0.00";
  const tbody = document
    .getElementById("tabla_movimientos_caja")
    .querySelector("tbody");
  tbody.innerHTML =
    '<tr><td colspan="5" class="text-center">Cargando movimientos...</td></tr>';

  fetch(
    `../../modules/caja/consultar-movimientos-caja.php?jornada_id=${jornadaId}`,
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        const resumen = data.data.resumen;
        document.getElementById("jornada_actual").textContent =
          data.data.jornada_id;
        document.getElementById("total_ingreso").textContent =
          `$ ${parseFloat(resumen.TotalIngreso || 0).toFixed(2)}`;
        document.getElementById("total_egreso").textContent =
          `$ ${parseFloat(resumen.TotalEgreso || 0).toFixed(2)}`;
        document.getElementById("total_caja").textContent =
          `$ ${parseFloat(resumen.TotalCaja || 0).toFixed(2)}`;
        document.getElementById("total_mercadopago").textContent =
          `$ ${parseFloat(resumen.TotalMercadoPago || 0).toFixed(2)}`;
        document.getElementById("total_ctacte").textContent =
          `$ ${parseFloat(resumen.TotalCtaCte || 0).toFixed(2)}`;

        actualizarTablaMovimientos(data.data.movimientos);
      } else {
        mostrarError(
          data.mensaje || "Error al cargar los datos de la jornada.",
        );
      }
    })
    .catch((error) => {
      console.error(error);
      mostrarError("Error de conexión al cargar datos de la jornada.");
    });
}

function actualizarTablaMovimientos(movimientos) {
  const tbody = document
    .getElementById("tabla_movimientos_caja")
    .querySelector("tbody");
  tbody.innerHTML = "";

  if (movimientos.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="text-center">No hay movimientos registrados en esta jornada.</td></tr>';
  } else {
    movimientos.forEach((mov) => {
      const row = tbody.insertRow();

      const fecha = new Date(mov.fecha);
      const fechaFormat = fecha.toLocaleString("es-ES");

      row.insertCell().textContent = fechaFormat;
      row.insertCell().textContent = mov.usuario_nombre || "N/A";
      row.insertCell().textContent = mov.tipo;
      row.insertCell().textContent = mov.descripcion;
      row.insertCell().textContent = `$ ${parseFloat(mov.monto).toFixed(2)}`;

      // Lógica de colores
      if (mov.metodo_pago === "MERCADOPAGO") {
        row.className = "table-primary text-primary"; // Azul para Mercado Pago
        row.style.fontWeight = "bold";
      } else if (mov.metodo_pago === "CTA_CTE") {
        row.className = "table-warning text-dark"; // Amarillo para Cta Cte
        row.style.fontWeight = "bold";
      } else if (mov.tipo === "INGRESO" || mov.tipo === "INICIO") {
        row.className = "table-success";
      } else if (mov.tipo === "EGRESO") {
        row.className = "table-danger";
      }
    });
  }
}

function mostrarError(mensaje) {
  document.getElementById("jornada_actual").textContent = "Error";
  const tbody = document
    .getElementById("tabla_movimientos_caja")
    .querySelector("tbody");
  tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${mensaje}</td></tr>`;
  Swal.fire({
    icon: "error",
    title: "Error",
    text: mensaje,
  });
}
