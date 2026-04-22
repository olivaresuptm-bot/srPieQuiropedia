-- Respaldo Sr. Pie Quiropedia
-- Fecha: 2026-04-22 19:27:14
-- Usuario: 26901498

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(9) DEFAULT NULL,
  `accion` varchar(30) NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `registro_id` varchar(50) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bitacora` (`id`, `usuario`, `accion`, `tabla`, `registro_id`, `detalle`, `ip`, `fecha`) VALUES ('1', '26901498', 'INSERTAR', 'citas', '29', 'Nueva cita - Paciente: 26901499, Fecha: 2026-04-23, Hora: 03:29:00', '127.0.0.1', '2026-04-22 17:29:30');
INSERT INTO `bitacora` (`id`, `usuario`, `accion`, `tabla`, `registro_id`, `detalle`, `ip`, `fecha`) VALUES ('2', '26901498', 'CREAR_RESPALDO', 'SISTEMA', NULL, 'respaldo_srpie_2026-04-22_17-30-51.sql', '127.0.0.1', '2026-04-22 17:30:51');

DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `cita_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_cedula` varchar(9) NOT NULL,
  `quiropedista_cedula` varchar(9) NOT NULL,
  `servicio_id` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estatus` enum('programada','atendida','cancelada') DEFAULT 'programada',
  `aviso` char(1) NOT NULL,
  `estado_comision` int(11) DEFAULT 0,
  PRIMARY KEY (`cita_id`),
  KEY `fk_cita_pac` (`paciente_cedula`),
  KEY `fk_cita_qui` (`quiropedista_cedula`),
  KEY `fk_cita_ser` (`servicio_id`),
  CONSTRAINT `fk_cita_pac` FOREIGN KEY (`paciente_cedula`) REFERENCES `pacientes` (`cedula_id`),
  CONSTRAINT `fk_cita_qui` FOREIGN KEY (`quiropedista_cedula`) REFERENCES `quiropedistas` (`usuario_cedula`),
  CONSTRAINT `fk_cita_ser` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`servicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('2', '26901499', '26901497', '4', '2026-04-04', '11:25:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('3', '26901499', '26901495', '1', '2026-03-26', '10:50:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('4', '26901499', '26901497', '1', '2026-03-26', '10:51:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('5', '26901499', '26901495', '1', '2026-04-03', '11:45:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('6', '26901499', '26901495', '1', '2026-03-16', '08:30:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('7', '26901499', '26901495', '2', '2026-03-21', '09:45:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('10', '8705197', '26901497', '1', '2026-03-21', '09:45:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('11', '26901499', '26901495', '1', '2026-03-27', '11:01:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('12', '26901499', '26901495', '1', '2026-03-18', '14:55:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('13', '26901499', '26901495', '1', '2026-03-18', '12:58:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('14', '26901499', '26901495', '1', '2026-03-23', '15:55:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('15', '8953348', '26901495', '2', '2026-03-23', '16:22:00', 'atendida', 'N', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('16', '26901499', '26901495', '1', '2026-04-09', '12:00:00', 'cancelada', 'S', '0');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('23', '26901499', '26901497', '1', '2026-04-09', '05:53:00', 'cancelada', 'S', '0');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('24', '26901499', '26901497', '1', '2026-04-09', '22:53:00', 'cancelada', 'S', '0');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('25', '26901499', '26901495', '1', '2026-04-09', '06:00:00', 'cancelada', 'S', '0');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('26', '26901499', '26901495', '1', '2026-04-09', '12:00:00', 'atendida', 'S', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('27', '26901499', '26901497', '2', '2026-04-10', '12:00:00', 'atendida', 'S', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('28', '26901499', '26901495', '1', '2026-04-11', '17:00:00', 'atendida', 'S', '1');
INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES ('29', '26901499', '26901495', '2', '2026-04-23', '03:29:00', 'programada', 'N', '0');

DROP TABLE IF EXISTS `historial_clinico`;
CREATE TABLE `historial_clinico` (
  `historial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_cedula` varchar(9) NOT NULL,
  `cita_id` int(10) unsigned NOT NULL,
  `quiropedista_cedula` varchar(9) NOT NULL,
  `motivo_consulta` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`historial_id`),
  KEY `fk_his_pac` (`paciente_cedula`),
  KEY `fk_his_cita` (`cita_id`),
  KEY `fk_his_qui` (`quiropedista_cedula`),
  CONSTRAINT `fk_his_cita` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`cita_id`),
  CONSTRAINT `fk_his_pac` FOREIGN KEY (`paciente_cedula`) REFERENCES `pacientes` (`cedula_id`),
  CONSTRAINT `fk_his_qui` FOREIGN KEY (`quiropedista_cedula`) REFERENCES `quiropedistas` (`usuario_cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `historial_clinico` (`historial_id`, `paciente_cedula`, `cita_id`, `quiropedista_cedula`, `motivo_consulta`, `diagnostico`, `tratamiento`, `observaciones`, `fecha_registro`) VALUES ('1', '26901499', '12', '26901495', 'Perdida de uña ', 'Hongos', 'Crema', 'Debe volver para evolucion del hongo', '2026-03-23 16:05:59');
INSERT INTO `historial_clinico` (`historial_id`, `paciente_cedula`, `cita_id`, `quiropedista_cedula`, `motivo_consulta`, `diagnostico`, `tratamiento`, `observaciones`, `fecha_registro`) VALUES ('2', '8953348', '15', '26901495', 'Dolor en uña', 'Uña encarnada', 'Corte de una, y tratamiento', 'Debe volver en 3 dias para cura, y evitar infeccion', '2026-03-23 16:19:24');

DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE `pacientes` (
  `cedula_id` varchar(9) NOT NULL,
  `tipo_doc` enum('V','E') NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nac` date NOT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `instagram` varchar(25) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `diabetico` varchar(2) NOT NULL DEFAULT 'No',
  `registrado_por` varchar(9) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cedula_id`),
  KEY `fk_pac_reg` (`registrado_por`),
  CONSTRAINT `fk_pac_reg` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`cedula_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('11111111', 'V', 'Luis', 'Luis', 'Spiro', 'Ramirez', '2006-02-01', 'M', '04242088865', 'luisangel50089@gmail.com', '@luissss', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-13 18:54:39');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('26901496', 'V', 'Luis', 'jjj', 'Spiro', 'Ramirez', '2002-02-28', 'M', '04248088866', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-09 22:19:02');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('26901497', 'V', 'Luis', 'Petro', 'Perez', 'Ramirez', '2001-03-01', 'M', '04248088866', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-09 22:12:11');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('26901499', 'V', 'Angel', 'Luis', 'Olivares', 'Ramirez', '0000-00-00', 'M', '04248088865', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 20', 'No', '26901498', '2026-03-06 18:54:15');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('54725727', 'V', 'Javier', 'Luis', 'Jorge', 'Ramiro', '2026-01-01', 'M', '515262', 'luisangel@gmail.com', NULL, 'Los Curos, El Entable, vereda 3, casa 20', 'No', '26901498', '2026-03-13 18:24:02');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('8705197', 'V', 'Zaida', 'Elena', 'Ramirez', 'Araque', '1998-02-05', 'F', '0424082865', 'luis1@gmail.com', '', 'Los curos', 'No', '26901498', '2026-03-16 15:08:49');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('8709422', 'V', 'Dolly', 'Milangi', 'Olivares', 'Ramirez', '1995-06-14', 'F', '04242088865', 'dolly@gmail.com', '@dollymila', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-13 18:48:27');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('8953348', 'V', 'Luis', 'Jose', 'Olivares', 'Ramirez', '1989-10-19', 'M', '04248088866', 'olivaresuptm@gmail.com', NULL, 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-03-23 15:18:48');
INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES ('8953349', 'V', 'Javier', '', 'Romero', '', '2000-06-14', 'M', '04242088855', 'olivaresuptm@gmail.com', '@luisangel1415', 'Los Curos', 'No', '26901498', '2026-03-25 15:49:56');

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos` (
  `pago_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cita_id` int(10) unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tasa_bcv` decimal(10,2) DEFAULT NULL,
  `metodo_pago` enum('efectivo','efectivo_bs','transferencia','pago_movil','punto') NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `referencia` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `fk_pago_cita` (`cita_id`),
  CONSTRAINT `fk_pago_cita` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`cita_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('1', '6', '25.00', NULL, 'efectivo', '2026-03-17 18:32:34', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('2', '10', '25.00', NULL, 'pago_movil', '2026-03-17 19:36:40', '963587');
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('3', '7', '25.00', NULL, 'pago_movil', '2026-03-17 19:40:18', '986561');
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('5', '3', '25.00', NULL, 'efectivo', '2026-03-17 20:14:30', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('6', '4', '25.00', NULL, 'efectivo', '2026-03-17 21:38:24', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('7', '5', '25.00', NULL, '', '2026-03-17 21:44:10', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('8', '2', '0.00', NULL, 'efectivo_bs', '2026-03-17 21:52:16', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('9', '11', '25.00', NULL, 'efectivo_bs', '2026-03-17 21:58:31', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('10', '12', '20.00', NULL, 'efectivo', '2026-03-18 19:52:17', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('11', '13', '20.00', '451.51', 'efectivo', '2026-03-18 20:26:49', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('12', '14', '20.00', '457.08', 'efectivo', '2026-03-23 20:56:19', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('13', '15', '25.00', '457.08', 'efectivo', '2026-03-23 21:18:11', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('14', '28', '20.00', '476.43', 'efectivo', '2026-04-10 23:03:43', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('15', '27', '25.00', '480.26', 'efectivo', '2026-04-17 13:07:29', NULL);
INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES ('16', '26', '20.00', '481.70', 'efectivo_bs', '2026-04-21 15:45:50', NULL);

DROP TABLE IF EXISTS `quiropedistas`;
CREATE TABLE `quiropedistas` (
  `usuario_cedula` varchar(9) NOT NULL,
  `disponibilidad` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`usuario_cedula`),
  CONSTRAINT `fk_quiro_usu` FOREIGN KEY (`usuario_cedula`) REFERENCES `usuarios` (`cedula_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `quiropedistas` (`usuario_cedula`, `disponibilidad`) VALUES ('26901495', '1');
INSERT INTO `quiropedistas` (`usuario_cedula`, `disponibilidad`) VALUES ('26901497', '1');

DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `servicio_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `comision_porcentaje` decimal(5,2) NOT NULL DEFAULT 40.00,
  `estatus` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`servicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `servicios` (`servicio_id`, `nombre`, `descripcion`, `precio`, `comision_porcentaje`, `estatus`) VALUES ('1', 'Quiropedia', 'Tratamiento de hongos', '20.00', '40.00', '1');
INSERT INTO `servicios` (`servicio_id`, `nombre`, `descripcion`, `precio`, `comision_porcentaje`, `estatus`) VALUES ('2', 'Uña encarnada', 'Uña encarnada', '25.00', '40.00', '1');
INSERT INTO `servicios` (`servicio_id`, `nombre`, `descripcion`, `precio`, `comision_porcentaje`, `estatus`) VALUES ('3', 'Uña encarnada-Cura', 'Cura de la uña encarnada', '0.00', '0.00', '1');
INSERT INTO `servicios` (`servicio_id`, `nombre`, `descripcion`, `precio`, `comision_porcentaje`, `estatus`) VALUES ('4', 'Quiropedia-Revisión', 'Revisión', '0.00', '0.00', '1');

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `cedula_id` varchar(9) NOT NULL,
  `tipo_doc` enum('V','E') NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('gerente','quiropedista','recepcionista') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Pendiente, 1: Activo',
  PRIMARY KEY (`cedula_id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('11111111', 'V', 'Luis', 'Luis', 'Marcano', 'Ramirez', 'lili@gmail.com', '$2y$10$99Qj.7imzsw1jqeyte1UBuk54ffG/nr9pus0Og2b5FH/ejwvudOoO', 'recepcionista', '2026-04-20 17:59:41', '1');
INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('26262626', 'V', 'Pedro', 'Pepe', 'Quiñonez', 'Emiro', 'lllll@gmail.com', '$2y$10$kZaKwounych9Qqc/pdUQRuQFxIJQKb7BMpLXtE6MmxD1mYXH0sSmO', 'recepcionista', '2026-03-09 22:11:43', '1');
INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('26901495', 'V', 'Javier', 'Luis', 'Ramirez', 'Olivares', 'kingdomofash01@gmail.com', '$2y$10$mVwv1V0aT2c7U7AgIp./i.cWsoutl.mOupHg63KXf7A/iLJeeJL5W', 'quiropedista', '2026-03-07 15:41:04', '1');
INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('26901497', 'V', 'Dorianny', 'Luis', 'Ramirez', 'Olivares', 'webdevelopervzla@gmail.com', '$2y$10$j3xzHoG8rV.EgE0kDfMRWuOBJ5M7UoN4vH5gucb.PjVHu9QRG8aoe', 'quiropedista', '2026-03-07 15:39:41', '1');
INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('26901498', 'V', 'Luis', 'Angel', 'Olivares', 'Ramirez', 'olivaresuptm@gmail.com', '$2y$10$XLXH/IWXzs7As1q8rhShKuvHZcr0gvCNtY9D1KnEqoGbuJ2A9pBHy', 'gerente', '2026-03-02 13:33:26', '1');
INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`) VALUES ('31190339', 'V', 'José', 'Manuel', 'Mendez', 'Marquez', 'luisangel50089@gmail.com', '$2y$10$5m.zGLKhPwHKcbl5JLcdTuMchX3hz.R/C5tjEcDeAZPv8bfRAM2/G', 'recepcionista', '2026-03-02 14:32:18', '1');

SET FOREIGN_KEY_CHECKS=1;
