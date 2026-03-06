-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-03-2026 a las 03:42:50
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
  `aviso` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `cedula_id` varchar(9) NOT NULL,
  `tipo_doc` enum('V','E','J','P') NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nac` date NOT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `registrado_por` varchar(9) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `pago_id` int(10) UNSIGNED NOT NULL,
  `cita_id` int(10) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','pago_movil','punto') NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `referencia` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quiropedistas`
--

CREATE TABLE `quiropedistas` (
  `usuario_cedula` varchar(9) NOT NULL,
  `especialidad` varchar(100) DEFAULT 'Quiropedia General',
  `disponibilidad` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `servicio_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estatus` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_id` varchar(9) NOT NULL,
  `tipo_doc` enum('V','E','J','P') NOT NULL,
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
('26901498', 'V', 'Luis', 'Angel', 'Olivares', 'Ramirez', 'olivaresuptm@gmail.com', '$2y$10$XLXH/IWXzs7As1q8rhShKuvHZcr0gvCNtY9D1KnEqoGbuJ2A9pBHy', 'gerente', '2026-03-02 17:33:26', 1, NULL, NULL),
('30451363', 'V', 'Sofia', 'Nathaly', 'Rojas', 'Bastidas', 'webdevelopervzla@gmail.com', '$2y$10$kADOkRWXIvv0zF6pP49BZ.6UNebi55Ky2dsE52Brj.mh6o8/BDRRm', 'quiropedista', '2026-03-02 21:07:25', 1, NULL, NULL),
('31190339', 'V', 'José', 'Manuel', 'Mendez', 'Marquez', 'luisangel50089@gmail.com', '$2y$10$8fhTROk8ivgdNn6zeMpD8ehGSxjO/ulfu79pPWPovxFxt3l7nubOe', 'quiropedista', '2026-03-02 18:32:18', 1, NULL, NULL),
('31190543', 'V', 'Dorianny', 'Andrea', 'Marcano', 'Araque', 'kingdomofash01@gmail.com', '$2y$10$D5Q.uft2okUiJpoAhFRk8.yK5sXdBF31SiOjhzvE/LIu2olD1/rm2', 'recepcionista', '2026-03-05 22:03:45', 1, NULL, NULL);

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
  MODIFY `cita_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_clinico`
--
ALTER TABLE `historial_clinico`
  MODIFY `historial_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `pago_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `servicio_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
