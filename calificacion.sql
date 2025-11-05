-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-11-2025 a las 00:52:03
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `calificacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `inscripcion_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','ausente','tardanza','justificado') COLLATE utf8mb4_unicode_ci DEFAULT 'presente',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_asistencia` (`inscripcion_id`,`fecha`),
  KEY `asistencias_fecha_index` (`fecha`),
  KEY `asistencias_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `inscripcion_id`, `fecha`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-09-08', 'presente', NULL, '2025-10-23 18:22:40', '2025-10-23 23:44:00'),
(2, 9, '2025-10-22', 'presente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(3, 10, '2025-10-22', 'ausente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(4, 11, '2025-10-22', 'ausente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(5, 12, '2025-10-22', 'tardanza', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(6, 13, '2025-10-22', 'presente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(7, 14, '2025-10-22', 'presente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(8, 29, '2025-10-22', 'presente', NULL, '2025-10-23 23:45:08', '2025-10-23 23:45:08'),
(9, 22, '2025-10-20', 'tardanza', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(10, 23, '2025-10-20', 'tardanza', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(11, 24, '2025-10-20', 'ausente', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(12, 25, '2025-10-20', 'presente', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(13, 26, '2025-10-20', 'presente', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(14, 27, '2025-10-20', 'ausente', NULL, '2025-10-23 23:46:31', '2025-10-23 23:46:31'),
(15, 2, '2025-09-23', 'tardanza', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(16, 3, '2025-09-23', 'justificado', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(17, 4, '2025-09-23', 'presente', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(18, 5, '2025-09-23', 'presente', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(19, 6, '2025-09-23', 'presente', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(20, 7, '2025-09-23', 'presente', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(21, 28, '2025-09-23', 'ausente', NULL, '2025-10-24 00:00:42', '2025-10-24 00:00:42'),
(22, 22, '2025-09-10', 'presente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(23, 23, '2025-09-10', 'presente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(24, 24, '2025-09-10', 'ausente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(25, 25, '2025-09-10', 'presente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(26, 26, '2025-09-10', 'presente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(27, 27, '2025-09-10', 'presente', NULL, '2025-10-24 00:04:03', '2025-10-24 00:04:03'),
(28, 22, '2025-09-17', 'presente', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(29, 23, '2025-09-17', 'presente', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(30, 24, '2025-09-17', 'tardanza', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(31, 25, '2025-09-17', 'presente', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(32, 26, '2025-09-17', 'presente', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(33, 27, '2025-09-17', 'presente', NULL, '2025-10-24 00:04:31', '2025-10-24 00:04:31'),
(34, 22, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(35, 23, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(36, 24, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(37, 25, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(38, 26, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(39, 27, '2025-09-03', 'presente', NULL, '2025-10-24 00:04:58', '2025-10-24 00:04:58'),
(40, 34, '2025-10-25', 'presente', NULL, '2025-10-25 22:16:09', '2025-10-25 22:16:09'),
(41, 35, '2025-10-25', 'tardanza', NULL, '2025-10-25 22:16:09', '2025-10-25 22:16:09'),
(42, 36, '2025-10-25', 'ausente', NULL, '2025-10-25 22:16:09', '2025-10-25 22:16:09'),
(43, 37, '2025-10-25', 'presente', NULL, '2025-10-25 22:16:09', '2025-10-25 22:16:09'),
(44, 2, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(45, 3, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(46, 4, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(47, 5, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(48, 6, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(49, 7, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(50, 28, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(51, 38, '2025-10-27', 'presente', NULL, '2025-10-27 16:53:04', '2025-10-27 16:53:04'),
(52, 2, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(53, 3, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(54, 4, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(55, 5, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(56, 6, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(57, 7, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(58, 28, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(59, 38, '2025-10-21', 'presente', NULL, '2025-10-27 16:53:25', '2025-10-27 16:53:25'),
(60, 2, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(61, 3, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(62, 4, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(63, 5, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(64, 6, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(65, 7, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(66, 28, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(67, 38, '2025-10-17', 'presente', NULL, '2025-10-27 16:53:39', '2025-10-27 16:53:39'),
(68, 2, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(69, 3, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(70, 4, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(71, 5, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(72, 6, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(73, 7, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(74, 28, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(75, 38, '2025-10-12', 'presente', NULL, '2025-10-27 16:53:49', '2025-10-27 16:53:49'),
(76, 2, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(77, 3, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(78, 4, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(79, 5, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(80, 6, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(81, 7, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(82, 28, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(83, 38, '2025-10-08', 'presente', NULL, '2025-10-27 16:54:01', '2025-10-27 16:54:01'),
(84, 2, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(85, 3, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(86, 4, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(87, 5, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(88, 6, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(89, 7, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(90, 28, '2025-10-03', 'presente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(91, 38, '2025-10-03', 'ausente', NULL, '2025-10-27 16:54:22', '2025-10-27 16:54:22'),
(92, 9, '2025-10-27', 'tardanza', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(93, 10, '2025-10-27', 'tardanza', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(94, 11, '2025-10-27', 'tardanza', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(95, 12, '2025-10-27', 'tardanza', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(96, 13, '2025-10-27', 'tardanza', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(97, 14, '2025-10-27', 'presente', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(98, 39, '2025-10-27', 'ausente', NULL, '2025-10-27 23:54:14', '2025-10-27 23:54:14'),
(99, 9, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(100, 10, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(101, 11, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(102, 12, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(103, 13, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(104, 14, '2025-10-23', 'presente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(105, 39, '2025-10-23', 'ausente', NULL, '2025-10-27 23:54:31', '2025-10-27 23:54:31'),
(106, 9, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(107, 10, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(108, 11, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(109, 12, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(110, 13, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(111, 14, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(112, 39, '2025-10-18', 'presente', NULL, '2025-10-27 23:54:50', '2025-10-27 23:54:50'),
(113, 9, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(114, 10, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(115, 11, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(116, 12, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(117, 13, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(118, 14, '2025-10-14', 'presente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04'),
(119, 39, '2025-10-14', 'ausente', NULL, '2025-10-27 23:55:04', '2025-10-27 23:55:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

DROP TABLE IF EXISTS `calificaciones`;
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `evaluacion_id` bigint UNSIGNED NOT NULL,
  `estudiante_id` bigint UNSIGNED NOT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  `comentarios` text COLLATE utf8mb4_unicode_ci,
  `fecha_calificacion` timestamp NULL DEFAULT NULL,
  `estado` enum('pendiente','calificada','revisada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `intentos` int NOT NULL DEFAULT '0',
  `tiempo_empleado` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_calificacion` (`evaluacion_id`,`estudiante_id`),
  KEY `calificaciones_estudiante_id_index` (`estudiante_id`),
  KEY `calificaciones_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `evaluacion_id`, `estudiante_id`, `nota`, `comentarios`, `fecha_calificacion`, `estado`, `intentos`, `tiempo_empleado`, `created_at`, `updated_at`) VALUES
(2, 2, 7, 8.20, 'el punto 4 es la respuesta de la vision periférica', '2025-10-22 16:34:40', 'calificada', 0, NULL, '2025-10-22 16:34:40', '2025-10-22 16:34:40'),
(3, 9, 7, 80.00, NULL, '2025-10-22 16:46:09', 'calificada', 0, NULL, '2025-10-22 16:38:36', '2025-10-22 16:46:09'),
(4, 9, 8, 70.00, NULL, '2025-10-22 16:39:31', 'calificada', 0, NULL, '2025-10-22 16:39:31', '2025-10-22 16:39:31'),
(5, 9, 9, 90.00, NULL, '2025-10-22 16:39:31', 'calificada', 0, NULL, '2025-10-22 16:39:31', '2025-10-22 16:39:31'),
(6, 9, 10, 50.00, NULL, '2025-10-22 16:39:31', 'calificada', 0, NULL, '2025-10-22 16:39:31', '2025-10-22 16:39:31'),
(7, 9, 11, 85.00, NULL, '2025-10-22 16:39:31', 'calificada', 0, NULL, '2025-10-22 16:39:31', '2025-10-22 16:39:31'),
(18, 4, 7, 90.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(9, 10, 7, 80.00, NULL, '2025-10-22 16:40:40', 'calificada', 0, NULL, '2025-10-22 16:40:40', '2025-10-22 16:40:40'),
(10, 11, 7, 70.00, NULL, '2025-10-22 16:41:01', 'calificada', 0, NULL, '2025-10-22 16:41:01', '2025-10-22 16:41:01'),
(11, 12, 7, 100.00, NULL, '2025-10-22 16:41:19', 'calificada', 0, NULL, '2025-10-22 16:41:19', '2025-10-22 16:41:19'),
(12, 5, 13, 77.00, NULL, '2025-10-23 18:10:13', 'calificada', 0, NULL, '2025-10-23 18:10:13', '2025-10-23 18:10:13'),
(13, 6, 13, 90.00, NULL, '2025-10-23 18:10:31', 'calificada', 0, NULL, '2025-10-23 18:10:31', '2025-10-23 18:10:31'),
(14, 7, 13, 88.00, NULL, '2025-10-23 18:10:49', 'calificada', 0, NULL, '2025-10-23 18:10:49', '2025-10-23 18:10:49'),
(15, 8, 13, 80.00, NULL, '2025-10-23 18:11:01', 'calificada', 0, NULL, '2025-10-23 18:11:01', '2025-10-23 18:11:01'),
(16, 8, 9, 70.00, 'subió nota, obteniendo la calificación de aprobado', '2025-10-24 19:50:00', 'calificada', 0, NULL, '2025-10-24 04:12:55', '2025-10-24 22:51:32'),
(17, 8, 10, 79.00, NULL, '2025-10-24 04:12:55', 'calificada', 0, NULL, '2025-10-24 04:12:55', '2025-10-24 04:12:55'),
(19, 4, 8, 100.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(20, 4, 9, 80.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(21, 4, 10, 40.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(22, 4, 11, 90.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(23, 4, 12, 100.00, NULL, '2025-10-24 22:58:45', 'calificada', 0, NULL, '2025-10-24 22:58:45', '2025-10-24 22:58:45'),
(25, 4, 13, 70.00, NULL, '2025-10-24 23:11:00', 'calificada', 0, NULL, '2025-10-24 23:11:28', '2025-10-24 23:11:28'),
(26, 14, 12, 100.00, NULL, '2025-10-25 18:36:00', 'calificada', 0, NULL, '2025-10-25 18:36:59', '2025-10-25 18:36:59'),
(27, 16, 12, 79.00, NULL, '2025-10-25 22:18:44', 'calificada', 0, NULL, '2025-10-25 22:18:44', '2025-10-25 22:18:44'),
(28, 16, 11, 60.00, NULL, '2025-10-25 22:18:44', 'calificada', 0, NULL, '2025-10-25 22:18:44', '2025-10-25 22:18:44'),
(29, 16, 13, 68.00, NULL, '2025-10-25 22:18:44', 'calificada', 0, NULL, '2025-10-25 22:18:44', '2025-10-25 22:18:44'),
(30, 2, 11, 67.00, NULL, '2025-10-26 00:05:00', 'calificada', 0, NULL, '2025-10-26 00:05:10', '2025-10-26 00:05:10'),
(31, 4, 16, 90.00, NULL, '2025-10-27 16:54:00', 'calificada', 0, NULL, '2025-10-27 16:54:50', '2025-10-27 16:54:50'),
(32, 1, 16, 100.00, NULL, '2025-10-08 16:54:00', 'calificada', 0, NULL, '2025-10-27 16:55:15', '2025-10-27 16:55:15'),
(33, 2, 16, 60.00, NULL, '2025-10-12 16:55:00', 'calificada', 0, NULL, '2025-10-27 16:55:52', '2025-10-27 16:55:52'),
(34, 3, 16, 80.00, NULL, '2025-10-22 16:56:00', 'calificada', 0, NULL, '2025-10-27 16:56:24', '2025-10-27 16:56:24'),
(35, 5, 16, 40.00, NULL, '2025-10-27 23:56:00', 'calificada', 0, NULL, '2025-10-27 23:56:51', '2025-10-27 23:56:51'),
(36, 6, 16, 60.00, NULL, '2025-10-18 23:57:00', 'calificada', 0, NULL, '2025-10-27 23:57:24', '2025-10-27 23:57:24'),
(37, 7, 16, 20.00, NULL, '2025-11-22 23:57:00', 'calificada', 0, NULL, '2025-10-27 23:57:58', '2025-10-27 23:57:58'),
(38, 8, 16, 50.00, NULL, '2025-10-27 23:58:00', 'calificada', 0, NULL, '2025-10-27 23:58:23', '2025-10-27 23:58:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `clave` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('string','number','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clave_unique` (`clave`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `created_at`, `updated_at`) VALUES
(1, 'nota_minima_aprobacion', '60', 'Nota mínima que debe obtener un estudiante para aprobar una materia', 'number', '2025-10-20 17:30:00', '2025-10-28 17:32:46'),
(2, 'max_estudiantes_seccion', '30', 'Cantidad máxima de estudiantes permitidos por sección', 'number', '2025-10-20 17:30:00', '2025-10-27 16:38:12'),
(3, 'sistema_nombre', 'Colegio Secundario Augusto Pulenta', 'Nombre del sistema que aparece en títulos y encabezados', 'string', '2025-10-20 17:30:00', '2025-10-31 13:45:30'),
(4, 'timezone', 'America/Argentina/San_Juan', 'Zona horaria del sistema para fechas y horas', 'string', '2025-10-20 17:30:00', '2025-10-28 17:32:53'),
(5, 'formato_fecha', 'Y-m-d', 'Formato de visualización de fechas en el sistema', 'string', '2025-10-20 17:30:00', '2025-10-20 17:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_curso` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `creditos` int NOT NULL DEFAULT '3',
  `horas_semanales` int NOT NULL DEFAULT '4',
  `nivel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrera` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requisitos` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cursos_codigo_curso_unique` (`codigo_curso`),
  KEY `cursos_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `codigo_curso`, `nombre`, `descripcion`, `creditos`, `horas_semanales`, `nivel`, `carrera`, `requisitos`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'MAT101', 'Matematica I', 'Fundamentos de algebra', 4, 4, '1', 'Economía y Sociales', NULL, 'activo', '2025-10-20 17:30:00', '2025-10-24 04:09:32'),
(2, 'FIS-001', 'Fisica I', 'Mecanica y cinematica', 4, 4, '1', 'Ciclo Básico', NULL, 'activo', '2025-10-20 17:30:00', '2025-10-23 04:38:40'),
(3, 'PROG101', 'Programacion I', 'Intro a programacion', 5, 5, '1', 'Ciclo Básico', NULL, 'activo', '2025-10-20 17:30:00', '2025-10-23 04:42:18'),
(4, 'EDUF_001', 'EDUCACION FÍSICA', NULL, 2, 2, 'Primer año, Turno mañana', 'Economía y Sociales', NULL, 'activo', '2025-10-22 01:16:05', '2025-10-23 04:41:14'),
(5, 'FRA-001', 'Frances', 'idiomas', 3, 2, 'primer año, tercer ciclo', 'Economía y Sociales', NULL, 'activo', '2025-10-25 17:05:22', '2025-10-25 17:05:22'),
(6, 'BIOLOGIA-001', 'Biologia', NULL, 3, 3, '1er Año', 'Ciclo Básico', NULL, 'activo', '2025-10-25 22:12:22', '2025-10-25 22:12:22'),
(7, 'INGLES-002', 'INGLES ll', 'Formación de Past Simple, Past Perfect', 3, 3, '5º Año, 2 Trimestre', 'Turno Tarde', NULL, 'activo', '2025-10-31 13:48:46', '2025-10-31 13:48:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

DROP TABLE IF EXISTS `evaluaciones`;
CREATE TABLE IF NOT EXISTS `evaluaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seccion_id` bigint UNSIGNED NOT NULL,
  `tipo_evaluacion_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_evaluacion` date DEFAULT NULL,
  `fecha_limite` datetime DEFAULT NULL,
  `nota_maxima` decimal(5,2) NOT NULL DEFAULT '100.00',
  `porcentaje` decimal(5,2) DEFAULT NULL,
  `estado` enum('programada','activa','finalizada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `instrucciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluaciones_tipo_evaluacion_id_foreign` (`tipo_evaluacion_id`),
  KEY `evaluaciones_seccion_id_index` (`seccion_id`),
  KEY `evaluaciones_fecha_evaluacion_index` (`fecha_evaluacion`),
  KEY `evaluaciones_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `seccion_id`, `tipo_evaluacion_id`, `nombre`, `descripcion`, `fecha_evaluacion`, `fecha_limite`, `nota_maxima`, `porcentaje`, `estado`, `instrucciones`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Examen Parcial 1', 'Evaluacion correspondiente a Examen Parcial 1', '2025-10-30', '2025-10-30 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(2, 1, 8, 'Proyecto 1', 'Evaluacion correspondiente a Proyecto 1', '2025-11-09', '2025-11-09 16:30:00', 100.00, 20.00, 'programada', 'Instrucciones para Proyecto 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(3, 1, 7, 'Examen Parcial 2', 'Evaluacion correspondiente a Examen Parcial 2', '2025-11-29', '2025-11-29 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 2', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(4, 1, 2, 'Examen Final', 'Evaluacion correspondiente a Examen Final', '2025-12-19', '2025-12-19 16:30:00', 100.00, 30.00, 'programada', 'Instrucciones para Examen Final', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(5, 2, 6, 'Examen Parcial 1', 'Evaluacion correspondiente a Examen Parcial 1', '2025-10-30', '2025-10-30 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(6, 2, 3, 'Proyecto 1', 'Evaluacion correspondiente a Proyecto 1', '2025-11-09', '2025-11-09 16:30:00', 100.00, 20.00, 'programada', 'Instrucciones para Proyecto 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(7, 2, 7, 'Examen Parcial 2', 'Evaluacion correspondiente a Examen Parcial 2', '2025-11-29', '2025-11-29 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 2', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(8, 2, 2, 'Examen Final', 'Evaluacion correspondiente a Examen Final', '2025-12-19', '2025-12-19 16:30:00', 100.00, 30.00, 'programada', 'Instrucciones para Examen Final', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(9, 3, 8, 'Examen Parcial 1', 'Evaluacion correspondiente a Examen Parcial 1', '2025-10-30', '2025-10-30 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(10, 3, 4, 'Proyecto 1', 'Evaluacion correspondiente a Proyecto 1', '2025-11-09', '2025-11-09 16:30:00', 100.00, 20.00, 'programada', 'Instrucciones para Proyecto 1', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(11, 3, 5, 'Examen Parcial 2', 'Evaluacion correspondiente a Examen Parcial 2', '2025-11-29', '2025-11-29 16:30:00', 100.00, 25.00, 'programada', 'Instrucciones para Examen Parcial 2', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(12, 3, 8, 'Examen Final', 'Evaluacion correspondiente a Examen Final', '2025-12-19', '2025-12-19 16:30:00', 100.00, 30.00, 'programada', 'Instrucciones para Examen Final', '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(13, 5, 5, 'Multiple Choise', NULL, '2025-10-15', NULL, 100.00, 70.00, 'programada', NULL, '2025-10-25 18:04:59', '2025-10-25 18:04:59'),
(14, 5, 2, 'examen final', NULL, '2025-11-18', NULL, 100.00, 30.00, 'programada', NULL, '2025-10-25 18:36:06', '2025-10-25 18:36:06'),
(15, 4, 2, 'examen final edfisica', NULL, '2025-11-18', NULL, 100.00, 24.00, 'programada', NULL, '2025-10-25 22:10:27', '2025-10-25 22:10:27'),
(16, 6, 3, 'Seres Vivos', 'Tarea numero 1', '2025-10-28', NULL, 100.00, 40.00, 'programada', NULL, '2025-10-25 22:14:53', '2025-10-25 22:14:53'),
(17, 1, 2, 'examen final', NULL, '2025-10-27', NULL, 100.00, NULL, 'programada', NULL, '2025-10-27 16:52:35', '2025-10-27 16:52:35'),
(18, 2, 4, 'proyecto', NULL, '2025-10-14', NULL, 100.00, NULL, 'programada', NULL, '2025-10-27 23:55:37', '2025-10-27 23:55:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seccion_id` bigint UNSIGNED NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `aula` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `horarios_seccion_id_index` (`seccion_id`),
  KEY `horarios_dia_semana_index` (`dia_semana`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `seccion_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `aula`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lunes', '08:00:00', '10:00:00', 'Aula 3, Presencial', '2025-10-28 17:26:45', '2025-10-28 17:26:45'),
(2, 1, 'Jueves', '09:00:00', '10:30:00', 'Aula 3, Presencial', '2025-10-28 17:39:45', '2025-10-28 17:39:45'),
(3, 5, 'Lunes', '10:00:00', '11:30:00', 'Aula 4, Presencial', '2025-10-31 21:30:23', '2025-10-31 21:30:23'),
(4, 5, 'Miércoles', '08:00:00', '11:00:00', 'Aula 5, Presencial', '2025-10-31 21:31:44', '2025-10-31 21:31:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `estudiante_id` bigint UNSIGNED NOT NULL,
  `seccion_id` bigint UNSIGNED NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_retiro` timestamp NULL DEFAULT NULL,
  `estado` enum('inscrito','retirado','completado') COLLATE utf8mb4_unicode_ci DEFAULT 'inscrito',
  `nota_final` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_inscripcion` (`estudiante_id`,`seccion_id`),
  KEY `inscripciones_estudiante_id_index` (`estudiante_id`),
  KEY `inscripciones_estado_index` (`estado`),
  KEY `inscripciones_seccion_id_foreign` (`seccion_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `estudiante_id`, `seccion_id`, `fecha_inscripcion`, `fecha_retiro`, `estado`, `nota_final`, `created_at`, `updated_at`) VALUES
(2, 7, 1, '2025-10-20 17:30:00', NULL, 'inscrito', 57.28, '2025-10-20 17:30:00', '2025-10-24 22:58:45'),
(3, 8, 1, '2025-10-20 17:30:00', NULL, 'inscrito', 100.00, '2025-10-20 17:30:00', '2025-10-24 22:58:45'),
(4, 9, 1, '2025-10-20 17:30:00', NULL, 'inscrito', 80.00, '2025-10-20 17:30:00', '2025-10-24 22:58:45'),
(5, 10, 1, '2025-10-20 17:30:00', NULL, 'inscrito', 40.00, '2025-10-20 17:30:00', '2025-10-24 22:58:45'),
(6, 11, 1, '2025-10-20 17:30:00', NULL, 'inscrito', 80.80, '2025-10-20 17:30:00', '2025-10-26 00:05:10'),
(7, 12, 1, '2025-10-20 17:30:00', NULL, 'retirado', 100.00, '2025-10-20 17:30:00', '2025-10-30 16:33:44'),
(9, 7, 2, '2025-10-20 17:30:00', NULL, 'inscrito', NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(10, 8, 2, '2025-10-20 17:30:00', NULL, 'inscrito', NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(11, 9, 2, '2025-10-20 17:30:00', NULL, 'inscrito', 70.00, '2025-10-20 17:30:00', '2025-10-24 22:30:14'),
(12, 10, 2, '2025-10-20 17:30:00', NULL, 'inscrito', 23.70, '2025-10-20 17:30:00', '2025-10-24 04:12:55'),
(13, 11, 2, '2025-10-20 17:30:00', NULL, 'inscrito', NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(14, 12, 2, '2025-10-20 17:30:00', NULL, 'inscrito', NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(16, 7, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 83.50, '2025-10-20 17:30:00', '2025-10-22 16:46:09'),
(17, 8, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 17.50, '2025-10-20 17:30:00', '2025-10-22 16:39:31'),
(18, 9, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 22.50, '2025-10-20 17:30:00', '2025-10-22 16:39:31'),
(19, 10, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 12.50, '2025-10-20 17:30:00', '2025-10-22 16:39:31'),
(20, 11, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 21.25, '2025-10-20 17:30:00', '2025-10-22 16:39:31'),
(21, 12, 3, '2025-10-20 17:30:00', NULL, 'inscrito', 25.00, '2025-10-20 17:30:00', '2025-10-22 16:39:31'),
(22, 13, 4, '2025-10-22 21:44:00', NULL, 'inscrito', NULL, '2025-10-22 21:44:00', '2025-10-22 21:44:00'),
(23, 12, 4, '2025-10-22 21:44:46', NULL, 'inscrito', 85.02, '2025-10-22 21:44:46', '2025-10-23 04:50:32'),
(24, 8, 4, '2025-10-22 21:44:46', '2025-12-05 03:00:00', 'retirado', NULL, '2025-10-22 21:44:46', '2025-10-24 00:23:14'),
(25, 11, 4, '2025-10-22 21:44:46', NULL, 'inscrito', NULL, '2025-10-22 21:44:46', '2025-10-22 21:44:46'),
(26, 7, 4, '2025-10-22 21:44:46', NULL, 'inscrito', NULL, '2025-10-22 21:44:46', '2025-10-22 21:44:46'),
(27, 10, 4, '2025-10-22 21:44:46', NULL, 'inscrito', NULL, '2025-10-22 21:44:46', '2025-10-22 21:44:46'),
(28, 13, 1, '2025-10-23 17:31:59', NULL, 'inscrito', 70.00, '2025-10-23 17:31:59', '2025-10-24 22:59:26'),
(29, 13, 2, '2025-10-23 18:08:06', '2025-12-07 03:00:00', 'retirado', 83.25, '2025-10-23 18:08:06', '2025-10-24 00:25:03'),
(30, 12, 5, '2025-10-25 17:07:42', NULL, 'inscrito', 100.00, '2025-10-25 17:07:42', '2025-10-25 18:36:59'),
(31, 11, 5, '2025-10-25 17:07:42', NULL, 'inscrito', NULL, '2025-10-25 17:07:42', '2025-10-25 17:07:42'),
(32, 7, 5, '2025-10-25 21:57:04', NULL, 'inscrito', NULL, '2025-10-25 21:57:04', '2025-10-25 21:57:04'),
(33, 13, 5, '2025-10-25 21:57:04', NULL, 'inscrito', NULL, '2025-10-25 21:57:04', '2025-10-25 21:57:04'),
(34, 12, 6, '2025-10-25 22:13:42', NULL, 'inscrito', 79.00, '2025-10-25 22:13:42', '2025-10-25 22:18:44'),
(35, 11, 6, '2025-10-25 22:13:42', NULL, 'inscrito', 60.00, '2025-10-25 22:13:42', '2025-10-25 22:18:44'),
(36, 7, 6, '2025-10-25 22:13:42', NULL, 'inscrito', NULL, '2025-10-25 22:13:42', '2025-10-25 22:13:42'),
(37, 13, 6, '2025-10-25 22:13:42', NULL, 'inscrito', 68.00, '2025-10-25 22:13:42', '2025-10-25 22:18:45'),
(38, 16, 1, '2025-10-27 16:51:24', NULL, 'inscrito', 84.00, '2025-10-27 16:51:24', '2025-10-27 16:56:24'),
(39, 16, 2, '2025-10-27 23:53:00', NULL, 'inscrito', 42.00, '2025-10-27 23:53:00', '2025-10-27 23:58:23'),
(40, 12, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15'),
(41, 11, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15'),
(42, 16, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15'),
(43, 7, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15'),
(44, 15, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15'),
(45, 13, 8, '2025-10-31 21:27:15', NULL, 'inscrito', NULL, '2025-10-31 21:27:15', '2025-10-31 21:27:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_01_16_000000_create_configuraciones_table', 1),
(6, '2025_09_25_192117_create_usuarios_table', 1),
(7, '2025_09_25_192129_create_cursos_table', 1),
(8, '2025_09_25_192141_create_periodos_academicos_table', 1),
(9, '2025_09_25_192152_create_secciones_table', 1),
(10, '2025_09_25_192204_create_inscripciones_table', 1),
(11, '2025_09_25_192215_create_tipos_evaluacion_table', 1),
(12, '2025_09_25_192228_create_evaluaciones_table', 1),
(13, '2025_09_25_192240_create_calificaciones_table', 1),
(14, '2025_09_25_192253_create_asistencias_table', 1),
(15, '2025_09_25_192317_create_notificaciones_table', 1),
(16, '2025_10_17_000000_update_codigo_seccion_length_in_secciones_table', 1),
(17, '2025_10_17_173800_add_horas_semanales_and_requisitos_to_cursos_table', 1),
(18, '2025_10_20_133203_update_estado_enum_in_periodos_academicos_table', 1),
(19, '2025_10_20_133451_add_missing_columns_to_periodos_academicos_table', 1),
(20, '2025_10_20_133715_add_periodo_id_to_secciones_table', 1),
(21, '2025_10_20_135003_add_modalidad_to_secciones_table', 1),
(22, '2025_10_20_135915_fix_inscripciones_estado_values', 1),
(25, '2025_10_20_191134_add_fecha_retiro_to_inscripciones_table', 2),
(26, '2025_10_21_200937_create_horarios_table', 2),
(27, '2025_10_22_184024_make_nombre_nullable_in_secciones_table', 3),
(28, '2025_10_23_201510_fix_asistencias_estado_column', 4),
(29, '2025_10_24_190115_change_cascade_to_restrict_in_foreign_keys', 5),
(30, '2025_10_25_135151_remove_hibrida_from_secciones_modalidad', 6),
(31, '2025_10_25_000001_alter_evaluaciones_make_porcentaje_nullable', 7),
(35, '2025_11_01_000000_add_estado_estudiante_to_usuarios_table', 8),
(36, '2025_11_01_000001_create_tutores_table', 8),
(37, '2025_11_01_000002_add_tutor_id_to_usuarios_table', 8),
(38, '2025_11_01_000003_make_cedula_nullable_in_tutores_table', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('info','warning','success','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificaciones_usuario_id_index` (`usuario_id`),
  KEY `notificaciones_leida_index` (`leida`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos_academicos`
--

DROP TABLE IF EXISTS `periodos_academicos`;
CREATE TABLE IF NOT EXISTS `periodos_academicos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciclo_escolar` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `año_academico` year DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activo','Inactivo','Finalizado') COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periodos_academicos_codigo_unique` (`codigo`),
  KEY `periodos_academicos_estado_index` (`estado`),
  KEY `periodos_academicos_fecha_inicio_fecha_fin_index` (`fecha_inicio`,`fecha_fin`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `periodos_academicos`
--

INSERT INTO `periodos_academicos` (`id`, `codigo`, `nombre`, `ciclo_escolar`, `año_academico`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'P2025-1', 'Primer Semestre 2025', NULL, NULL, '2025-03-01', '2025-05-30', 'Finalizado', '2025-10-20 17:30:00', '2025-10-20 18:34:48'),
(2, 'PA-2025-001', 'Ciclo lectivo 2025, Tercer Trimestre', '2025-2026', '2025', '2025-09-12', '2025-12-03', 'Finalizado', '2025-10-20 17:31:15', '2025-10-31 20:22:32'),
(3, 'PA-2025-002', 'Ciclo lectivo 2025, Segundo Trimestre', '2025-2026', '2025', '2025-06-10', '2025-09-01', 'Activo', '2025-10-20 18:32:18', '2025-10-31 13:53:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

DROP TABLE IF EXISTS `secciones`;
CREATE TABLE IF NOT EXISTS `secciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `curso_id` bigint UNSIGNED NOT NULL,
  `periodo_academico_id` bigint UNSIGNED NOT NULL,
  `profesor_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_seccion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cupo_maximo` int NOT NULL DEFAULT '30',
  `horario` text COLLATE utf8mb4_unicode_ci,
  `aula` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modalidad` enum('presencial','virtual') COLLATE utf8mb4_unicode_ci DEFAULT 'presencial',
  `estado` enum('activo','inactivo','finalizado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_seccion` (`curso_id`,`periodo_academico_id`,`codigo_seccion`),
  KEY `secciones_periodo_academico_id_foreign` (`periodo_academico_id`),
  KEY `secciones_profesor_id_index` (`profesor_id`),
  KEY `secciones_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `curso_id`, `periodo_academico_id`, `profesor_id`, `nombre`, `codigo_seccion`, `cupo_maximo`, `horario`, `aula`, `modalidad`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, 'A', 'SEC-001', 30, NULL, 'Aula 1', 'presencial', 'activo', '2025-10-20 17:30:00', '2025-10-24 04:09:49'),
(2, 2, 2, 5, 'A', 'SEC-002', 30, NULL, 'Aula 2', 'virtual', 'activo', '2025-10-20 17:30:00', '2025-10-20 18:29:10'),
(3, 3, 2, 4, 'A', 'SEC-003', 30, NULL, 'Aula 3', 'presencial', 'activo', '2025-10-20 17:30:00', '2025-10-25 16:40:36'),
(4, 4, 2, 14, NULL, 'EDUF_001', 26, NULL, NULL, 'presencial', 'activo', '2025-10-22 21:43:27', '2025-10-22 21:43:27'),
(5, 5, 2, 6, NULL, 'FRA-001', 70, NULL, NULL, 'virtual', 'activo', '2025-10-25 17:06:57', '2025-10-25 17:06:57'),
(6, 6, 2, 5, NULL, 'BIOLOGIA-001', 40, NULL, NULL, 'presencial', 'activo', '2025-10-25 22:13:05', '2025-10-25 22:13:05'),
(7, 7, 3, 17, NULL, 'INGLES-002', 49, NULL, NULL, 'presencial', 'activo', '2025-10-31 13:54:04', '2025-10-31 13:54:04'),
(8, 2, 3, 14, NULL, 'FIS_001', 30, NULL, NULL, 'presencial', 'activo', '2025-10-31 21:26:31', '2025-10-31 21:26:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_evaluacion`
--

DROP TABLE IF EXISTS `tipos_evaluacion`;
CREATE TABLE IF NOT EXISTS `tipos_evaluacion` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_evaluacion`
--

INSERT INTO `tipos_evaluacion` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Examen Parcial', 'Evaluación parcial del contenido', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(2, 'Examen Final', 'Evaluación final acumulativa', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(3, 'Tarea', 'Tarea o ejercicio asignado', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(4, 'Proyecto', 'Proyecto práctico o investigación', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(5, 'Quiz', 'Evaluación rápida', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(6, 'Laboratorio', 'Práctica de laboratorio', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(7, 'Presentación', 'Exposición oral', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54'),
(8, 'Participación', 'Participación en clase', 1, '2025-10-20 17:29:54', '2025-10-20 17:29:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutores`
--

DROP TABLE IF EXISTS `tutores`;
CREATE TABLE IF NOT EXISTS `tutores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `parentesco` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tutores`
--

INSERT INTO `tutores` (`id`, `nombre`, `apellido`, `cedula`, `telefono`, `email`, `direccion`, `parentesco`, `created_at`, `updated_at`) VALUES
(1, 'Claudio', 'Moreira', NULL, '2646913872190', 'ClaudioMoreira@gmail.com', NULL, 'padre', '2025-11-01 20:33:30', '2025-11-01 21:28:26'),
(2, 'milena', 'salgueiro', NULL, '213º41414', NULL, NULL, 'madre', '2025-11-01 22:46:14', '2025-11-01 22:46:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `fecha_nacimiento` date DEFAULT NULL,
  `tipo_usuario` enum('estudiante','profesor','administrador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'estudiante',
  `estado` enum('activo','inactivo','suspendido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `estado_estudiante` enum('regular','suspendido','libre','preinscripto') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tutor_id` bigint UNSIGNED DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_tipo_usuario_index` (`tipo_usuario`),
  KEY `usuarios_estado_index` (`estado`),
  KEY `usuarios_tutor_id_foreign` (`tutor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `direccion`, `fecha_nacimiento`, `tipo_usuario`, `estado`, `estado_estudiante`, `tutor_id`, `password`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Sistema', 'admin@sistema.edu', NULL, NULL, NULL, 'administrador', 'activo', NULL, NULL, '$2y$10$7EJ5aq5fRDDgEbB.WHbrHuo.QVICJaR47CRZV8hBM/D2k6kTGd9yO', NULL, NULL, '2025-10-20 17:29:59', '2025-10-20 17:29:59'),
(2, 'Profesor', 'Test', 'profesor@test.com', NULL, NULL, NULL, 'profesor', 'activo', NULL, NULL, '$2y$10$nBXD2zQtfE6IZB/NE1903eI0IkU4qj9ic97gAcV/3r/ohsHbVe2nC', NULL, NULL, '2025-10-20 17:29:59', '2025-10-20 17:29:59'),
(13, 'Sebastian', 'Toledo', 'sebatoledo@gmail.com', '264672761', NULL, '2025-01-16', 'estudiante', 'activo', NULL, NULL, '$2y$10$sAYi79B1r3Qn5I2ZrBRm8uFH2713qggwi1e.ZrIwjpZCIHkaXg4yy', NULL, NULL, '2025-10-22 21:32:36', '2025-10-22 21:32:36'),
(4, 'Juan', 'Perez', 'juan.perez@escuela.com', NULL, NULL, NULL, 'profesor', 'activo', NULL, NULL, '$2y$10$detXqJfBYqruOdASl.m37ezY3bfiVNjMYB7n0acmcPoHzO6ifKctG', NULL, NULL, '2025-10-20 17:29:59', '2025-10-20 17:29:59'),
(5, 'Maria', 'Gonzalez', 'maria.gonzalez@escuela.com', NULL, NULL, NULL, 'profesor', 'activo', NULL, NULL, '$2y$10$QhYqpwvHcN4WMoz0CtI8XeDqNW2p.O6Cqs0tAdYhzwgT6df.95Hma', NULL, NULL, '2025-10-20 17:30:00', '2025-10-31 13:38:12'),
(6, 'Carlos', 'Rodriguez', 'carlos.rodriguez@escuela.com', NULL, NULL, NULL, 'profesor', 'activo', NULL, NULL, '$2y$10$TY59zvOI758DDlwn7HqrWOXMqO8ZcmVIL8tZxFn8hV5MXQjYuxj2u', NULL, NULL, '2025-10-20 17:30:00', '2025-10-23 04:30:19'),
(7, 'Pedro', 'Sanchez', 'pedro.sanchez@estudiante.com', NULL, NULL, NULL, 'estudiante', 'activo', NULL, NULL, '$2y$10$goETuMK6DyJSvdUNUskrpu0NttNhBNo3zlhmKz1DkNpkjOlC8djX6', NULL, NULL, '2025-10-20 17:30:00', '2025-10-22 21:28:06'),
(8, 'Laura', 'Fernandez', 'laura.fernandez@estudiante.com', NULL, NULL, NULL, 'estudiante', 'inactivo', NULL, NULL, '$2y$10$b3lIO10LYpJq9pY5e2z22eSt.aSqDvoVNbcl9lBR2WcDZJKTs5uWS', NULL, NULL, '2025-10-20 17:30:00', '2025-10-23 04:30:07'),
(9, 'Diego', 'Ramirez', 'diego.ramirez@estudiante.com', NULL, NULL, NULL, 'estudiante', 'suspendido', NULL, NULL, '$2y$10$DwmoRjHXAIehG5e/UFF1r.8g5gIQwMj4Gl1MphKEJUYlX/BqXncre', NULL, NULL, '2025-10-20 17:30:00', '2025-10-20 18:25:34'),
(10, 'Sofia', 'Torres', 'sofia.torres@estudiante.com', NULL, NULL, NULL, 'estudiante', 'inactivo', NULL, NULL, '$2y$10$kUjFylZCf70.QTYehnulFefP45VfrV8N7ZJRA1XxWxssk8WxaQ/dK', NULL, NULL, '2025-10-20 17:30:00', '2025-10-23 04:29:57'),
(11, 'Miguel', 'Flores', 'miguel.flores@estudiante.com', NULL, NULL, '2007-10-20', 'estudiante', 'activo', NULL, NULL, '$2y$10$7OqIGgNZpiUx.Rq3Yhgufur1Jihs1qAxFIdswhSNAVqrAgcgYCbIe', NULL, NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(12, 'Valentina', 'Castro', 'valentina.castro@estudiante.com', NULL, NULL, '2007-10-20', 'estudiante', 'activo', NULL, NULL, '$2y$10$D0N9ANrlWEjExxh0lWvT3exwanAfLI5kYDGPSXDuonZo356YJKFBK', NULL, NULL, '2025-10-20 17:30:00', '2025-10-20 17:30:00'),
(14, 'Geronimo', 'Morales', 'geromorales@gmail.com', '2124238741248', NULL, '1994-09-23', 'profesor', 'activo', NULL, NULL, '$2y$10$9zrt1ZserItIyAGQdc6tUOcgK09.4kQr2WFultltckU7vYF5Tdjfi', NULL, NULL, '2025-10-22 21:42:47', '2025-10-22 21:42:47'),
(15, 'Samuel', 'Frias', 'estudiante@test.com', '1241251313', NULL, '2000-05-12', 'estudiante', 'activo', 'regular', 2, '$2y$10$5wp87O4yRlNoPYomOctvM.DleJ/SBGcy.5RKk9OJyIa3lWnRhy6VK', NULL, NULL, '2025-10-27 16:38:12', '2025-11-01 22:46:14'),
(16, 'facundo', 'leyes', 'facundoleyes@gmail.com', '2341353151q4', NULL, '2000-02-04', 'estudiante', 'activo', 'regular', 1, '$2y$10$RE2CtL5ON0AOpWzmsFNv6O6.CmJB/0FuGuOATB/e0EQ7zdWzn78aG', NULL, NULL, '2025-10-27 16:50:36', '2025-11-01 22:59:11'),
(17, 'Marcelo', 'Herrera', 'marceloherrera1782@gmail.com', '124355364', NULL, '1995-07-21', 'profesor', 'activo', NULL, NULL, '$2y$10$tScWiZBxyRJdT0wi.N3Wb.o6peC.SXcHMaSwlH0XAGsULWAq6XKkK', NULL, NULL, '2025-10-31 13:51:43', '2025-10-31 13:51:43');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estudiantes_activos`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vista_estudiantes_activos`;
CREATE TABLE IF NOT EXISTS `vista_estudiantes_activos` (
`id` bigint unsigned
,`nombre` varchar(100)
,`apellido` varchar(100)
,`email` varchar(191)
,`telefono` varchar(20)
,`fecha_nacimiento` date
,`cursos_inscritos` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_eventos_proximos`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vista_eventos_proximos`;
CREATE TABLE IF NOT EXISTS `vista_eventos_proximos` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_notas_por_estudiante`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vista_notas_por_estudiante`;
CREATE TABLE IF NOT EXISTS `vista_notas_por_estudiante` (
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_periodos_completa`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vista_periodos_completa`;
CREATE TABLE IF NOT EXISTS `vista_periodos_completa` (
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estudiantes_activos`
--
DROP TABLE IF EXISTS `vista_estudiantes_activos`;

DROP VIEW IF EXISTS `vista_estudiantes_activos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estudiantes_activos`  AS SELECT `u`.`id` AS `id`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`email` AS `email`, `u`.`telefono` AS `telefono`, `u`.`fecha_nacimiento` AS `fecha_nacimiento`, count(distinct `i`.`seccion_id`) AS `cursos_inscritos` FROM (`usuarios` `u` left join `inscripciones` `i` on(((`u`.`id` = `i`.`estudiante_id`) and (`i`.`estado` = 'inscrito')))) WHERE ((`u`.`tipo_usuario` = 'estudiante') AND (`u`.`estado` = 'activo')) GROUP BY `u`.`id`, `u`.`nombre`, `u`.`apellido`, `u`.`email`, `u`.`telefono`, `u`.`fecha_nacimiento` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_eventos_proximos`
--
DROP TABLE IF EXISTS `vista_eventos_proximos`;

DROP VIEW IF EXISTS `vista_eventos_proximos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_eventos_proximos`  AS SELECT `pe`.`id` AS `id`, `pe`.`periodo_id` AS `periodo_id`, `pe`.`nombre` AS `nombre`, `pe`.`descripcion` AS `descripcion`, `pe`.`tipo_evento` AS `tipo_evento`, `pe`.`fecha_inicio` AS `fecha_inicio`, `pe`.`fecha_fin` AS `fecha_fin`, `pe`.`hora_inicio` AS `hora_inicio`, `pe`.`hora_fin` AS `hora_fin`, `pe`.`suspende_clases` AS `suspende_clases`, `pe`.`es_obligatorio` AS `es_obligatorio`, `pe`.`color` AS `color`, `pe`.`ubicacion` AS `ubicacion`, `pe`.`created_at` AS `created_at`, `pe`.`updated_at` AS `updated_at`, `p`.`nombre` AS `periodo_nombre`, `p`.`ciclo_escolar` AS `ciclo_escolar`, (to_days(`pe`.`fecha_inicio`) - to_days(curdate())) AS `dias_hasta_evento` FROM (`periodo_eventos` `pe` join `periodos_academicos` `p` on((`pe`.`periodo_id` = `p`.`id`))) WHERE (`pe`.`fecha_inicio` >= curdate()) ORDER BY `pe`.`fecha_inicio` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_notas_por_estudiante`
--
DROP TABLE IF EXISTS `vista_notas_por_estudiante`;

DROP VIEW IF EXISTS `vista_notas_por_estudiante`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_notas_por_estudiante`  AS SELECT `u`.`id` AS `estudiante_id`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`email` AS `email`, `c`.`codigo_curso` AS `codigo_curso`, `c`.`nombre_curso` AS `curso`, `s`.`codigo_seccion` AS `codigo_seccion`, `pa`.`nombre` AS `periodo`, `pa`.`ciclo_escolar` AS `ciclo_escolar`, `i`.`nota_final` AS `nota_final`, `i`.`estado` AS `estado_inscripcion`, concat(`p`.`nombre`,' ',`p`.`apellido`) AS `profesor` FROM (((((`inscripciones` `i` join `usuarios` `u` on((`i`.`estudiante_id` = `u`.`id`))) join `secciones` `s` on((`i`.`seccion_id` = `s`.`id`))) join `cursos` `c` on((`s`.`curso_id` = `c`.`id`))) join `periodos_academicos` `pa` on((`s`.`periodo_id` = `pa`.`id`))) join `usuarios` `p` on((`s`.`profesor_id` = `p`.`id`))) WHERE (`u`.`tipo_usuario` = 'estudiante') ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_periodos_completa`
--
DROP TABLE IF EXISTS `vista_periodos_completa`;

DROP VIEW IF EXISTS `vista_periodos_completa`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_periodos_completa`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`codigo` AS `codigo`, `p`.`ciclo_escolar` AS `ciclo_escolar`, `p`.`año_academico` AS `año_academico`, `p`.`periodo_tipo` AS `periodo_tipo`, `p`.`numero_periodo` AS `numero_periodo`, `p`.`fecha_inicio` AS `fecha_inicio`, `p`.`fecha_fin` AS `fecha_fin`, `p`.`fecha_inicio_inscripciones` AS `fecha_inicio_inscripciones`, `p`.`fecha_fin_inscripciones` AS `fecha_fin_inscripciones`, `p`.`fecha_inicio_clases` AS `fecha_inicio_clases`, `p`.`fecha_fin_clases` AS `fecha_fin_clases`, `p`.`fecha_inicio_examenes` AS `fecha_inicio_examenes`, `p`.`fecha_fin_examenes` AS `fecha_fin_examenes`, `p`.`fecha_entrega_notas` AS `fecha_entrega_notas`, `p`.`dias_habiles` AS `dias_habiles`, `p`.`total_semanas` AS `total_semanas`, `p`.`vacaciones` AS `vacaciones`, `p`.`fechas_especiales` AS `fechas_especiales`, `p`.`descripcion` AS `descripcion`, `p`.`observaciones` AS `observaciones`, `p`.`permite_inscripciones` AS `permite_inscripciones`, `p`.`permite_modificar_notas` AS `permite_modificar_notas`, `p`.`requiere_asistencia` AS `requiere_asistencia`, `p`.`estado` AS `estado`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, (to_days(`p`.`fecha_fin`) - to_days(`p`.`fecha_inicio`)) AS `duracion_total_dias`, (case when (`p`.`fecha_fin` < curdate()) then 0 else (to_days(`p`.`fecha_fin`) - to_days(curdate())) end) AS `dias_restantes`, (case when (`p`.`fecha_inicio` > curdate()) then 0 else (to_days(curdate()) - to_days(`p`.`fecha_inicio`)) end) AS `dias_transcurridos`, (case when ((curdate() between `p`.`fecha_inicio` and `p`.`fecha_fin`) and (`p`.`estado` = 'activo')) then true else false end) AS `es_vigente`, count(distinct `s`.`id`) AS `total_secciones`, count(distinct `s`.`curso_id`) AS `total_cursos`, count(distinct `i`.`estudiante_id`) AS `total_estudiantes` FROM ((`periodos_academicos` `p` left join `secciones` `s` on((`p`.`id` = `s`.`periodo_id`))) left join `inscripciones` `i` on(((`s`.`id` = `i`.`seccion_id`) and (`i`.`estado` = 'inscrito')))) GROUP BY `p`.`id` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
