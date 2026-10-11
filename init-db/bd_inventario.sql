-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-07-2026 a las 19:58:39
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
-- Base de datos: `bd_inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `ID_Ingreso` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `ID_Proveedor` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Fecha_Ingreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_Producto` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Stock` int(11) NOT NULL DEFAULT 0,
  `Precio` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ID_Producto`, `Nombre`, `Stock`, `Precio`) VALUES
(1, 'Dell Latitude 5540', 1, 18999.99),
(2, 'HP ProBook 450 G10', 1, 14500.00),
(3, 'Lenovo ThinkPad E15', 1, 13200.50),
(4, 'Dell Inspiron 15 3000', 1, 8999.99),
(5, 'HP Pavilion 14', 1, 16800.00),
(6, 'Dell OptiPlex 7090', 1, 22500.00),
(7, 'HP EliteDesk 800 G9', 1, 18900.00),
(8, 'Lenovo ThinkCentre M70q', 1, 15600.00),
(9, 'HP ProDesk 400 G9', 1, 12400.00),
(10, 'Dell UltraSharp U2723QE', 1, 12500.00),
(11, 'HP E24 G5', 1, 4200.00),
(12, 'Lenovo ThinkVision T24i-30', 1, 3800.00),
(13, 'Dell P2422H', 1, 4500.00),
(14, 'Logitech MX Keys', 1, 2800.00),
(15, 'Logitech K380', 1, 899.99),
(16, 'Microsoft Wireless Desktop 900', 1, 1200.00),
(17, 'Logitech MX Master 3S', 1, 1899.99),
(18, 'Logitech M720 Triathlon', 1, 999.99),
(19, 'Samsung SSD 870 EVO 1TB', 1, 1450.00),
(20, 'WD Blue SN570 500GB', 1, 899.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `ID_Proveedor` int(11) NOT NULL,
  `Razon_Social` varchar(50) NOT NULL,
  `Correo` varchar(50) DEFAULT NULL,
  `Numero` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`ID_Proveedor`, `Razon_Social`, `Correo`, `Numero`) VALUES
(1, 'Dell Technologies Perú SAC', 'ventas@dell.com.pe', '987654321'),
(2, 'HP Perú Distribuidora EIRL', 'contacto@hp.com.pe', '951234567'),
(3, 'Lenovo Perú Comercial SA', 'ventas@lenovo.com.pe', '962345678'),
(4, 'Logitech Perú Distribuciones SRL', 'distribucion@logitech.com.pe', '973456789'),
(5, 'Microsoft Perú Solutions SAC', 'partners@microsoft.com.pe', '984567890');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas`
--

CREATE TABLE `salidas` (
  `ID_Salida` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Razon` varchar(50) DEFAULT NULL,
  `Fecha_Salida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `ID_Venta` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Fecha_Venta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Usuario` varchar(50) NOT NULL UNIQUE,
  `Password` varchar(255) NOT NULL,
  `Rol` varchar(20) NOT NULL DEFAULT 'admin',
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuario de prueba: admin / admin123 (la contraseña ya está hasheada con password_hash de PHP)
INSERT INTO `usuarios` (`Nombre`, `Usuario`, `Password`, `Rol`, `Estado`) VALUES
('Administrador', 'admin', '$2y$10$l6LWdto9oPY9LMHH6gFsku/BMhJSpJwvbkkN/CrTDwxpLaFdRYNqi', 'admin', 1);
--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`ID_Ingreso`),
  ADD KEY `ID_Producto` (`ID_Producto`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_Producto`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`ID_Proveedor`);

--
-- Indices de la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`ID_Salida`),
  ADD KEY `ID_Producto` (`ID_Producto`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`ID_Venta`),
  ADD KEY `ID_Producto` (`ID_Producto`);


--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `ID_Ingreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salidas`
--
ALTER TABLE `salidas`
  MODIFY `ID_Salida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `ID_Venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`),
  ADD CONSTRAINT `ingresos_ibfk_2` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`);

--
-- Filtros para la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD CONSTRAINT `salidas_ibfk_1` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;