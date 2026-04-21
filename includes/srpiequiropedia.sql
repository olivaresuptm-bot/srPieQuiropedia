-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-04-2026 a las 04:36:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `srpiequiropedia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `cita_id` int(10) UNSIGNED NOT NULL,
  `paciente_cedula` varchar(9) NOT NULL,
  `quiropedista_cedula` varchar(9) NOT NULL,
  `servicio_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estatus` enum('programada','atendida','cancelada') DEFAULT 'programada',
  `aviso` char(1) NOT NULL,
  `estado_comision` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`cita_id`, `paciente_cedula`, `quiropedista_cedula`, `servicio_id`, `fecha`, `hora`, `estatus`, `aviso`, `estado_comision`) VALUES
(2, '26901499', '26901497', 4, '2026-04-04', '11:25:00', 'atendida', 'N', 1),
(3, '26901499', '26901495', 1, '2026-03-26', '10:50:00', 'atendida', 'N', 1),
(4, '26901499', '26901497', 1, '2026-03-26', '10:51:00', 'atendida', 'N', 1),
(5, '26901499', '26901495', 1, '2026-04-03', '11:45:00', 'atendida', 'N', 1),
(6, '26901499', '26901495', 1, '2026-03-16', '08:30:00', 'atendida', 'N', 1),
(7, '26901499', '26901495', 2, '2026-03-21', '09:45:00', 'atendida', 'N', 1),
(10, '8705197', '26901497', 1, '2026-03-21', '09:45:00', 'atendida', 'N', 1),
(11, '26901499', '26901495', 1, '2026-03-27', '11:01:00', 'atendida', 'N', 1),
(12, '26901499', '26901495', 1, '2026-03-18', '14:55:00', 'atendida', 'N', 1),
(13, '26901499', '26901495', 1, '2026-03-18', '12:58:00', 'atendida', 'N', 1),
(14, '26901499', '26901495', 1, '2026-03-23', '15:55:00', 'atendida', 'N', 1),
(15, '8953348', '26901495', 2, '2026-03-23', '16:22:00', 'atendida', 'N', 1),
(16, '26901499', '26901495', 1, '2026-04-09', '12:00:00', 'cancelada', 'S', 0),
(23, '26901499', '26901497', 1, '2026-04-09', '05:53:00', 'cancelada', 'S', 0),
(24, '26901499', '26901497', 1, '2026-04-09', '22:53:00', 'cancelada', 'S', 0),
(25, '26901499', '26901495', 1, '2026-04-09', '06:00:00', 'cancelada', 'S', 0),
(26, '26901499', '26901495', 1, '2026-04-09', '12:00:00', 'programada', 'S', 0),
(27, '26901499', '26901497', 2, '2026-04-10', '12:00:00', 'programada', 'S', 0),
(28, '26901499', '26901495', 1, '2026-04-11', '17:00:00', 'atendida', 'S', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_clinico`
--

CREATE TABLE `historial_clinico` (
  `historial_id` int(10) UNSIGNED NOT NULL,
  `paciente_cedula` varchar(9) NOT NULL,
  `cita_id` int(10) UNSIGNED NOT NULL,
  `quiropedista_cedula` varchar(9) NOT NULL,
  `motivo_consulta` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_clinico`
--

INSERT INTO `historial_clinico` (`historial_id`, `paciente_cedula`, `cita_id`, `quiropedista_cedula`, `motivo_consulta`, `diagnostico`, `tratamiento`, `observaciones`, `fecha_registro`) VALUES
(1, '26901499', 12, '26901495', 'Perdida de uña ', 'Hongos', 'Crema', 'Debe volver para evolucion del hongo', '2026-03-23 20:05:59'),
(2, '8953348', 15, '26901495', 'Dolor en uña', 'Uña encarnada', 'Corte de una, y tratamiento', 'Debe volver en 3 dias para cura, y evitar infeccion', '2026-03-23 20:19:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nac`, `genero`, `telefono`, `correo`, `instagram`, `direccion`, `diabetico`, `registrado_por`, `fecha_registro`) VALUES
('11111111', 'V', 'Luis', 'Luis', 'Spiro', 'Ramirez', '2006-02-01', 'M', '04242088865', 'luisangel50089@gmail.com', '@luissss', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-13 22:54:39'),
('26901496', 'V', 'Luis', 'jjj', 'Spiro', 'Ramirez', '2002-02-28', 'M', '04248088866', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-10 02:19:02'),
('26901497', 'V', 'Luis', 'Petro', 'Perez', 'Ramirez', '2001-03-01', 'M', '04248088866', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-10 02:12:11'),
('26901499', 'V', 'Angel', 'Luis', 'Olivares', 'Ramirez', '0000-00-00', 'M', '04248088865', 'kingdomofash01@gmail.com', '', 'Los Curos, El Entable, vereda 3, casa 20', 'No', '26901498', '2026-03-06 22:54:15'),
('54725727', 'V', 'Javier', 'Luis', 'Jorge', 'Ramiro', '2026-01-01', 'M', '515262', 'luisangel@gmail.com', NULL, 'Los Curos, El Entable, vereda 3, casa 20', 'No', '26901498', '2026-03-13 22:24:02'),
('8705197', 'V', 'Zaida', 'Elena', 'Ramirez', 'Araque', '1998-02-05', 'F', '0424082865', 'luis1@gmail.com', '', 'Los curos', 'No', '26901498', '2026-03-16 19:08:49'),
('8709422', 'V', 'Dolly', 'Milangi', 'Olivares', 'Ramirez', '1995-06-14', 'F', '04242088865', 'dolly@gmail.com', '@dollymila', 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-04-13 22:48:27'),
('8953348', 'V', 'Luis', 'Jose', 'Olivares', 'Ramirez', '1989-10-19', 'M', '04248088866', 'olivaresuptm@gmail.com', NULL, 'Los Curos, El Entable, vereda 3, casa 19', 'No', '26901498', '2026-03-23 19:18:48'),
('8953349', 'V', 'Javier', '', 'Romero', '', '2000-06-14', 'M', '04242088855', 'olivaresuptm@gmail.com', '@luisangel1415', 'Los Curos', 'No', '26901498', '2026-03-25 19:49:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `pago_id` int(10) UNSIGNED NOT NULL,
  `cita_id` int(10) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tasa_bcv` decimal(10,2) DEFAULT NULL,
  `metodo_pago` enum('efectivo','efectivo_bs','transferencia','pago_movil','punto') NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `referencia` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`pago_id`, `cita_id`, `monto`, `tasa_bcv`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES
(1, 6, 25.00, NULL, 'efectivo', '2026-03-17 22:32:34', NULL),
(2, 10, 25.00, NULL, 'pago_movil', '2026-03-17 23:36:40', '963587'),
(3, 7, 25.00, NULL, 'pago_movil', '2026-03-17 23:40:18', '986561'),
(5, 3, 25.00, NULL, 'efectivo', '2026-03-18 00:14:30', NULL),
(6, 4, 25.00, NULL, 'efectivo', '2026-03-18 01:38:24', NULL),
(7, 5, 25.00, NULL, '', '2026-03-18 01:44:10', NULL),
(8, 2, 0.00, NULL, 'efectivo_bs', '2026-03-18 01:52:16', NULL),
(9, 11, 25.00, NULL, 'efectivo_bs', '2026-03-18 01:58:31', NULL),
(10, 12, 20.00, NULL, 'efectivo', '2026-03-18 23:52:17', NULL),
(11, 13, 20.00, 451.51, 'efectivo', '2026-03-19 00:26:49', NULL),
(12, 14, 20.00, 457.08, 'efectivo', '2026-03-24 00:56:19', NULL),
(13, 15, 25.00, 457.08, 'efectivo', '2026-03-24 01:18:11', NULL),
(14, 28, 20.00, 476.43, 'efectivo', '2026-04-11 03:03:43', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quiropedistas`
--

CREATE TABLE `quiropedistas` (
  `usuario_cedula` varchar(9) NOT NULL,
  `especialidad` varchar(100) DEFAULT 'Quiropedia General',
  `disponibilidad` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `quiropedistas`
--

INSERT INTO `quiropedistas` (`usuario_cedula`, `especialidad`, `disponibilidad`) VALUES
('26901495', 'General', 1),
('26901497', 'General', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `servicio_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `comision_porcentaje` decimal(5,2) NOT NULL DEFAULT 40.00,
  `estatus` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`servicio_id`, `nombre`, `descripcion`, `precio`, `comision_porcentaje`, `estatus`) VALUES
(1, 'Quiropedia', 'Tratamiento de hongos', 20.00, 40.00, 1),
(2, 'Uña encarnada', 'Uña encarnada', 25.00, 40.00, 1),
(3, 'Uña encarnada-Cura', 'Cura de la uña encarnada', 0.00, 0.00, 1),
(4, 'Quiropedia-Revisión', 'Revisión', 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

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
  `token` varchar(255) DEFAULT NULL COMMENT 'Para aprobación de registro',
  `token_recuperacion` varchar(255) DEFAULT NULL COMMENT 'Para recuperación de clave'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_id`, `tipo_doc`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `correo`, `password`, `rol`, `fecha_registro`, `estado`, `token`, `token_recuperacion`) VALUES
('26262626', 'V', 'Pedro', 'Pepe', 'Quiñonez', 'Emiro', 'lllll@gmail.com', '$2y$10$kZaKwounych9Qqc/pdUQRuQFxIJQKb7BMpLXtE6MmxD1mYXH0sSmO', 'recepcionista', '2026-03-10 02:11:43', 1, '6229d4ca7869cf10cda8822da057bb5a', NULL),
('26901495', 'V', 'Javier', 'Luis', 'Ramirez', 'Olivares', 'kingdomofash01@gmail.com', '$2y$10$aU1cr9xMPXlXbXhPl/iTKOSkToWlFDKWyA/KJrHUNL2lrdWl6EB7i', 'quiropedista', '2026-03-07 19:41:04', 1, '1891316d582721a1027d9358e6126a8f', NULL),
('26901497', 'V', 'Dorianny', 'Luis', 'Ramirez', 'Olivares', 'webdevelopervzla@gmail.com', '$2y$10$j3xzHoG8rV.EgE0kDfMRWuOBJ5M7UoN4vH5gucb.PjVHu9QRG8aoe', 'quiropedista', '2026-03-07 19:39:41', 1, 'a581f4985611069611cf416d26c26e68', NULL),
('26901498', 'V', 'Luis', 'Angel', 'Olivares', 'Ramirez', 'olivaresuptm@gmail.com', '$2y$10$XLXH/IWXzs7As1q8rhShKuvHZcr0gvCNtY9D1KnEqoGbuJ2A9pBHy', 'gerente', '2026-03-02 17:33:26', 1, NULL, '15048c23f03f5fbf24d56ddf3735caa7'),
('31190339', 'V', 'José', 'Manuel', 'Mendez', 'Marquez', 'luisangel50089@gmail.com', '$2y$10$5m.zGLKhPwHKcbl5JLcdTuMchX3hz.R/C5tjEcDeAZPv8bfRAM2/G', 'recepcionista', '2026-03-02 18:32:18', 1, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`cita_id`),
  ADD KEY `fk_cita_pac` (`paciente_cedula`),
  ADD KEY `fk_cita_qui` (`quiropedista_cedula`),
  ADD KEY `fk_cita_ser` (`servicio_id`);

--
-- Indices de la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  ADD PRIMARY KEY (`historial_id`),
  ADD KEY `fk_his_pac` (`paciente_cedula`),
  ADD KEY `fk_his_cita` (`cita_id`),
  ADD KEY `fk_his_qui` (`quiropedista_cedula`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`cedula_id`),
  ADD KEY `fk_pac_reg` (`registrado_por`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`pago_id`),
  ADD KEY `fk_pago_cita` (`cita_id`);

--
-- Indices de la tabla `quiropedistas`
--
ALTER TABLE `quiropedistas`
  ADD PRIMARY KEY (`usuario_cedula`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`servicio_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `cita_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  MODIFY `historial_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `pago_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `servicio_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_cita_pac` FOREIGN KEY (`paciente_cedula`) REFERENCES `pacientes` (`cedula_id`),
  ADD CONSTRAINT `fk_cita_qui` FOREIGN KEY (`quiropedista_cedula`) REFERENCES `quiropedistas` (`usuario_cedula`),
  ADD CONSTRAINT `fk_cita_ser` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`servicio_id`);

--
-- Filtros para la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  ADD CONSTRAINT `fk_his_cita` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`cita_id`),
  ADD CONSTRAINT `fk_his_pac` FOREIGN KEY (`paciente_cedula`) REFERENCES `pacientes` (`cedula_id`),
  ADD CONSTRAINT `fk_his_qui` FOREIGN KEY (`quiropedista_cedula`) REFERENCES `quiropedistas` (`usuario_cedula`);

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `fk_pac_reg` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`cedula_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pago_cita` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`cita_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `quiropedistas`
--
ALTER TABLE `quiropedistas`
  ADD CONSTRAINT `fk_quiro_usu` FOREIGN KEY (`usuario_cedula`) REFERENCES `usuarios` (`cedula_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
