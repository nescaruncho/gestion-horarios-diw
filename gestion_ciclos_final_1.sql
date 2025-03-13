-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-03-2025 a las 21:33:41
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
-- Base de datos: `gestion_ciclos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclo`
--

CREATE TABLE `ciclo` (
  `id_ciclo` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciclo`
--

INSERT INTO `ciclo` (`id_ciclo`, `codigo`, `name`) VALUES
(1, 'SIFC03', 'Desenvolvemento de aplicacións web'),
(2, 'SIFC02', 'Desenvolvemento de aplicacións multiplataforma');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclo_tiene_modulo`
--

CREATE TABLE `ciclo_tiene_modulo` (
  `id_ciclo_modulo` int(11) NOT NULL,
  `id_ciclo` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciclo_tiene_modulo`
--

INSERT INTO `ciclo_tiene_modulo` (`id_ciclo_modulo`, `id_ciclo`, `id_modulo`, `id_profesor`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 1, 2, NULL),
(4, 1, 4, 1),
(5, 1, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id_modulo` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `curso` enum('1º','2º') NOT NULL,
  `horas_totales` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id_modulo`, `name`, `curso`, `horas_totales`) VALUES
(1, 'Sistemas Informáticos', '1º', 350),
(2, 'Bases de datos', '1º', 450),
(4, 'Diseño de Interfaces Web', '2º', 250),
(5, 'JavaScript', '1º', 200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`id_profesor`, `name`, `lastname`, `email`) VALUES
(1, 'Ignacio', 'Sordo', 'ignacio.sordo@acme.com'),
(2, 'Xan', 'García', 'xan.garcia@xunta.gal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesion`
--

CREATE TABLE `sesion` (
  `id_sesion` int(11) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') NOT NULL,
  `aula` varchar(50) DEFAULT NULL,
  `id_ciclo_modulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesion`
--

INSERT INTO `sesion` (`id_sesion`, `hora_inicio`, `hora_fin`, `dia_semana`, `aula`, `id_ciclo_modulo`) VALUES
(9, '08:45:00', '09:35:00', 'Lunes', '24', 1),
(10, '09:35:00', '10:25:00', 'Lunes', '24', 3),
(11, '10:25:00', '11:15:00', 'Lunes', '24', 1),
(12, '11:15:00', '12:05:00', 'Lunes', '24', 3),
(13, '12:05:00', '12:55:00', 'Lunes', '24', 1),
(17, '08:45:00', '09:35:00', 'Lunes', '24', 2),
(18, '12:55:00', '13:45:00', 'Lunes', '24', 3),
(19, '13:45:00', '14:35:00', 'Martes', '24', 4),
(20, '13:45:00', '14:35:00', 'Miércoles', '24', 4),
(21, '11:15:00', '12:05:00', 'Viernes', '24', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_modulo`
--

CREATE TABLE `user_modulo` (
  `id_user_modulo` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `fecha_matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_modulo`
--

INSERT INTO `user_modulo` (`id_user_modulo`, `id_user`, `id_modulo`, `fecha_matricula`) VALUES
(1, 2, 1, '2025-03-12'),
(2, 2, 2, '2025-03-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_user` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dni` char(9) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('administrador','alumno') NOT NULL DEFAULT 'alumno',
  `curso` enum('1º','2º') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_user`, `name`, `lastname`, `email`, `dni`, `login`, `password`, `rol`, `curso`) VALUES
(1, 'admin', '', 'admin@acme.com', '12345678a', 'admin', '$2y$10$Ly4r2quO6zPJR3anpyCCIuBDiPC3AnReXpGCALb8fjbkMFNWELaXe', 'administrador', NULL),
(2, 'hugo', 'da silva', 'hugo@acme.com', '87654321a', 'hugo', '$2y$10$gZ1Fe/EAaGmTUosZKE4DWed0of9nElKPWV8S4oSnNLhLQ7QyjuIL2', 'alumno', '1º');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_ciclo`
--

CREATE TABLE `usuario_ciclo` (
  `id_usuario_ciclo` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_ciclo` int(11) NOT NULL,
  `fecha_matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_ciclo`
--

INSERT INTO `usuario_ciclo` (`id_usuario_ciclo`, `id_user`, `id_ciclo`, `fecha_matricula`) VALUES
(1, 2, 1, '2025-03-12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ciclo`
--
ALTER TABLE `ciclo`
  ADD PRIMARY KEY (`id_ciclo`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `ciclo_tiene_modulo`
--
ALTER TABLE `ciclo_tiene_modulo`
  ADD PRIMARY KEY (`id_ciclo_modulo`),
  ADD KEY `id_ciclo` (`id_ciclo`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_profesor`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `sesion`
--
ALTER TABLE `sesion`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_ciclo_modulo` (`id_ciclo_modulo`);

--
-- Indices de la tabla `user_modulo`
--
ALTER TABLE `user_modulo`
  ADD PRIMARY KEY (`id_user_modulo`),
  ADD UNIQUE KEY `unique_user_modulo` (`id_user`,`id_modulo`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indices de la tabla `usuario_ciclo`
--
ALTER TABLE `usuario_ciclo`
  ADD PRIMARY KEY (`id_usuario_ciclo`),
  ADD UNIQUE KEY `unique_usuario_ciclo` (`id_user`,`id_ciclo`),
  ADD KEY `id_ciclo` (`id_ciclo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ciclo`
--
ALTER TABLE `ciclo`
  MODIFY `id_ciclo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ciclo_tiene_modulo`
--
ALTER TABLE `ciclo_tiene_modulo`
  MODIFY `id_ciclo_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sesion`
--
ALTER TABLE `sesion`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `user_modulo`
--
ALTER TABLE `user_modulo`
  MODIFY `id_user_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_ciclo`
--
ALTER TABLE `usuario_ciclo`
  MODIFY `id_usuario_ciclo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciclo_tiene_modulo`
--
ALTER TABLE `ciclo_tiene_modulo`
  ADD CONSTRAINT `ciclo_tiene_modulo_ibfk_1` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclo` (`id_ciclo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ciclo_tiene_modulo_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ciclo_tiene_modulo_ibfk_3` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesion`
--
ALTER TABLE `sesion`
  ADD CONSTRAINT `sesion_ibfk_1` FOREIGN KEY (`id_ciclo_modulo`) REFERENCES `ciclo_tiene_modulo` (`id_ciclo_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_modulo`
--
ALTER TABLE `user_modulo`
  ADD CONSTRAINT `user_modulo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_modulo_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_ciclo`
--
ALTER TABLE `usuario_ciclo`
  ADD CONSTRAINT `usuario_ciclo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_ciclo_ibfk_2` FOREIGN KEY (`id_ciclo`) REFERENCES `ciclo` (`id_ciclo`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
