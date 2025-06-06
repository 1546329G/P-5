-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-06-2025 a las 03:30:05
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
-- Base de datos: `veterinaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `dni` varchar(20) NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `nacionalidad` varchar(100) NOT NULL,
  `diagnostico` varchar(100) NOT NULL,
  `sexo` varchar(100) NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `fechaSeguimientoInicio` date NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `direccion`, `telefono`, `dni`, `doctor`, `nombre`, `fechaNacimiento`, `nacionalidad`, `diagnostico`, `sexo`, `especialidad`, `fechaSeguimientoInicio`, `descripcion`) VALUES
(7, 'clauhot@gmail.com', '5555555', '45693123', 'Claudio', 'Sergei', '2025-06-12', 'Extranjero', 'alergia', 'hombre', 'alergologia', '2025-06-19', ''),
(8, 'lalalagmail', '777777777', '123456971482555', 'zafina', 'miguel', '2025-06-25', 'Extranjero', 'cancer', 'hombre', 'sida', '2025-06-24', ''),
(9, 'pato@gmail.com', '326597412', '3698521771', 'Sergei Dragunov', 'Claudio Serafino', '2025-06-28', 'Extranjero', 'Silicosis', 'hombre', 'Neumología', '2025-06-17', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_visitas`
--

CREATE TABLE `historial_visitas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `fecha_visita` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `nacionalidad` varchar(100) NOT NULL,
  `diagnostico` varchar(100) NOT NULL,
  `sexo` enum('masculino','femenino') NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `propietario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `nombre`, `nacionalidad`, `diagnostico`, `sexo`, `especialidad`, `fechaNacimiento`, `propietario_id`) VALUES
(8, 'Sergei', 'Extranjero', 'alergia', '', 'alergologia', '2025-06-12', 7),
(9, 'miguel', 'Extranjero', 'cancer', '', 'sida', '2025-06-25', 8),
(10, 'Claudio Serafino', 'Extranjero', 'Silicosis', '', 'Neumología', '2025-06-28', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`username`, `password`) VALUES
('admin', '1234');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_visitas`
--
ALTER TABLE `historial_visitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `mascota_id` (`mascota_id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propietario_id` (`propietario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `historial_visitas`
--
ALTER TABLE `historial_visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_visitas`
--
ALTER TABLE `historial_visitas`
  ADD CONSTRAINT `historial_visitas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_visitas_ibfk_2` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
