-- SQL para crear la tabla 'movimientos_caja'
-- Esta tabla es necesaria para las funcionalidades de "Caja Consulta" y "Caja Chica".

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `tipo` varchar(10) NOT NULL COMMENT 'Puede ser: INGRESO, EGRESO, INICIO, CIERRE',
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `jornada_id` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `jornada_id` (`jornada_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
