-- SQL para crear la tabla 'caja_jornadas'
-- Esta tabla es para gestionar el estado de apertura y cierre de las jornadas de caja.

CREATE TABLE `caja_jornadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jornada_id` varchar(10) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `total_ingresos` decimal(10,2) DEFAULT NULL,
  `total_egresos` decimal(10,2) DEFAULT NULL,
  `monto_final_esperado` decimal(10,2) DEFAULT NULL,
  `monto_final_real` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` varchar(10) NOT NULL COMMENT 'Puede ser: ABIERTA, CERRADA',
  `usuario_apertura_id` int(11) NOT NULL,
  `usuario_cierre_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jornada_id` (`jornada_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
