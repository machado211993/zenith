<!--insertar-movimiento-caja.php	modules/caja/	Lógica PHP para registrar Ingresos, Egresos, Inicio y Cierre de Caja en la DB.
2	consultar-movimientos-caja.php	modules/caja/	Lógica PHP para consultar todos los movimientos y calcular el resumen/totales de la jornada.
3	registro-caja.js	ajax/	Script JS para enviar los datos del formulario de Caja Chica (petición AJAX al archivo 1).
4	resumen-caja.js	ajax/	Script JS para recibir los datos de la consulta (archivo 2) y pintar la tabla y el resumen en pantalla.
5	caja-chica.php	views/caja/	La interfaz HTML con el formulario de Ingreso/Egreso.
6	caja-consulta.php	views/caja/	La interfaz HTML para mostrar el resumen de totales y la lista de movimientos. 


⏭️ Tareas Pendientes Críticas:
Definir funciones clave: Debes asegurarte de que obtener_usuario_sesion(), obtener_jornada_actual() y responder_json() estén definidas en web_functions.php o en el propio archivo PHP si no lo están.

Incluir JS: Asegúrate de incluir ajax/registro-caja.js en caja-chica.php y ajax/resumen-caja.js en caja-consulta.php, preferiblemente en el pie de página.

Crear Tabla DB: Ejecuta el SQL para crear la tabla movimientos_caja.





-->