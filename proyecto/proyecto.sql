-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2023 a las 12:07:54
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ccomunal`
--

CREATE TABLE `ccomunal` (
  `id_cc` int(10) NOT NULL,
  `nombre_cc` varchar(30) NOT NULL,
  `situr` varchar(70) NOT NULL,
  `situr_nuevo` varchar(70) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `municipio` varchar(20) NOT NULL,
  `parroquia` varchar(30) NOT NULL,
  `tipo_cc` varchar(10) NOT NULL,
  `comuna` varchar(30) NOT NULL,
  `situacion` varchar(10) NOT NULL,
  `habitantes` int(10) NOT NULL,
  `prop_elecciones` date NOT NULL,
  `vencimiento` date NOT NULL,
  `cuaderno` int(10) NOT NULL,
  `participantes` int(10) NOT NULL,
  `acta` varchar(100) NOT NULL,
  `Mmayores` int(10) NOT NULL,
  `Mmenores` int(10) NOT NULL,
  `Hmayores` int(10) NOT NULL,
  `Hmenores` int(10) NOT NULL,
  `Latitud` varchar(40) NOT NULL,
  `Longitud` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ccomunal`
--

INSERT INTO `ccomunal` (`id_cc`, `nombre_cc`, `situr`, `situr_nuevo`, `estado`, `municipio`, `parroquia`, `tipo_cc`, `comuna`, `situacion`, `habitantes`, `prop_elecciones`, `vencimiento`, `cuaderno`, `participantes`, `acta`, `Mmayores`, `Mmenores`, `Hmayores`, `Hmenores`, `Latitud`, `Longitud`) VALUES
(1, 'Comunera', '13245646', '132456879', 'miranda', 'los salias', 'san antonio', 'Urbano', 'comunera', 'Vigente', 43, '2023-10-27', '2026-10-27', 100, 20, '/actas/certificado_Digitaliza_tu_negocio.pdf', 10, 12, 10, 11, '10.49397', '-66.87724'),
(11, 'prueba', '464798', '163546847', 'mdufh', 'dsfasno', 'asdlfjns', 'Rural', 'sdfgs', 'Vigente', 70, '2020-12-10', '2023-12-10', 42, 45, '/actas/p3.pdf', 41, 11, 4, 14, '0', '0'),
(12, 'asfasf', 'fafsas', 'fafsa', 'fasfasf', 'sfas', 'sfasfa', 'Urbano', 'asfsaf', 'Vigente', 137, '2023-10-24', '2026-10-24', 52, 82, '/actas/tecnologias_del_futuro_en_ciberseguridad.pdf', 41, 14, 41, 41, '0', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE `chat` (
  `ChatID` int(8) NOT NULL,
  `Emisor` int(10) NOT NULL,
  `Receptor` int(10) NOT NULL,
  `Mensaje` varchar(100) NOT NULL,
  `Fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `chat`
--

INSERT INTO `chat` (`ChatID`, `Emisor`, `Receptor`, `Mensaje`, `Fecha`) VALUES
(7, 1, 5, ' Bienvenido! Por favor hazme saber si necesitas ayuda con algún modulo del sistema', '2023-11-27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `tipo` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `Nombre`, `Usuario`, `pass`, `telefono`, `correo`, `tipo`) VALUES
(1, 'Yeison Aceituno', 'Yeisonavi', 'e9c84c57ec1b6faa32a5e5b69bc6c42e6f061656', '021542150', 'correo@gmail.com', '3'),
(5, 'Usuario Invitado', 'Invitado', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '1245362845', 'Invitado@gmail.com', '1'),
(6, 'Usuario Estandar', 'Estandar', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '124562231', 'Estandar@gmail.com', '2'),
(7, 'Usuario Administrado', 'Administrador', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '123641256', 'Administrador@gmail.com', '3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `voceros`
--

CREATE TABLE `voceros` (
  `id_vocero` int(8) NOT NULL,
  `id_cc` int(8) NOT NULL,
  `Nombre` varchar(30) NOT NULL,
  `Apellido` varchar(30) NOT NULL,
  `Cedula` varchar(12) NOT NULL,
  `Tlf` varchar(15) NOT NULL,
  `Unidad` varchar(40) NOT NULL,
  `Comite` varchar(100) NOT NULL,
  `Tipo` varchar(15) NOT NULL,
  `Votos` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `voceros`
--

INSERT INTO `voceros` (`id_vocero`, `id_cc`, `Nombre`, `Apellido`, `Cedula`, `Tlf`, `Unidad`, `Comite`, `Tipo`, `Votos`) VALUES
(1, 1, 'Nombre prueba', 'Apellido prueba', '123648851', '123458412', 'Unidad Administrativa y financiera', 'N/A', 'Suplente', 89),
(2, 1, 'jose', 'diaz', '134587951', '12546328', 'Unidad de Contraloria Social Comunal', 'N/A', 'Principal', 50),
(3, 1, 'ivan', 'torres', '13568424', '01352974635', 'Unidad Ejecutiva', 'Turismo', 'Suplente', 65),
(4, 1, 'Jesus Enrique', 'Martinez Diaz', '19351578', '124563185', 'Unidad Administrativa y financiera', 'N/A', 'Principal', 56),
(5, 1, 'yeison', 'Pinzon', '33246812', '0124869515', 'Comision Electoral', 'N/A', 'Principal', 86);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ccomunal`
--
ALTER TABLE `ccomunal`
  ADD PRIMARY KEY (`id_cc`);

--
-- Indices de la tabla `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`ChatID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `voceros`
--
ALTER TABLE `voceros`
  ADD PRIMARY KEY (`id_vocero`),
  ADD UNIQUE KEY `Cedula` (`Cedula`),
  ADD KEY `id_cc` (`id_cc`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ccomunal`
--
ALTER TABLE `ccomunal`
  MODIFY `id_cc` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `chat`
--
ALTER TABLE `chat`
  MODIFY `ChatID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `voceros`
--
ALTER TABLE `voceros`
  MODIFY `id_vocero` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `voceros`
--
ALTER TABLE `voceros`
  ADD CONSTRAINT `voceros_ibfk_1` FOREIGN KEY (`id_cc`) REFERENCES `ccomunal` (`id_cc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
