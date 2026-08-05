-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para alpadent
CREATE DATABASE IF NOT EXISTS `alpadent` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `alpadent`;

-- Volcando estructura para tabla alpadent.abonos
CREATE TABLE IF NOT EXISTS `abonos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pago_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','tarjeta') NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `abonos_pago_id_index` (`pago_id`),
  KEY `abonos_usuario_id_index` (`usuario_id`),
  KEY `abonos_fecha_index` (`fecha`),
  CONSTRAINT `abonos_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `abonos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.abonos: ~2 rows (aproximadamente)
INSERT INTO `abonos` (`id`, `pago_id`, `usuario_id`, `monto`, `metodo_pago`, `referencia`, `observaciones`, `fecha`) VALUES
	(1, 1, 1, 10.00, 'efectivo', NULL, NULL, '2026-05-03 01:33:34'),
	(2, 1, 1, 10.00, 'efectivo', NULL, NULL, '2026-05-03 01:33:54'),
	(3, 1, 1, 10.00, 'efectivo', NULL, NULL, '2026-05-03 01:34:14');

-- Volcando estructura para tabla alpadent.abonos_venta_producto
CREATE TABLE IF NOT EXISTS `abonos_venta_producto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','tarjeta') NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `abonos_venta_producto_venta_id_index` (`venta_id`),
  KEY `abonos_venta_producto_usuario_id_index` (`usuario_id`),
  KEY `abonos_venta_producto_fecha_index` (`fecha`),
  CONSTRAINT `abonos_venta_producto_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `abonos_venta_producto_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas_producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.abonos_venta_producto: ~0 rows (aproximadamente)
INSERT INTO `abonos_venta_producto` (`id`, `venta_id`, `usuario_id`, `monto`, `metodo_pago`, `referencia`, `observaciones`, `fecha`) VALUES
	(2, 2, 1, 16.00, 'efectivo', NULL, NULL, '2026-05-03 03:13:09');

-- Volcando estructura para tabla alpadent.antecedentes_medicos
CREATE TABLE IF NOT EXISTS `antecedentes_medicos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint(20) unsigned NOT NULL,
  `diabetes` tinyint(1) NOT NULL DEFAULT 0,
  `alergias_medicamentos` tinyint(1) NOT NULL DEFAULT 0,
  `detalle_alergias` text DEFAULT NULL,
  `problemas_hemorragicos` tinyint(1) NOT NULL DEFAULT 0,
  `problemas_cardiacos` tinyint(1) NOT NULL DEFAULT 0,
  `problemas_renales` tinyint(1) NOT NULL DEFAULT 0,
  `embarazo` tinyint(1) NOT NULL DEFAULT 0,
  `otros` text DEFAULT NULL,
  `presion_arterial` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bajo_tratamiento_medico` tinyint(1) NOT NULL DEFAULT 0,
  `hipertenso` tinyint(1) NOT NULL DEFAULT 0,
  `motivo_consulta_inicial` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `antecedentes_medicos_paciente_id_unique` (`paciente_id`),
  KEY `antecedentes_medicos_paciente_id_index` (`paciente_id`),
  CONSTRAINT `antecedentes_medicos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.antecedentes_medicos: ~2 rows (aproximadamente)
INSERT INTO `antecedentes_medicos` (`id`, `paciente_id`, `diabetes`, `alergias_medicamentos`, `detalle_alergias`, `problemas_hemorragicos`, `problemas_cardiacos`, `problemas_renales`, `embarazo`, `otros`, `presion_arterial`, `created_at`, `updated_at`, `bajo_tratamiento_medico`, `hipertenso`, `motivo_consulta_inicial`) VALUES
	(2, 7, 1, 0, NULL, 0, 0, 0, 1, NULL, NULL, '2026-05-02 22:18:20', '2026-05-02 22:19:15', 0, 0, NULL),
	(3, 8, 0, 0, NULL, 0, 0, 0, 0, NULL, NULL, '2026-05-02 22:20:40', '2026-05-02 22:20:40', 0, 0, NULL);

-- Volcando estructura para tabla alpadent.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.cache: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.cache_locks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo_tratamiento_id` bigint(20) unsigned DEFAULT NULL,
  `doctor_externo_id` bigint(20) unsigned DEFAULT NULL,
  `fecha_hora_inicio` datetime NOT NULL,
  `motivo_consulta` varchar(255) NOT NULL,
  `estado` enum('programada','en_curso','completada','cancelada','no_asistio') NOT NULL DEFAULT 'programada',
  `observaciones` text DEFAULT NULL,
  `es_primera_vez` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_paciente_id_index` (`paciente_id`),
  KEY `citas_usuario_id_index` (`usuario_id`),
  KEY `citas_doctor_externo_id_index` (`doctor_externo_id`),
  KEY `citas_fecha_hora_inicio_estado_index` (`fecha_hora_inicio`,`estado`),
  KEY `citas_estado_index` (`estado`),
  KEY `citas_tipo_tratamiento_id_foreign` (`tipo_tratamiento_id`),
  CONSTRAINT `citas_doctor_externo_id_foreign` FOREIGN KEY (`doctor_externo_id`) REFERENCES `doctores_externos` (`id`),
  CONSTRAINT `citas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `citas_tipo_tratamiento_id_foreign` FOREIGN KEY (`tipo_tratamiento_id`) REFERENCES `tipos_tratamiento` (`id`),
  CONSTRAINT `citas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.citas: ~8 rows (aproximadamente)
INSERT INTO `citas` (`id`, `paciente_id`, `usuario_id`, `tipo_tratamiento_id`, `doctor_externo_id`, `fecha_hora_inicio`, `motivo_consulta`, `estado`, `observaciones`, `es_primera_vez`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL, '2026-05-02 14:00:00', 'Hacerse una limpiieza', 'cancelada', '....', 1, '2026-05-02 18:45:59', '2026-05-02 19:51:02'),
	(2, 2, 1, NULL, NULL, '2026-05-02 14:30:00', 'Hacerse una limpiieza', 'cancelada', '----', 1, '2026-05-02 19:01:41', '2026-05-02 19:50:54'),
	(3, 3, 1, NULL, NULL, '2026-05-02 15:00:00', 'Hacerse una limpiieza', 'cancelada', '....', 1, '2026-05-02 19:03:48', '2026-05-02 19:50:43'),
	(4, 1, 1, NULL, NULL, '2026-05-02 15:30:00', 'Hacerse una limpiieza', 'cancelada', '---', 1, '2026-05-02 19:51:25', '2026-05-02 19:56:51'),
	(5, 1, 1, NULL, NULL, '2026-05-02 15:30:00', 'Hacerse una limpiieza', 'cancelada', 'No vino el paciente', 1, '2026-05-02 19:57:18', '2026-05-02 20:16:06'),
	(6, 1, 1, NULL, NULL, '2026-05-02 15:30:00', 'Hacerse una limpiieza', 'completada', '---', 1, '2026-05-02 20:17:01', '2026-05-02 20:17:13'),
	(8, 3, 1, 41, NULL, '2026-05-03 14:00:00', 'Porque queria verse guapo aja', 'cancelada', 'NINGUNA', 1, '2026-05-02 22:56:23', '2026-05-02 23:06:03'),
	(9, 8, 1, 3, NULL, '2026-05-03 15:00:00', 'Porque queria verse guapo aja', 'no_asistio', 'El paciente no asistio', 1, '2026-05-02 23:07:09', '2026-05-02 23:13:43'),
	(10, 7, 1, 3, NULL, '2026-05-03 10:20:00', 'Porque queria verse guapo aja', 'programada', '---', 1, '2026-05-03 00:16:49', '2026-05-03 00:16:49'),
	(11, 3, 1, 4, NULL, '2026-05-03 11:20:00', 'Porque queria verse guapo aja', 'programada', '---', 1, '2026-05-03 00:17:43', '2026-05-03 00:17:43');

-- Volcando estructura para tabla alpadent.detalle_venta
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_venta_venta_id_index` (`venta_id`),
  KEY `detalle_venta_producto_id_index` (`producto_id`),
  CONSTRAINT `detalle_venta_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `detalle_venta_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas_producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.detalle_venta: ~2 rows (aproximadamente)
INSERT INTO `detalle_venta` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
	(2, 2, 15, 2, 5.50, 11.00),
	(3, 2, 4, 1, 5.00, 5.00);

-- Volcando estructura para tabla alpadent.diagnosticos
CREATE TABLE IF NOT EXISTS `diagnosticos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cita_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `descripcion` text NOT NULL,
  `indice_cpo_cariados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_cpo_perdidos` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_cpo_obturados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `gingivitis` tinyint(1) NOT NULL DEFAULT 0,
  `enfermedad_periodontal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diagnosticos_cita_id_unique` (`cita_id`),
  KEY `diagnosticos_cita_id_index` (`cita_id`),
  KEY `diagnosticos_usuario_id_index` (`usuario_id`),
  CONSTRAINT `diagnosticos_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  CONSTRAINT `diagnosticos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.diagnosticos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.dientes_diagnostico
CREATE TABLE IF NOT EXISTS `dientes_diagnostico` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `diagnostico_id` bigint(20) unsigned NOT NULL,
  `numero_diente` tinyint(3) unsigned NOT NULL,
  `condicion` enum('sano','cariado','obturado','faltante','con_tratamiento_radicular','con_corona','con_puente','implante','ausente') NOT NULL,
  `superficie` set('oclusal','vestibular','lingual','mesial','distal') DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dientes_diagnostico_diagnostico_id_index` (`diagnostico_id`),
  KEY `dientes_diagnostico_numero_diente_index` (`numero_diente`),
  CONSTRAINT `dientes_diagnostico_diagnostico_id_foreign` FOREIGN KEY (`diagnostico_id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.dientes_diagnostico: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.doctores_externos
CREATE TABLE IF NOT EXISTS `doctores_externos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `especialidad` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctores_externos_especialidad_index` (`especialidad`),
  KEY `doctores_externos_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.doctores_externos: ~3 rows (aproximadamente)
INSERT INTO `doctores_externos` (`id`, `nombre`, `apellido`, `especialidad`, `telefono`, `email`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Dr. Especialista', 'Cirujano', 'Cirugía de terceros molares y frenillos', NULL, NULL, 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(2, 'Dr. Especialista', 'Endodoncista', 'Endodoncia', NULL, NULL, 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(3, 'Dr. Especialista', 'Implantólogo', 'Implantología y rehabilitación oral', NULL, NULL, 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17');

-- Volcando estructura para tabla alpadent.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para función alpadent.fn_calcular_edad
DELIMITER //
CREATE FUNCTION `fn_calcular_edad`(p_fecha_nacimiento DATE) RETURNS tinyint(3) unsigned
    READS SQL DATA
    DETERMINISTIC
BEGIN
    IF p_fecha_nacimiento IS NULL THEN RETURN NULL; END IF;
    IF p_fecha_nacimiento > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha no puede ser futura.';
    END IF;
    RETURN TIMESTAMPDIFF(YEAR, p_fecha_nacimiento, CURDATE());
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_cita_tiene_diagnostico
DELIMITER //
CREATE FUNCTION `fn_cita_tiene_diagnostico`(p_cita_id BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_existe TINYINT(1) DEFAULT 0;
    SELECT COUNT(*) INTO v_existe FROM diagnosticos WHERE cita_id = p_cita_id;
    RETURN IF(v_existe > 0, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_cita_tiene_pago
DELIMITER //
CREATE FUNCTION `fn_cita_tiene_pago`(p_cita_id BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_existe TINYINT(1) DEFAULT 0;
    SELECT COUNT(*) INTO v_existe FROM pagos WHERE cita_id = p_cita_id;
    RETURN IF(v_existe > 0, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_descripcion_estado_cita
DELIMITER //
CREATE FUNCTION `fn_descripcion_estado_cita`(p_e ENUM('programada','en_curso','completada','cancelada','no_asistio')) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
BEGIN
    RETURN CASE p_e
        WHEN 'programada'  THEN 'Cita programada'
        WHEN 'en_curso'    THEN 'En atención'
        WHEN 'completada'  THEN 'Atención completada'
        WHEN 'cancelada'   THEN 'Cita cancelada'
        WHEN 'no_asistio'  THEN 'Paciente no asistió'
        ELSE 'Desconocido'
    END;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_descripcion_estado_pago
DELIMITER //
CREATE FUNCTION `fn_descripcion_estado_pago`(p_e ENUM('pendiente','parcial','pagado')) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
BEGIN
    RETURN CASE p_e
        WHEN 'pendiente' THEN 'Pago pendiente'
        WHEN 'parcial'   THEN 'Pago con saldo pendiente'
        WHEN 'pagado'    THEN 'Pagado en su totalidad'
        ELSE 'Desconocido'
    END;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_dias_sin_visita
DELIMITER //
CREATE FUNCTION `fn_dias_sin_visita`(p_paciente_id BIGINT UNSIGNED) RETURNS int(11)
    READS SQL DATA
BEGIN
    DECLARE v_u DATETIME;
    SELECT MAX(fecha_hora_inicio) INTO v_u FROM citas WHERE paciente_id = p_paciente_id AND estado = 'completada';
    RETURN IF(v_u IS NULL, NULL, DATEDIFF(CURDATE(), DATE(v_u)));
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_es_menor_de_edad
DELIMITER //
CREATE FUNCTION `fn_es_menor_de_edad`(p_fecha_nacimiento DATE) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    RETURN IF(TIMESTAMPDIFF(YEAR, p_fecha_nacimiento, CURDATE()) < 18, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_es_paciente_frecuente
DELIMITER //
CREATE FUNCTION `fn_es_paciente_frecuente`(p_paciente_id BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_total INT UNSIGNED DEFAULT 0;
    SELECT COUNT(*) INTO v_total FROM citas WHERE paciente_id = p_paciente_id AND estado = 'completada';
    RETURN IF(v_total >= 3, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_formatear_numero_historia
DELIMITER //
CREATE FUNCTION `fn_formatear_numero_historia`(p_num VARCHAR(20)) RETURNS varchar(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
BEGIN
    RETURN UPPER(TRIM(p_num));
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_horario_disponible
DELIMITER //
CREATE FUNCTION `fn_horario_disponible`(p_fecha_hora_inicio DATETIME, p_fecha_hora_fin DATETIME, p_excluir_cita_id BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_conflicto INT DEFAULT 0;
    SELECT COUNT(*) INTO v_conflicto FROM citas
    WHERE estado NOT IN ('cancelada', 'no_asistio')
      AND (p_excluir_cita_id IS NULL OR id != p_excluir_cita_id)
      AND ((p_fecha_hora_inicio >= fecha_hora_inicio AND p_fecha_hora_inicio < fecha_hora_fin) OR
           (p_fecha_hora_fin > fecha_hora_inicio AND p_fecha_hora_fin <= fecha_hora_fin) OR
           (p_fecha_hora_inicio <= fecha_hora_inicio AND p_fecha_hora_fin >= fecha_hora_fin));
    RETURN IF(v_conflicto = 0, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_ingresos_anio
DELIMITER //
CREATE FUNCTION `fn_ingresos_anio`(p_anio INT) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_ing_citas DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_ing_ventas DECIMAL(10,2) DEFAULT 0.00;
    SELECT COALESCE(SUM(monto), 0.00) INTO v_ing_citas FROM abonos WHERE YEAR(fecha) = p_anio;
    SELECT COALESCE(SUM(total), 0.00) INTO v_ing_ventas FROM ventas_producto WHERE YEAR(created_at) = p_anio;
    RETURN v_ing_citas + v_ing_ventas;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_ingresos_mes
DELIMITER //
CREATE FUNCTION `fn_ingresos_mes`(p_anio INT, p_mes TINYINT) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_ing_citas DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_ing_ventas DECIMAL(10,2) DEFAULT 0.00;
    SELECT COALESCE(SUM(monto), 0.00) INTO v_ing_citas FROM abonos WHERE YEAR(fecha) = p_anio AND MONTH(fecha) = p_mes;
    SELECT COALESCE(SUM(total), 0.00) INTO v_ing_ventas FROM ventas_producto WHERE YEAR(created_at) = p_anio AND MONTH(created_at) = p_mes;
    RETURN v_ing_citas + v_ing_ventas;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_nombre_completo_paciente
DELIMITER //
CREATE FUNCTION `fn_nombre_completo_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS varchar(205) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
BEGIN
    DECLARE v_nombre VARCHAR(205);
    SELECT CONCAT(TRIM(nombre), ' ', TRIM(apellido)) INTO v_nombre FROM pacientes WHERE id = p_paciente_id;
    RETURN COALESCE(v_nombre, 'Paciente no encontrado');
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_nombre_completo_usuario
DELIMITER //
CREATE FUNCTION `fn_nombre_completo_usuario`(p_usuario_id BIGINT UNSIGNED) RETURNS varchar(205) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
BEGIN
    DECLARE v_nombre VARCHAR(205);
    SELECT CONCAT(TRIM(nombre), ' ', TRIM(apellido)) INTO v_nombre FROM usuarios WHERE id = p_usuario_id;
    RETURN COALESCE(v_nombre, 'Usuario no encontrado');
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_producto_bajo_stock
DELIMITER //
CREATE FUNCTION `fn_producto_bajo_stock`(p_producto_id BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_stock_actual INT DEFAULT 0;
    DECLARE v_stock_minimo INT DEFAULT 0;
    SELECT stock_actual, stock_minimo INTO v_stock_actual, v_stock_minimo FROM productos WHERE id = p_producto_id AND activo = 1;
    RETURN IF(v_stock_actual <= v_stock_minimo, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_proxima_cita_paciente
DELIMITER //
CREATE FUNCTION `fn_proxima_cita_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS datetime
    READS SQL DATA
BEGIN
    DECLARE v_fecha DATETIME;
    SELECT MIN(fecha_hora_inicio) INTO v_fecha FROM citas WHERE paciente_id = p_paciente_id AND estado = 'programada' AND fecha_hora_inicio > NOW();
    RETURN v_fecha;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_saldo_pendiente_paciente
DELIMITER //
CREATE FUNCTION `fn_saldo_pendiente_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_saldo DECIMAL(10,2) DEFAULT 0.00;
    SELECT COALESCE(SUM(saldo), 0.00) INTO v_saldo FROM pagos WHERE paciente_id = p_paciente_id AND estado != 'pagado';
    RETURN v_saldo;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_slots_ocupados_dia
DELIMITER //
CREATE FUNCTION `fn_slots_ocupados_dia`(p_fecha DATE) RETURNS int(10) unsigned
    READS SQL DATA
BEGIN
    DECLARE v_total INT UNSIGNED DEFAULT 0;
    SELECT COUNT(*) INTO v_total FROM citas WHERE DATE(fecha_hora_inicio) = p_fecha AND estado NOT IN ('cancelada', 'no_asistio');
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_stock_suficiente
DELIMITER //
CREATE FUNCTION `fn_stock_suficiente`(p_producto_id BIGINT UNSIGNED, p_cantidad INT) RETURNS tinyint(1)
    READS SQL DATA
BEGIN
    DECLARE v_stock INT DEFAULT 0;
    SELECT stock_actual INTO v_stock FROM productos WHERE id = p_producto_id AND activo = 1;
    RETURN IF(COALESCE(v_stock, 0) >= p_cantidad, 1, 0);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_tasa_asistencia_mes
DELIMITER //
CREATE FUNCTION `fn_tasa_asistencia_mes`(p_anio INT, p_mes TINYINT) RETURNS decimal(5,2)
    READS SQL DATA
BEGIN
    DECLARE v_prog INT DEFAULT 0;
    DECLARE v_comp INT DEFAULT 0;
    SELECT COUNT(*) INTO v_prog FROM citas WHERE YEAR(fecha_hora_inicio) = p_anio AND MONTH(fecha_hora_inicio) = p_mes AND estado != 'cancelada';
    IF v_prog = 0 THEN RETURN 0.00; END IF;
    SELECT COUNT(*) INTO v_comp FROM citas WHERE YEAR(fecha_hora_inicio) = p_anio AND MONTH(fecha_hora_inicio) = p_mes AND estado = 'completada';
    RETURN ROUND((v_comp / v_prog) * 100, 2);
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_total_citas_mes
DELIMITER //
CREATE FUNCTION `fn_total_citas_mes`(p_anio INT, p_mes TINYINT) RETURNS int(10) unsigned
    READS SQL DATA
BEGIN
    DECLARE v_total INT UNSIGNED DEFAULT 0;
    SELECT COUNT(*) INTO v_total FROM citas WHERE YEAR(fecha_hora_inicio) = p_anio AND MONTH(fecha_hora_inicio) = p_mes AND estado NOT IN ('cancelada', 'no_asistio');
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_total_citas_paciente
DELIMITER //
CREATE FUNCTION `fn_total_citas_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS int(10) unsigned
    READS SQL DATA
BEGIN
    DECLARE v_total INT UNSIGNED DEFAULT 0;
    SELECT COUNT(*) INTO v_total FROM citas WHERE paciente_id = p_paciente_id AND estado NOT IN ('cancelada', 'no_asistio');
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_total_pacientes_nuevos_mes
DELIMITER //
CREATE FUNCTION `fn_total_pacientes_nuevos_mes`(p_anio INT, p_mes TINYINT) RETURNS int(10) unsigned
    READS SQL DATA
BEGIN
    DECLARE v_total INT UNSIGNED DEFAULT 0;
    SELECT COUNT(*) INTO v_total FROM pacientes WHERE YEAR(created_at) = p_anio AND MONTH(created_at) = p_mes;
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_total_pagado_paciente
DELIMITER //
CREATE FUNCTION `fn_total_pagado_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
    SELECT COALESCE(SUM(monto_abonado), 0.00) INTO v_total FROM pagos WHERE paciente_id = p_paciente_id;
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_total_tratamientos_cita
DELIMITER //
CREATE FUNCTION `fn_total_tratamientos_cita`(p_cita_id BIGINT UNSIGNED) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
    SELECT COALESCE(SUM(precio_aplicado), 0.00) INTO v_total FROM tratamientos_realizados WHERE cita_id = p_cita_id;
    RETURN v_total;
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_tratamiento_mas_frecuente_mes
DELIMITER //
CREATE FUNCTION `fn_tratamiento_mas_frecuente_mes`(p_anio INT, p_mes TINYINT) RETURNS varchar(150) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
BEGIN
    DECLARE v_nombre VARCHAR(150) DEFAULT 'Sin datos';
    SELECT tt.nombre INTO v_nombre FROM tratamientos_realizados tr
    JOIN tipos_tratamiento tt ON tt.id = tr.tipo_tratamiento_id
    JOIN citas c ON c.id = tr.cita_id
    WHERE YEAR(c.fecha_hora_inicio) = p_anio AND MONTH(c.fecha_hora_inicio) = p_mes AND c.estado = 'completada'
    GROUP BY tt.id ORDER BY COUNT(tr.id) DESC LIMIT 1;
    RETURN COALESCE(v_nombre, 'Sin datos');
END//
DELIMITER ;

-- Volcando estructura para función alpadent.fn_ultima_cita_paciente
DELIMITER //
CREATE FUNCTION `fn_ultima_cita_paciente`(p_paciente_id BIGINT UNSIGNED) RETURNS datetime
    READS SQL DATA
BEGIN
    DECLARE v_fecha DATETIME;
    SELECT MAX(fecha_hora_inicio) INTO v_fecha FROM citas WHERE paciente_id = p_paciente_id AND estado = 'completada';
    RETURN v_fecha;
END//
DELIMITER ;

-- Volcando estructura para tabla alpadent.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.job_batches: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.migrations: ~25 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_04_30_185202_create_personal_access_tokens_table', 1),
	(5, '2026_05_01_001000_create_usuarios_table', 1),
	(6, '2026_05_01_001001_create_pacientes_table', 1),
	(7, '2026_05_01_001002_create_antecedentes_medicos_table', 1),
	(8, '2026_05_01_001003_create_doctores_externos_table', 1),
	(9, '2026_05_01_001004_create_tipos_tratamiento_table', 1),
	(10, '2026_05_01_001005_create_citas_table', 1),
	(11, '2026_05_01_001006_create_diagnosticos_table', 1),
	(12, '2026_05_01_001007_create_dientes_diagnostico_table', 1),
	(13, '2026_05_01_001008_create_tratamientos_realizados_table', 1),
	(14, '2026_05_01_001009_create_pagos_table', 1),
	(15, '2026_05_01_001010_create_abonos_table', 1),
	(16, '2026_05_01_001011_create_productos_table', 1),
	(17, '2026_05_01_001012_create_ventas_producto_table', 1),
	(18, '2026_05_01_001013_create_detalle_venta_table', 1),
	(19, '2026_05_01_001014_create_movimientos_inventario_table', 1),
	(20, '2026_05_01_001015_create_recordatorios_table', 1),
	(21, '2026_05_01_231948_add_email_to_pacientes_table', 1),
	(22, '2026_05_02_162349_add_campos_extra_to_antecedentes_medicos_table', 2),
	(23, '2026_05_02_170000_add_initial_clinical_fields_to_antecedentes_medicos_table', 3),
	(24, '2026_05_02_171500_add_created_at_to_antecedentes_medicos_table', 4),
	(25, '2026_05_02_173000_update_citas_for_single_start_time_and_treatment', 5),
	(26, '2026_05_03_000000_create_odontogramas_tables', 6),
	(27, '2026_05_03_010000_add_payment_fields_to_ventas_producto_table', 7),
	(28, '2026_05_03_011000_create_abonos_venta_producto_table', 7);

-- Volcando estructura para tabla alpadent.movimientos_inventario
CREATE TABLE IF NOT EXISTS `movimientos_inventario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_producto_id_index` (`producto_id`),
  KEY `movimientos_inventario_usuario_id_index` (`usuario_id`),
  KEY `movimientos_inventario_tipo_movimiento_index` (`tipo_movimiento`),
  KEY `movimientos_inventario_created_at_index` (`created_at`),
  CONSTRAINT `movimientos_inventario_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `movimientos_inventario_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.movimientos_inventario: ~2 rows (aproximadamente)
INSERT INTO `movimientos_inventario` (`id`, `producto_id`, `usuario_id`, `tipo_movimiento`, `cantidad`, `descripcion`, `created_at`, `updated_at`) VALUES
	(2, 15, 1, 'salida', 2, 'Venta de productos #2', '2026-05-03 03:12:35', NULL),
	(3, 4, 1, 'salida', 1, 'Venta de productos #2', '2026-05-03 03:12:35', NULL);

-- Volcando estructura para tabla alpadent.odontograma_marcas
CREATE TABLE IF NOT EXISTS `odontograma_marcas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `odontograma_id` bigint(20) unsigned NOT NULL,
  `cita_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_tratamiento_id` bigint(20) unsigned DEFAULT NULL,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `numero_diente` tinyint(3) unsigned NOT NULL,
  `denticion` varchar(20) NOT NULL,
  `superficie` varchar(20) NOT NULL DEFAULT 'general',
  `condicion` varchar(40) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `odontograma_diente_superficie_unique` (`odontograma_id`,`numero_diente`,`superficie`),
  KEY `odontograma_marcas_cita_id_foreign` (`cita_id`),
  KEY `odontograma_marcas_tipo_tratamiento_id_foreign` (`tipo_tratamiento_id`),
  KEY `odontograma_marcas_usuario_id_foreign` (`usuario_id`),
  KEY `odontograma_marcas_numero_diente_superficie_index` (`numero_diente`,`superficie`),
  KEY `odontograma_marcas_condicion_index` (`condicion`),
  CONSTRAINT `odontograma_marcas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `odontograma_marcas_odontograma_id_foreign` FOREIGN KEY (`odontograma_id`) REFERENCES `odontogramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `odontograma_marcas_tipo_tratamiento_id_foreign` FOREIGN KEY (`tipo_tratamiento_id`) REFERENCES `tipos_tratamiento` (`id`) ON DELETE SET NULL,
  CONSTRAINT `odontograma_marcas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.odontograma_marcas: ~4 rows (aproximadamente)
INSERT INTO `odontograma_marcas` (`id`, `odontograma_id`, `cita_id`, `tipo_tratamiento_id`, `usuario_id`, `numero_diente`, `denticion`, `superficie`, `condicion`, `color`, `observacion`, `created_at`, `updated_at`) VALUES
	(2, 2, 6, 3, 1, 14, 'permanente', 'vestibular', 'cariado', '#ef4444', NULL, '2026-05-03 02:16:45', '2026-05-03 02:16:45'),
	(3, 2, 6, 5, 1, 14, 'permanente', 'oclusal', 'cariado', '#ef4444', NULL, '2026-05-03 02:17:17', '2026-05-03 02:17:17'),
	(4, 2, NULL, NULL, 1, 13, 'permanente', 'distal', 'tratamiento_indicado', '#facc15', NULL, '2026-05-03 02:18:53', '2026-05-03 02:18:53'),
	(5, 3, NULL, NULL, 1, 14, 'permanente', 'distal', 'cariado', '#ef4444', NULL, '2026-05-03 02:19:29', '2026-05-03 02:19:29');

-- Volcando estructura para tabla alpadent.odontogramas
CREATE TABLE IF NOT EXISTS `odontogramas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `indice_cpo_cariados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_cpo_perdidos` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_cpo_obturados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_ceo_cariados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_ceo_extraidos` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `indice_ceo_obturados` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `higiene_placa` tinyint(3) unsigned DEFAULT NULL,
  `higiene_calculo` tinyint(3) unsigned DEFAULT NULL,
  `higiene_gingivitis` tinyint(3) unsigned DEFAULT NULL,
  `enfermedad_periodontal` varchar(20) NOT NULL DEFAULT 'ninguna',
  `maloclusion` varchar(20) NOT NULL DEFAULT 'ninguna',
  `fluorosis` varchar(20) NOT NULL DEFAULT 'ninguna',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `odontogramas_paciente_id_unique` (`paciente_id`),
  KEY `odontogramas_usuario_id_index` (`usuario_id`),
  CONSTRAINT `odontogramas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `odontogramas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.odontogramas: ~4 rows (aproximadamente)
INSERT INTO `odontogramas` (`id`, `paciente_id`, `usuario_id`, `indice_cpo_cariados`, `indice_cpo_perdidos`, `indice_cpo_obturados`, `indice_ceo_cariados`, `indice_ceo_extraidos`, `indice_ceo_obturados`, `higiene_placa`, `higiene_calculo`, `higiene_gingivitis`, `enfermedad_periodontal`, `maloclusion`, `fluorosis`, `observaciones`, `created_at`, `updated_at`) VALUES
	(2, 1, 1, 1, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'ninguna', 'ninguna', 'ninguna', NULL, '2026-05-03 02:14:41', '2026-05-03 02:16:45'),
	(3, 8, 1, 1, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'ninguna', 'ninguna', 'ninguna', NULL, '2026-05-03 02:18:39', '2026-05-03 02:19:29'),
	(4, 3, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'ninguna', 'ninguna', 'ninguna', NULL, '2026-05-03 02:18:42', '2026-05-03 02:18:42'),
	(5, 7, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'ninguna', 'ninguna', 'ninguna', NULL, '2026-05-03 02:33:57', '2026-05-03 02:33:57'),
	(6, 2, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, 'ninguna', 'ninguna', 'ninguna', NULL, '2026-05-03 02:33:59', '2026-05-03 02:33:59');

-- Volcando estructura para tabla alpadent.pacientes
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_historia` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` enum('M','F') NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `es_menor` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pacientes_numero_historia_unique` (`numero_historia`),
  KEY `pacientes_numero_historia_index` (`numero_historia`),
  KEY `pacientes_nombre_index` (`nombre`),
  KEY `pacientes_apellido_index` (`apellido`),
  KEY `pacientes_activo_index` (`activo`),
  KEY `pacientes_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.pacientes: ~5 rows (aproximadamente)
INSERT INTO `pacientes` (`id`, `numero_historia`, `nombre`, `apellido`, `fecha_nacimiento`, `sexo`, `telefono`, `email`, `direccion`, `es_menor`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'HC-000001', 'Angel Josue', 'Minuche Pacheco', '2005-10-27', 'M', '0991640687', 'angelitoangel097@gmail.com', 'EL GUABO', 0, 0, '2026-05-02 22:11:18', '2026-05-02 22:19:41'),
	(2, 'HC-000002', 'Maribel Jessenia', 'Pacheco Carrillo', '1986-10-10', 'F', '00000000', 'maribel@gmail.com', 'EL GUABO', 0, 1, '2026-05-02 18:47:07', '2026-05-02 18:47:07'),
	(3, 'HC-000003', 'Carlos Milton', 'Castro Pogo', '1780-10-15', 'M', '0991640687', 'carlos@gmail.com', 'EL GUABO', 0, 1, '2026-05-02 19:02:26', '2026-05-02 19:02:26'),
	(7, 'HC-000004', 'Lorena Elizabeth', 'Sumba Torres', '2000-10-02', 'F', '0991640687', 'lorena@gmail.com', 'EL GUABO', 0, 1, '2026-05-02 22:18:20', '2026-05-02 22:18:20'),
	(8, 'HC-000008', 'Carlitos Juanito', 'Penecito Culito', '2016-10-10', 'M', '0991640687', 'penecin@gmail.com', 'PIÑAS', 1, 1, '2026-05-02 22:20:40', '2026-05-02 22:20:40');

-- Volcando estructura para tabla alpadent.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint(20) unsigned NOT NULL,
  `cita_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_pendiente` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','parcial','pagado') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` enum('efectivo','transferencia','tarjeta') NOT NULL DEFAULT 'efectivo',
  `referencia_transferencia` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_paciente_id_index` (`paciente_id`),
  KEY `pagos_cita_id_index` (`cita_id`),
  KEY `pagos_usuario_id_index` (`usuario_id`),
  KEY `pagos_estado_index` (`estado`),
  KEY `pagos_created_at_index` (`created_at`),
  CONSTRAINT `pagos_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  CONSTRAINT `pagos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `pagos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.pagos: ~1 rows (aproximadamente)
INSERT INTO `pagos` (`id`, `paciente_id`, `cita_id`, `usuario_id`, `monto_total`, `monto_pagado`, `saldo_pendiente`, `estado`, `metodo_pago`, `referencia_transferencia`, `created_at`, `updated_at`) VALUES
	(1, 3, 11, 1, 30.00, 30.00, 0.00, 'pagado', 'efectivo', NULL, '2026-05-03 01:33:34', '2026-05-03 01:34:14');

-- Volcando estructura para tabla alpadent.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.personal_access_tokens: ~5 rows (aproximadamente)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 1, 'auth_token', 'b1b012a3f032147863b74d431976070aa3e6269bdfb3abec816d4b9eed20827c', '["*"]', '2026-05-02 23:57:25', NULL, '2026-05-02 22:10:46', '2026-05-02 23:57:25'),
	(2, 'App\\Models\\User', 1, 'auth_token', '40de9dbf186b82d7da73fd213a89415f8d70b0b34c5e1f9bc86919e0b3234866', '["*"]', '2026-05-03 00:21:06', NULL, '2026-05-03 00:15:06', '2026-05-03 00:21:06'),
	(3, 'App\\Models\\User', 1, 'auth_token', 'ae4d76e22f59f4bf8e5e092a9cf91c064d6d692f2efbd48db59db7d6e6f8b252', '["*"]', '2026-05-03 01:34:18', NULL, '2026-05-03 01:30:22', '2026-05-03 01:34:18'),
	(4, 'App\\Models\\User', 1, 'auth_token', '77cfd70084a1919ed0c3418da1941c970146ae8f206fa5e8959462c6815ef3d6', '["*"]', '2026-05-03 02:34:30', NULL, '2026-05-03 02:14:20', '2026-05-03 02:34:30'),
	(5, 'App\\Models\\User', 1, 'auth_token', '4fb23658c77ba5e57095867bc0a792222b47af2b7c5f1da08696b67f917ae1bd', '["*"]', '2026-05-03 03:13:20', NULL, '2026-05-03 03:10:46', '2026-05-03 03:13:20');

-- Volcando estructura para tabla alpadent.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_nombre_unique` (`nombre`),
  KEY `productos_nombre_index` (`nombre`),
  KEY `productos_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.productos: ~19 rows (aproximadamente)
INSERT INTO `productos` (`id`, `nombre`, `marca`, `descripcion`, `precio_venta`, `stock_actual`, `stock_minimo`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Cepillo dental adulto suave', 'Oral-B', 'Cepillo manual cerdas suaves para adulto', 3.50, 20, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(2, 'Cepillo dental adulto medio', 'Oral-B', 'Cepillo manual cerdas medias para adulto', 4.50, 30, 5, 1, '2026-05-02 22:09:18', '2026-05-03 03:11:40'),
	(3, 'Cepillo dental infantil', 'Colgate', 'Cepillo manual para niños con mango antideslizante', 3.00, 15, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(4, 'Cepillo dental ortodoncia', 'GUM', 'Cepillo especial para brackets de ortodoncia', 5.00, 9, 3, 1, '2026-05-02 22:09:18', '2026-05-03 03:12:35'),
	(5, 'Cepillo interproximal', 'GUM', 'Cepillo para limpiar entre dientes y brackets', 4.50, 10, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(6, 'Pasta dental blanqueadora', 'Colgate', 'Crema dental para blanqueamiento diario 150ml', 4.00, 15, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(7, 'Pasta dental flúor total', 'Oral-B', 'Crema dental protección completa 150ml', 3.80, 15, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(8, 'Pasta dental infantil', 'Colgate', 'Crema dental para niños sin flúor 75ml', 3.50, 10, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(9, 'Pasta dental sensibilidad', 'Sensodyne', 'Crema dental para dientes sensibles 100ml', 5.50, 10, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(10, 'Pasta dental post-ortodoncia', 'GUM', 'Crema dental especial post tratamiento 75ml', 6.00, 8, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(11, 'Hilo dental clásico', 'Oral-B', 'Hilo dental encerado sabor menta 50m', 2.50, 20, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(12, 'Hilo dental superfloss', 'Oral-B', 'Hilo dental para puentes e implantes', 5.00, 10, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(13, 'Palillos con hilo dental', 'GUM', 'Palillos con hilo incorporado x30', 3.00, 15, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(14, 'Enjuague bucal antiséptico', 'Listerine', 'Enjuague antibacterial 500ml', 6.50, 12, 4, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(15, 'Enjuague bucal flúor', 'Colgate', 'Enjuague con flúor anticaries 500ml', 5.50, 10, 4, 1, '2026-05-02 22:09:18', '2026-05-03 03:12:35'),
	(16, 'Enjuague bucal sin alcohol', 'Listerine', 'Enjuague suave sin alcohol 500ml', 6.50, 8, 3, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(17, 'Irrigador oral portátil', 'Waterpik', 'Irrigador de agua para higiene dental', 45.00, 5, 2, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(18, 'Limpiador lingual', 'Genérico', 'Raspador de lengua de acero inoxidable', 2.00, 15, 5, 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(19, 'Protector bucal deportivo', 'Genérico', 'Protector bucal termoformable para deporte', 8.00, 8, 3, 0, '2026-05-02 22:09:18', '2026-05-03 03:11:50');

-- Volcando estructura para tabla alpadent.recordatorios
CREATE TABLE IF NOT EXISTS `recordatorios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cita_id` bigint(20) unsigned NOT NULL,
  `canal` enum('email','sms','whatsapp') NOT NULL DEFAULT 'email',
  `estado` enum('pendiente','enviado','fallido') NOT NULL DEFAULT 'pendiente',
  `fecha_envio` timestamp NULL DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `recordatorios_cita_id_index` (`cita_id`),
  KEY `recordatorios_estado_index` (`estado`),
  KEY `recordatorios_fecha_envio_index` (`fecha_envio`),
  CONSTRAINT `recordatorios_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.recordatorios: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.sessions: ~1 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('gRX9GhsQJFsoBplCeOtiMhGJqNvfnZYwpeELu3rL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWt1MTdzMTkxVTRKR0xvSk9FbEp2cFdSdGI4Tm9GYlNnbHZONW95QyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777778015);

-- Volcando estructura para procedimiento alpadent.sp_actualizar_antecedentes
DELIMITER //
CREATE PROCEDURE `sp_actualizar_antecedentes`(
    IN p_paciente_id            BIGINT UNSIGNED,
    IN p_diabetes                TINYINT(1),
    IN p_alergias_medicamentos  TINYINT(1),
    IN p_detalle_alergias        TEXT,
    IN p_problemas_hemorragicos TINYINT(1),
    IN p_problemas_cardiacos    TINYINT(1),
    IN p_problemas_renales      TINYINT(1),
    IN p_embarazo               TINYINT(1),
    IN p_presion_arterial        VARCHAR(20),
    IN p_otros                  TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM pacientes WHERE id = p_paciente_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paciente no encontrado o inactivo.';
    END IF;

    START TRANSACTION;

        UPDATE antecedentes_medicos
        SET diabetes               = p_diabetes,
            alergias_medicamentos  = p_alergias_medicamentos,
            detalle_alergias       = p_detalle_alergias,
            problemas_hemorragicos = p_problemas_hemorragicos,
            problemas_cardiacos    = p_problemas_cardiacos,
            problemas_renales      = p_problemas_renales,
            embarazo               = p_embarazo,
            presion_arterial       = p_presion_arterial,
            otros                  = p_otros
        WHERE paciente_id = p_paciente_id;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_agendar_cita
DELIMITER //
CREATE PROCEDURE `sp_agendar_cita`(
    IN  p_paciente_id       BIGINT UNSIGNED,
    IN  p_usuario_id        BIGINT UNSIGNED,
    IN  p_doctor_externo_id BIGINT UNSIGNED,
    IN  p_fecha_hora_inicio DATETIME,
    IN  p_fecha_hora_fin    DATETIME,
    IN  p_motivo_consulta   VARCHAR(255),
    IN  p_observaciones     TEXT,
    OUT p_cita_id           BIGINT UNSIGNED
)
BEGIN
    DECLARE v_es_primera_vez TINYINT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM pacientes WHERE id = p_paciente_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paciente no encontrado o inactivo.';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_usuario_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Usuario no encontrado o inactivo.';
    END IF;

    IF p_doctor_externo_id IS NOT NULL AND
       NOT EXISTS (SELECT 1 FROM doctores_externos WHERE id = p_doctor_externo_id AND activo = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Doctor externo no encontrado o inactivo.';
    END IF;

    SELECT IF(COUNT(*) = 0, 1, 0)
    INTO v_es_primera_vez
    FROM citas
    WHERE paciente_id = p_paciente_id
      AND estado NOT IN ('cancelada', 'no_asistio');

    START TRANSACTION;

        INSERT INTO citas (
            paciente_id, usuario_id, doctor_externo_id,
            fecha_hora_inicio, fecha_hora_fin,
            motivo_consulta, observaciones, es_primera_vez
        )
        VALUES (
            p_paciente_id, p_usuario_id, p_doctor_externo_id,
            p_fecha_hora_inicio, p_fecha_hora_fin,
            TRIM(p_motivo_consulta), p_observaciones, v_es_primera_vez
        );

        SET p_cita_id = LAST_INSERT_ID();

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_agregar_diente_diagnostico
DELIMITER //
CREATE PROCEDURE `sp_agregar_diente_diagnostico`(
    IN p_diagnostico_id BIGINT UNSIGNED,
    IN p_numero_diente  TINYINT UNSIGNED,
    IN p_condicion      ENUM('sano','cariado','obturado','perdido','fractura','corona','implante','extraccion_indicada','otro'),
    IN p_superficie     VARCHAR(100),
    IN p_observacion    TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM diagnosticos WHERE id = p_diagnostico_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Diagnóstico no encontrado.';
    END IF;

    START TRANSACTION;

        IF EXISTS (
            SELECT 1 FROM dientes_diagnostico
            WHERE diagnostico_id = p_diagnostico_id AND numero_diente = p_numero_diente
        ) THEN
            UPDATE dientes_diagnostico
            SET condicion    = p_condicion,
                superficie   = p_superficie,
                observacion  = p_observacion
            WHERE diagnostico_id = p_diagnostico_id
              AND numero_diente  = p_numero_diente;
        ELSE
            INSERT INTO dientes_diagnostico (diagnostico_id, numero_diente, condicion, superficie, observacion)
            VALUES (p_diagnostico_id, p_numero_diente, p_condicion, p_superficie, p_observacion);
        END IF;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_cancelar_cita
DELIMITER //
CREATE PROCEDURE `sp_cancelar_cita`(
    IN p_cita_id      BIGINT UNSIGNED,
    IN p_estado       ENUM('cancelada','no_asistio'),
    IN p_observaciones TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM citas WHERE id = p_cita_id AND estado = 'programada') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cita no existe o no se puede cancelar en su estado actual.';
    END IF;

    START TRANSACTION;

        UPDATE citas
        SET estado        = p_estado,
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | ', COALESCE(p_observaciones, ''))
        WHERE id = p_cita_id;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_completar_cita
DELIMITER //
CREATE PROCEDURE `sp_completar_cita`(
    IN p_cita_id BIGINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (
        SELECT 1 FROM citas WHERE id = p_cita_id AND estado IN ('programada','en_curso')
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cita no existe o no se puede completar en su estado actual.';
    END IF;

    START TRANSACTION;

        UPDATE citas SET estado = 'completada' WHERE id = p_cita_id;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_entrada_inventario
DELIMITER //
CREATE PROCEDURE `sp_entrada_inventario`(
    IN p_producto_id BIGINT UNSIGNED,
    IN p_usuario_id  BIGINT UNSIGNED,
    IN p_cantidad    INT,
    IN p_motivo      VARCHAR(255)
)
BEGIN
    DECLARE v_stock_antes   INT;
    DECLARE v_stock_despues INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad de entrada debe ser mayor a cero.';
    END IF;

    SELECT stock_actual INTO v_stock_antes
    FROM productos WHERE id = p_producto_id AND activo = 1;

    SET v_stock_despues = v_stock_antes + p_cantidad;

    START TRANSACTION;

        UPDATE productos SET stock_actual = v_stock_despues WHERE id = p_producto_id;

        INSERT INTO movimientos_inventario
            (producto_id, usuario_id, tipo, cantidad, stock_antes, stock_despues, motivo)
        VALUES
            (p_producto_id, p_usuario_id, 'entrada', p_cantidad,
             v_stock_antes, v_stock_despues, TRIM(p_motivo));

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_obtener_historial_paciente
DELIMITER //
CREATE PROCEDURE `sp_obtener_historial_paciente`(
    IN p_paciente_id BIGINT UNSIGNED
)
BEGIN
    SELECT
        p.id, p.numero_historia, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
        p.fecha_nacimiento, TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
        p.sexo, p.telefono, p.direccion, p.es_menor,
        am.diabetes, am.alergias_medicamentos, am.detalle_alergias, am.problemas_hemorragicos,
        am.problemas_cardiacos, am.problemas_renales, am.embarazo, am.presion_arterial, am.otros AS otros_antecedentes
    FROM pacientes p
    LEFT JOIN antecedentes_medicos am ON am.paciente_id = p.id
    WHERE p.id = p_paciente_id;

    SELECT
        c.id AS cita_id, c.fecha_hora_inicio, c.fecha_hora_fin, c.motivo_consulta, c.estado AS estado_cita,
        c.es_primera_vez, CONCAT(u.nombre, ' ', u.apellido) AS registrado_por,
        CONCAT(de.nombre, ' ', de.apellido) AS doctor_externo, de.especialidad,
        d.descripcion AS diagnostico, d.gingivitis, d.enfermedad_periodontal,
        d.indice_cpo_cariados, d.indice_cpo_perdidos, d.indice_cpo_obturados,
        tt.nombre AS tratamiento, tt.categoria, tr.numero_diente, tr.precio_aplicado,
        tr.notas AS notas_tratamiento, pg.monto_total, pg.monto_abonado, pg.saldo, pg.estado AS estado_pago
    FROM citas c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id
    LEFT JOIN diagnosticos d ON d.cita_id = c.id
    LEFT JOIN tratamientos_realizados tr ON tr.cita_id = c.id
    LEFT JOIN tipos_tratamiento tt ON tt.id = tr.tipo_tratamiento_id
    LEFT JOIN pagos pg ON pg.cita_id = c.id
    WHERE c.paciente_id = p_paciente_id
    ORDER BY c.fecha_hora_inicio DESC;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_productos_stock_bajo
DELIMITER //
CREATE PROCEDURE `sp_productos_stock_bajo`()
BEGIN
    SELECT id, nombre, marca, stock_actual, stock_minimo, (stock_minimo - stock_actual) AS unidades_faltantes, precio_venta
    FROM productos
    WHERE stock_actual <= stock_minimo AND activo = 1
    ORDER BY unidades_faltantes DESC;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_reagendar_cita
DELIMITER //
CREATE PROCEDURE `sp_reagendar_cita`(
    IN p_cita_id           BIGINT UNSIGNED,
    IN p_fecha_hora_inicio DATETIME,
    IN p_fecha_hora_fin    DATETIME,
    IN p_observaciones     TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM citas WHERE id = p_cita_id AND estado = 'programada') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cita no existe o no está en estado programada.';
    END IF;

    START TRANSACTION;

        UPDATE citas
        SET fecha_hora_inicio = p_fecha_hora_inicio,
            fecha_hora_fin    = p_fecha_hora_fin,
            observaciones     = p_observaciones
        WHERE id = p_cita_id;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_abono
DELIMITER //
CREATE PROCEDURE `sp_registrar_abono`(
    IN p_pago_id    BIGINT UNSIGNED,
    IN p_usuario_id BIGINT UNSIGNED,
    IN p_monto      DECIMAL(10,2),
    IN p_metodo_pago ENUM('efectivo','transferencia','tarjeta'),
    IN p_referencia VARCHAR(100),
    IN p_notas      TEXT
)
BEGIN
    DECLARE v_saldo_actual DECIMAL(10,2);
    DECLARE v_estado       VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT saldo, estado INTO v_saldo_actual, v_estado
    FROM pagos WHERE id = p_pago_id;

    IF v_saldo_actual IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Pago no encontrado.';
    END IF;

    IF v_estado = 'pagado' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Este pago ya fue cancelado en su totalidad.';
    END IF;

    START TRANSACTION;
        INSERT INTO abonos (pago_id, usuario_id, monto, metodo_pago, referencia, notas)
        VALUES (p_pago_id, p_usuario_id, p_monto, p_metodo_pago, p_referencia, p_notas);
    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_diagnostico
DELIMITER //
CREATE PROCEDURE `sp_registrar_diagnostico`(
    IN  p_cita_id               BIGINT UNSIGNED,
    IN  p_usuario_id            BIGINT UNSIGNED,
    IN  p_descripcion           TEXT,
    IN  p_indice_cpo_cariados   TINYINT UNSIGNED,
    IN  p_indice_cpo_perdidos   TINYINT UNSIGNED,
    IN  p_indice_cpo_obturados  TINYINT UNSIGNED,
    IN  p_gingivitis            TINYINT(1),
    IN  p_enfermedad_periodontal TINYINT(1),
    OUT p_diagnostico_id        BIGINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (
        SELECT 1 FROM citas
        WHERE id = p_cita_id AND estado IN ('programada','en_curso')
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cita no existe o no permite registrar diagnóstico en su estado actual.';
    END IF;

    IF EXISTS (SELECT 1 FROM diagnosticos WHERE cita_id = p_cita_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Esta cita ya tiene un diagnóstico registrado.';
    END IF;

    START TRANSACTION;

        UPDATE citas SET estado = 'en_curso' WHERE id = p_cita_id AND estado = 'programada';

        INSERT INTO diagnosticos (
            cita_id, usuario_id, descripcion,
            indice_cpo_cariados, indice_cpo_perdidos, indice_cpo_obturados,
            gingivitis, enfermedad_periodontal
        )
        VALUES (
            p_cita_id, p_usuario_id, TRIM(p_descripcion),
            p_indice_cpo_cariados, p_indice_cpo_perdidos, p_indice_cpo_obturados,
            p_gingivitis, p_enfermedad_periodontal
        );

        SET p_diagnostico_id = LAST_INSERT_ID();

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_paciente
DELIMITER //
CREATE PROCEDURE `sp_registrar_paciente`(
    IN  p_nombre            VARCHAR(100),
    IN  p_apellido          VARCHAR(100),
    IN  p_fecha_nacimiento DATE,
    IN  p_sexo              ENUM('M','F'),
    IN  p_telefono          VARCHAR(20),
    IN  p_email             VARCHAR(150),
    IN  p_direccion         VARCHAR(255),
    OUT p_paciente_id       BIGINT UNSIGNED,
    OUT p_numero_historia  VARCHAR(20)
)
BEGIN
    -- Declaramos el manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

        -- 1. Insertamos al paciente
        -- OJO: numero_historia no puede ser '' por el CHECK que pusiste antes.
        -- Como ejemplo, generamos uno temporal basado en el tiempo o UUID, 
        -- o lo dejamos para actualizarlo abajo.
        
        INSERT INTO pacientes (
            nombre, apellido, fecha_nacimiento, sexo,
            telefono, email, direccion, numero_historia
        )
        VALUES (
            TRIM(p_nombre), TRIM(p_apellido), p_fecha_nacimiento, p_sexo,
            p_telefono, p_email, TRIM(p_direccion), 
            CONCAT('TMP-', REPLACE(NOW(), ' ', '')) -- Valor temporal para evitar error de CHECK
        );

        SET p_paciente_id := LAST_INSERT_ID();

        -- 2. Generamos el número de historia real (ejemplo: HC-ID)
        SET p_numero_historia := CONCAT('HC-', p_paciente_id);

        -- 3. Actualizamos el registro con el número real
        UPDATE pacientes 
        SET numero_historia = p_numero_historia 
        WHERE id = p_paciente_id;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_pago
DELIMITER //
CREATE PROCEDURE `sp_registrar_pago`(
    IN  p_paciente_id              BIGINT UNSIGNED,
    IN  p_cita_id                  BIGINT UNSIGNED,
    IN  p_usuario_id               BIGINT UNSIGNED,
    IN  p_monto_total              DECIMAL(10,2),
    IN  p_monto_abonado            DECIMAL(10,2),
    IN  p_metodo_pago_inicial      ENUM('efectivo','transferencia','tarjeta'),
    IN  p_referencia_transferencia VARCHAR(100),
    OUT p_pago_id                  BIGINT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF EXISTS (SELECT 1 FROM pagos WHERE cita_id = p_cita_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Esta cita ya tiene un pago registrado.';
    END IF;

    IF p_monto_total <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto total debe ser mayor a cero.';
    END IF;

    START TRANSACTION;

        INSERT INTO pagos (
            paciente_id, cita_id, usuario_id,
            monto_total, monto_abonado,
            metodo_pago_inicial, referencia_transferencia
        )
        VALUES (
            p_paciente_id, p_cita_id, p_usuario_id,
            p_monto_total, p_monto_abonado,
            p_metodo_pago_inicial, p_referencia_transferencia
        );

        SET p_pago_id = LAST_INSERT_ID();

        IF p_monto_abonado > 0 THEN
            INSERT INTO abonos (pago_id, usuario_id, monto, metodo_pago, referencia)
            VALUES (p_pago_id, p_usuario_id, p_monto_abonado,
                    p_metodo_pago_inicial, p_referencia_transferencia);
        END IF;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_tratamiento
DELIMITER //
CREATE PROCEDURE `sp_registrar_tratamiento`(
    IN p_cita_id             BIGINT UNSIGNED,
    IN p_tipo_tratamiento_id BIGINT UNSIGNED,
    IN p_diagnostico_id      BIGINT UNSIGNED,
    IN p_numero_diente       TINYINT UNSIGNED,
    IN p_precio_aplicado     DECIMAL(10,2),
    IN p_notas               TEXT
)
BEGIN
    DECLARE v_precio_catalogo DECIMAL(10,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (
        SELECT 1 FROM citas WHERE id = p_cita_id AND estado IN ('programada','en_curso')
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cita no existe o no permite agregar tratamientos en su estado actual.';
    END IF;

    SELECT precio INTO v_precio_catalogo
    FROM tipos_tratamiento WHERE id = p_tipo_tratamiento_id AND activo = 1;

    IF v_precio_catalogo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de tratamiento no encontrado o inactivo.';
    END IF;

    IF p_precio_aplicado IS NULL OR p_precio_aplicado < 0 THEN
        SET p_precio_aplicado = v_precio_catalogo;
    END IF;

    START TRANSACTION;

        INSERT INTO tratamientos_realizados (
            cita_id, tipo_tratamiento_id, diagnostico_id,
            numero_diente, precio_aplicado, notas
        )
        VALUES (
            p_cita_id, p_tipo_tratamiento_id, p_diagnostico_id,
            p_numero_diente, p_precio_aplicado, p_notas
        );

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_registrar_venta_producto
DELIMITER //
CREATE PROCEDURE `sp_registrar_venta_producto`(
    IN  p_paciente_id  BIGINT UNSIGNED,
    IN  p_usuario_id   BIGINT UNSIGNED,
    IN  p_metodo_pago  ENUM('efectivo','transferencia','tarjeta'),
    IN  p_referencia   VARCHAR(100),
    IN  p_productos    JSON,
    OUT p_venta_id     BIGINT UNSIGNED
)
BEGIN
    DECLARE v_i         INT DEFAULT 0;
    DECLARE v_n         INT;
    DECLARE v_prod_id   BIGINT UNSIGNED;
    DECLARE v_cantidad  INT;
    DECLARE v_precio    DECIMAL(10,2);
    DECLARE v_stock     INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SET v_n = JSON_LENGTH(p_productos);

    IF v_n = 0 OR v_n IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe incluir al menos un producto en la venta.';
    END IF;

    START TRANSACTION;

        INSERT INTO ventas_producto (paciente_id, usuario_id, total, metodo_pago, referencia)
        VALUES (p_paciente_id, p_usuario_id, 0, p_metodo_pago, p_referencia);

        SET p_venta_id = LAST_INSERT_ID();

        WHILE v_i < v_n DO
            SET v_prod_id  = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_i, '].producto_id')));
            SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_i, '].cantidad')));

            SELECT precio_venta, stock_actual
            INTO v_precio, v_stock
            FROM productos
            WHERE id = v_prod_id AND activo = 1;

            IF v_precio IS NULL THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Producto no encontrado o inactivo.';
            END IF;

            INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal)
            VALUES (p_venta_id, v_prod_id, v_cantidad, v_precio, 0);

            SET v_i = v_i + 1;
        END WHILE;

    COMMIT;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_reporte_citas_por_mes
DELIMITER //
CREATE PROCEDURE `sp_reporte_citas_por_mes`(
    IN p_anio INT
)
BEGIN
    SELECT MONTH(fecha_hora_inicio) AS mes, MONTHNAME(fecha_hora_inicio) AS nombre_mes, estado, COUNT(*) AS total_citas
    FROM citas
    WHERE YEAR(fecha_hora_inicio) = p_anio
    GROUP BY MONTH(fecha_hora_inicio), estado
    ORDER BY mes, estado;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_reporte_ingresos
DELIMITER //
CREATE PROCEDURE `sp_reporte_ingresos`(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin    DATE
)
BEGIN
    SELECT DATE(a.fecha) AS fecha, pg.metodo_pago_inicial AS metodo_pago, COUNT(DISTINCT pg.id) AS cantidad_pagos, SUM(a.monto) AS total_cobrado
    FROM abonos a
    JOIN pagos pg ON pg.id = a.pago_id
    WHERE DATE(a.fecha) BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY DATE(a.fecha), pg.metodo_pago_inicial;

    SELECT DATE(vp.created_at) AS fecha, vp.metodo_pago, COUNT(DISTINCT vp.id) AS cantidad_ventas, SUM(vp.total) AS total_ventas
    FROM ventas_producto vp
    WHERE DATE(vp.created_at) BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY DATE(vp.created_at), vp.metodo_pago;
END//
DELIMITER ;

-- Volcando estructura para procedimiento alpadent.sp_reporte_tratamientos_frecuentes
DELIMITER //
CREATE PROCEDURE `sp_reporte_tratamientos_frecuentes`(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin    DATE
)
BEGIN
    SELECT tt.nombre AS tratamiento, tt.categoria, COUNT(tr.id) AS veces_realizado, SUM(tr.precio_aplicado) AS ingresos_generados
    FROM tratamientos_realizados tr
    JOIN tipos_tratamiento tt ON tt.id = tr.tipo_tratamiento_id
    JOIN citas c ON c.id = tr.cita_id
    WHERE DATE(c.fecha_hora_inicio) BETWEEN p_fecha_inicio AND p_fecha_fin AND c.estado = 'completada'
    GROUP BY tt.id
    ORDER BY veces_realizado DESC;
END//
DELIMITER ;

-- Volcando estructura para tabla alpadent.tipos_tratamiento
CREATE TABLE IF NOT EXISTS `tipos_tratamiento` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `categoria` enum('operatoria','limpieza','periodoncia','endodoncia','exodoncia','cirugia','protesis_removible','protesis_fija','ortodoncia','implantologia','rayos_x','otros') NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_tratamiento_nombre_unique` (`nombre`),
  KEY `tipos_tratamiento_categoria_index` (`categoria`),
  KEY `tipos_tratamiento_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.tipos_tratamiento: ~44 rows (aproximadamente)
INSERT INTO `tipos_tratamiento` (`id`, `nombre`, `categoria`, `precio`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Restauración resina 1 superficie', 'operatoria', 35.00, 'Curación de resina compuesta en una superficie', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(2, 'Restauración resina 2 superficies', 'operatoria', 45.00, 'Curación de resina compuesta en dos superficies', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(3, 'Restauración resina 3 superficies', 'operatoria', 55.00, 'Curación de resina compuesta en tres superficies', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(4, 'Restauración amalgama', 'operatoria', 30.00, 'Curación de amalgama dental', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(5, 'Reconstrucción dental', 'operatoria', 65.00, 'Reconstrucción total de la corona dental', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(6, 'Profilaxis dental (limpieza)', 'limpieza', 40.00, 'Limpieza dental completa con ultrasonido y pulido', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(7, 'Detartraje supragingival', 'limpieza', 45.00, 'Eliminación de sarro por encima de la encía', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(8, 'Detartraje subgingival', 'limpieza', 60.00, 'Eliminación de sarro por debajo de la encía', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(9, 'Aplicación de flúor', 'limpieza', 15.00, 'Aplicación tópica de flúor', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(10, 'Raspado y alisado radicular por cuadrante', 'periodoncia', 70.00, 'Tratamiento periodontal por cuadrante', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(11, 'Cirugía periodontal', 'periodoncia', 150.00, 'Intervención quirúrgica de encías', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(12, 'Endodoncia diente anterior (1 conducto)', 'endodoncia', 120.00, 'Tratamiento de conducto diente anterior', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(13, 'Endodoncia diente premolar (2 conductos)', 'endodoncia', 150.00, 'Tratamiento de conducto premolar', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(14, 'Endodoncia diente molar (3 conductos)', 'endodoncia', 200.00, 'Tratamiento de conducto molar', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(15, 'Endodoncia diente molar (4 conductos)', 'endodoncia', 230.00, 'Tratamiento de conducto molar complejo', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(16, 'Exodoncia simple', 'exodoncia', 35.00, 'Extracción dental simple', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(17, 'Exodoncia compleja', 'exodoncia', 60.00, 'Extracción dental con complicaciones', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(18, 'Exodoncia de terceros molares', 'cirugia', 150.00, 'Extracción de muela del juicio', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(19, 'Frenectomía labial', 'cirugia', 120.00, 'Cirugía de frenillo labial', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(20, 'Implante dental (colocación)', 'cirugia', 800.00, 'Colocación quirúrgica del implante', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(21, 'Prótesis total superior', 'protesis_removible', 350.00, 'Dentadura completa superior removible', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(22, 'Prótesis total inferior', 'protesis_removible', 350.00, 'Dentadura completa inferior removible', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(23, 'Prótesis parcial removible acrílica', 'protesis_removible', 200.00, 'Puente removible de acrílico', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(24, 'Prótesis parcial removible metálica', 'protesis_removible', 280.00, 'Prótesis parcial con estructura metálica', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(25, 'Corona de porcelana', 'protesis_fija', 280.00, 'Corona dental de porcelana', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(26, 'Corona metal-porcelana', 'protesis_fija', 230.00, 'Corona de metal con recubrimiento de porcelana', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(27, 'Corona de zirconia', 'protesis_fija', 350.00, 'Corona de alta estética en zirconia', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(28, 'Puente de 3 piezas porcelana', 'protesis_fija', 750.00, 'Puente fijo de tres unidades en porcelana', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(29, 'Carilla de porcelana', 'protesis_fija', 300.00, 'Carilla estética de porcelana', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(30, 'Carilla de resina', 'protesis_fija', 120.00, 'Carilla estética de resina compuesta', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(31, 'Corona sobre implante', 'protesis_fija', 350.00, 'Rehabilitación protésica sobre implante', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(32, 'Ortodoncia metálica (consulta inicial)', 'ortodoncia', 30.00, 'Consulta inicial y plan de tratamiento', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(33, 'Ortodoncia metálica (mensualidad)', 'ortodoncia', 60.00, 'Control mensual de ortodoncia metálica', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(34, 'Ortodoncia estética (mensualidad)', 'ortodoncia', 80.00, 'Control mensual de ortodoncia estética', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(35, 'Retenedor removible', 'ortodoncia', 80.00, 'Retenedor postortodoncia removible', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(36, 'Retenedor fijo', 'ortodoncia', 60.00, 'Retenedor postortodoncia fijo', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(37, 'Radiografía periapical', 'rayos_x', 8.00, 'Radiografía de un diente y su raíz', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(38, 'Radiografía bite-wing', 'rayos_x', 10.00, 'Radiografía de aleta de mordida', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(39, 'Radiografía panorámica', 'rayos_x', 35.00, 'Radiografía panorámica de toda la boca', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(40, 'Consulta / Revisión general', 'otros', 15.00, 'Revisión general y diagnóstico', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(41, 'Blanqueamiento dental (consultorio)', 'otros', 120.00, 'Blanqueamiento profesional en consultorio', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(42, 'Blanqueamiento dental (casa)', 'otros', 80.00, 'Kit de blanqueamiento para uso en casa', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(43, 'Sellantes de fosas y fisuras', 'otros', 20.00, 'Sellantes preventivos por diente', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18'),
	(44, 'Tratamiento fluorización', 'otros', 25.00, 'Aplicación profesional de fluorización', 1, '2026-05-02 22:09:18', '2026-05-02 22:09:18');

-- Volcando estructura para tabla alpadent.tratamientos_realizados
CREATE TABLE IF NOT EXISTS `tratamientos_realizados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cita_id` bigint(20) unsigned NOT NULL,
  `tipo_tratamiento_id` bigint(20) unsigned NOT NULL,
  `diagnostico_id` bigint(20) unsigned DEFAULT NULL,
  `numero_diente` tinyint(3) unsigned DEFAULT NULL,
  `precio_aplicado` decimal(10,2) NOT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tratamientos_realizados_cita_id_index` (`cita_id`),
  KEY `tratamientos_realizados_tipo_tratamiento_id_index` (`tipo_tratamiento_id`),
  KEY `tratamientos_realizados_diagnostico_id_index` (`diagnostico_id`),
  CONSTRAINT `tratamientos_realizados_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`),
  CONSTRAINT `tratamientos_realizados_diagnostico_id_foreign` FOREIGN KEY (`diagnostico_id`) REFERENCES `diagnosticos` (`id`),
  CONSTRAINT `tratamientos_realizados_tipo_tratamiento_id_foreign` FOREIGN KEY (`tipo_tratamiento_id`) REFERENCES `tipos_tratamiento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.tratamientos_realizados: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.users: ~0 rows (aproximadamente)

-- Volcando estructura para tabla alpadent.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('administrador','secretaria') NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_email_index` (`email`),
  KEY `usuarios_rol_index` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.usuarios: ~2 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'Sistema', 'admin@alpadent.com', '$2y$12$D8ifada4qZFeDsyWKVk5T.3MaZ9BYvffl4v40Svv0yiSgwTKRvsW.', 'administrador', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17'),
	(2, 'Secretaria', 'Oficial', 'secretaria@alpadent.com', '$2y$12$tMbBXr4zErn/U3YKJJUJfulYo.5YptuQJiAoDIfx7lDSLNFURVblm', 'secretaria', 1, '2026-05-02 22:09:17', '2026-05-02 22:09:17');

-- Volcando estructura para vista alpadent.v_abonos_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_abonos_completo` 
);

-- Volcando estructura para vista alpadent.v_citas_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_citas_completo` (
	`cita_id` BIGINT(20) UNSIGNED NOT NULL,
	`fecha_hora_inicio` DATETIME NOT NULL,
	`tipo_tratamiento_id` BIGINT(20) UNSIGNED NULL,
	`tipo_tratamiento` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`precio_tratamiento` DECIMAL(10,2) NULL,
	`motivo_consulta` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado` ENUM('programada','en_curso','completada','cancelada','no_asistio') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado_descripcion` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`observaciones` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`es_primera_vez` TINYINT(1) NOT NULL,
	`paciente_id` BIGINT(20) UNSIGNED NOT NULL,
	`numero_historia` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`paciente_nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`paciente_edad` TINYINT(3) UNSIGNED NULL,
	`paciente_telefono` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`es_menor` TINYINT(1) NOT NULL,
	`usuario_id` BIGINT(20) UNSIGNED NOT NULL,
	`registrado_por` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`rol_usuario` ENUM('administrador','secretaria') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`doctor_externo_id` BIGINT(20) UNSIGNED NULL,
	`doctor_externo_nombre` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`especialidad` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`tiene_diagnostico` TINYINT(1) NULL,
	`tiene_pago` TINYINT(1) NULL,
	`total_tratamientos` DECIMAL(10,2) NULL,
	`created_at` TIMESTAMP NULL
);

-- Volcando estructura para vista alpadent.v_citas_hoy
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_citas_hoy` (
	`cita_id` BIGINT(20) UNSIGNED NOT NULL,
	`fecha_hora_inicio` DATETIME NOT NULL,
	`hora_inicio` TIME NULL,
	`tipo_tratamiento_id` BIGINT(20) UNSIGNED NULL,
	`tipo_tratamiento` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`precio_tratamiento` DECIMAL(10,2) NULL,
	`motivo_consulta` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado` ENUM('programada','en_curso','completada','cancelada','no_asistio') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado_descripcion` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`es_primera_vez` TINYINT(1) NOT NULL,
	`paciente_id` BIGINT(20) UNSIGNED NOT NULL,
	`numero_historia` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`paciente_nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`paciente_telefono` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`paciente_edad` TINYINT(3) UNSIGNED NULL,
	`es_menor` TINYINT(1) NOT NULL,
	`especialidad_doctor` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`doctor_externo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`tiene_diagnostico` TINYINT(1) NULL,
	`tiene_pago` TINYINT(1) NULL
);

-- Volcando estructura para vista alpadent.v_citas_proximas
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_citas_proximas` 
);

-- Volcando estructura para vista alpadent.v_dashboard_resumen
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_dashboard_resumen` 
);

-- Volcando estructura para vista alpadent.v_historial_clinico
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_historial_clinico` 
);

-- Volcando estructura para vista alpadent.v_ingresos_por_mes_anio_actual
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_ingresos_por_mes_anio_actual` (
	`numero_mes` INT(2) NOT NULL,
	`nombre_mes` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`ingresos_citas` DECIMAL(32,2) NULL,
	`ingresos_productos` DECIMAL(32,2) NULL,
	`total_mes` DECIMAL(33,2) NULL
);

-- Volcando estructura para vista alpadent.v_inventario_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_inventario_completo` (
	`id` BIGINT(20) UNSIGNED NOT NULL,
	`nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`marca` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`descripcion` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio_venta` DECIMAL(10,2) NOT NULL,
	`stock_actual` INT(11) NOT NULL,
	`stock_minimo` INT(11) NOT NULL,
	`margen_stock` BIGINT(12) NOT NULL,
	`alerta_stock_bajo` TINYINT(1) NULL,
	`estado_stock` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`activo` TINYINT(1) NOT NULL,
	`ultima_actualizacion` TIMESTAMP NULL
);

-- Volcando estructura para vista alpadent.v_movimientos_inventario_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_movimientos_inventario_completo` 
);

-- Volcando estructura para vista alpadent.v_odontograma_paciente
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_odontograma_paciente` (
	`paciente_id` BIGINT(20) UNSIGNED NOT NULL,
	`paciente_nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`numero_historia` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`numero_diente` TINYINT(3) UNSIGNED NOT NULL,
	`condicion` ENUM('sano','cariado','obturado','faltante','con_tratamiento_radicular','con_corona','con_puente','implante','ausente') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`superficie` SET('oclusal','vestibular','lingual','mesial','distal') NULL COLLATE 'utf8mb4_unicode_ci',
	`observacion` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_diagnostico` DATE NULL
);

-- Volcando estructura para vista alpadent.v_pacientes_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_pacientes_completo` (
	`id` BIGINT(20) UNSIGNED NOT NULL,
	`numero_historia` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`apellido` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_completo` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_nacimiento` DATE NOT NULL,
	`edad` TINYINT(3) UNSIGNED NULL,
	`sexo` ENUM('M','F') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sexo_descripcion` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`telefono` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`es_menor` TINYINT(1) NOT NULL,
	`activo` TINYINT(1) NOT NULL,
	`total_citas` INT(10) UNSIGNED NULL,
	`es_frecuente` TINYINT(1) NULL,
	`saldo_pendiente` DECIMAL(10,2) NULL,
	`total_pagado` DECIMAL(10,2) NULL,
	`ultima_cita` DATETIME NULL,
	`proxima_cita` DATETIME NULL,
	`dias_sin_visita` INT(11) NULL,
	`diabetes` TINYINT(1) NULL,
	`alergias_medicamentos` TINYINT(1) NULL,
	`detalle_alergias` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`problemas_hemorragicos` TINYINT(1) NULL,
	`problemas_cardiacos` TINYINT(1) NULL,
	`problemas_renales` TINYINT(1) NULL,
	`embarazo` TINYINT(1) NULL,
	`presion_arterial` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`otros_antecedentes` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_registro` TIMESTAMP NULL
);

-- Volcando estructura para vista alpadent.v_pagos_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_pagos_completo` 
);

-- Volcando estructura para vista alpadent.v_pagos_pendientes
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_pagos_pendientes` 
);

-- Volcando estructura para vista alpadent.v_recordatorios_pendientes
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_recordatorios_pendientes` 
);

-- Volcando estructura para vista alpadent.v_tratamientos_estadisticas
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_tratamientos_estadisticas` (
	`tipo_tratamiento_id` BIGINT(20) UNSIGNED NOT NULL,
	`nombre` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`categoria` ENUM('operatoria','limpieza','periodoncia','endodoncia','exodoncia','cirugia','protesis_removible','protesis_fija','ortodoncia','implantologia','rayos_x','otros') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio_catalogo` DECIMAL(10,2) NOT NULL,
	`veces_realizado` BIGINT(21) NOT NULL,
	`ingresos_totales` DECIMAL(32,2) NULL,
	`precio_promedio_aplicado` DECIMAL(14,6) NULL,
	`precio_minimo_aplicado` DECIMAL(10,2) NULL,
	`precio_maximo_aplicado` DECIMAL(10,2) NULL
);

-- Volcando estructura para vista alpadent.v_ventas_producto_completo
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_ventas_producto_completo` (
	`venta_id` BIGINT(20) UNSIGNED NOT NULL,
	`fecha_venta` TIMESTAMP NULL,
	`paciente_nombre` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`numero_historia` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`total` DECIMAL(10,2) NOT NULL,
	`metodo_pago` ENUM('efectivo','transferencia','tarjeta') NULL COLLATE 'utf8mb4_unicode_ci',
	`referencia` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`vendido_por` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`producto` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`marca` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`cantidad` INT(11) NOT NULL,
	`precio_unitario` DECIMAL(10,2) NOT NULL,
	`subtotal` DECIMAL(10,2) NOT NULL
);

-- Volcando estructura para tabla alpadent.ventas_producto
CREATE TABLE IF NOT EXISTS `ventas_producto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paciente_id` bigint(20) unsigned DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_pendiente` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','parcial','pagado') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` enum('efectivo','transferencia','tarjeta') DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ventas_producto_paciente_id_index` (`paciente_id`),
  KEY `ventas_producto_usuario_id_index` (`usuario_id`),
  KEY `ventas_producto_created_at_index` (`created_at`),
  CONSTRAINT `ventas_producto_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `ventas_producto_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla alpadent.ventas_producto: ~0 rows (aproximadamente)
INSERT INTO `ventas_producto` (`id`, `paciente_id`, `usuario_id`, `total`, `monto_pagado`, `saldo_pendiente`, `estado`, `metodo_pago`, `referencia`, `observaciones`, `created_at`, `updated_at`) VALUES
	(2, 1, 1, 16.00, 16.00, 0.00, 'pagado', 'efectivo', NULL, NULL, '2026-05-03 03:12:35', '2026-05-03 03:13:09');

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_abonos_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_abonos_completo` AS SELECT
    a.id                                                        AS abono_id,
    a.fecha,
    a.monto,
    a.metodo_pago,
    a.referencia,
    a.notas,
    pg.id                                                       AS pago_id,
    pg.monto_total,
    pg.estado                                                   AS estado_pago,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.numero_historia,
    CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido))              AS registrado_por
FROM abonos a
JOIN pagos pg    ON pg.id = a.pago_id
JOIN pacientes p ON p.id  = pg.paciente_id
JOIN usuarios u  ON u.id  = a.usuario_id
ORDER BY a.fecha DESC 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_citas_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_citas_completo` AS SELECT
        c.id AS cita_id,
        c.fecha_hora_inicio AS fecha_hora_inicio,
        c.tipo_tratamiento_id AS tipo_tratamiento_id,
        tt.nombre AS tipo_tratamiento,
        tt.precio AS precio_tratamiento,
        c.motivo_consulta AS motivo_consulta,
        c.estado AS estado,
        fn_descripcion_estado_cita(c.estado) AS estado_descripcion,
        c.observaciones AS observaciones,
        c.es_primera_vez AS es_primera_vez,
        p.id AS paciente_id,
        p.numero_historia AS numero_historia,
        CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido)) AS paciente_nombre,
        fn_calcular_edad(p.fecha_nacimiento) AS paciente_edad,
        p.telefono AS paciente_telefono,
        p.es_menor AS es_menor,
        u.id AS usuario_id,
        CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido)) AS registrado_por,
        u.rol AS rol_usuario,
        de.id AS doctor_externo_id,
        CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido)) AS doctor_externo_nombre,
        de.especialidad AS especialidad,
        fn_cita_tiene_diagnostico(c.id) AS tiene_diagnostico,
        fn_cita_tiene_pago(c.id) AS tiene_pago,
        fn_total_tratamientos_cita(c.id) AS total_tratamientos,
        c.created_at AS created_at
    FROM citas c
    JOIN pacientes p ON p.id = c.paciente_id
    JOIN usuarios u ON u.id = c.usuario_id
    LEFT JOIN tipos_tratamiento tt ON tt.id = c.tipo_tratamiento_id
    LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_citas_hoy`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_citas_hoy` AS SELECT
        c.id AS cita_id,
        c.fecha_hora_inicio AS fecha_hora_inicio,
        CAST(c.fecha_hora_inicio AS TIME) AS hora_inicio,
        c.tipo_tratamiento_id AS tipo_tratamiento_id,
        tt.nombre AS tipo_tratamiento,
        tt.precio AS precio_tratamiento,
        c.motivo_consulta AS motivo_consulta,
        c.estado AS estado,
        fn_descripcion_estado_cita(c.estado) AS estado_descripcion,
        c.es_primera_vez AS es_primera_vez,
        p.id AS paciente_id,
        p.numero_historia AS numero_historia,
        CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido)) AS paciente_nombre,
        p.telefono AS paciente_telefono,
        fn_calcular_edad(p.fecha_nacimiento) AS paciente_edad,
        p.es_menor AS es_menor,
        de.especialidad AS especialidad_doctor,
        CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido)) AS doctor_externo,
        fn_cita_tiene_diagnostico(c.id) AS tiene_diagnostico,
        fn_cita_tiene_pago(c.id) AS tiene_pago
    FROM citas c
    JOIN pacientes p ON p.id = c.paciente_id
    LEFT JOIN tipos_tratamiento tt ON tt.id = c.tipo_tratamiento_id
    LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id
    WHERE CAST(c.fecha_hora_inicio AS DATE) = CURDATE()
        AND c.estado NOT IN ('cancelada', 'no_asistio')
    ORDER BY c.fecha_hora_inicio 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_citas_proximas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_citas_proximas` AS SELECT
    c.id                                                        AS cita_id,
    DATE(c.fecha_hora_inicio)                                   AS fecha,
    TIME(c.fecha_hora_inicio)                                   AS hora_inicio,
    TIME(c.fecha_hora_fin)                                      AS hora_fin,
    c.motivo_consulta,
    c.estado,
    p.id                                                        AS paciente_id,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.telefono                                                  AS paciente_telefono,
    c.es_primera_vez,
    CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido))            AS doctor_externo,
    de.especialidad,
    DATEDIFF(DATE(c.fecha_hora_inicio), CURDATE())             AS dias_para_cita
FROM citas c
JOIN pacientes p              ON p.id  = c.paciente_id
LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id
WHERE c.fecha_hora_inicio > NOW()
  AND c.fecha_hora_inicio <= DATE_ADD(NOW(), INTERVAL 7 DAY)
  AND c.estado = 'programada'
ORDER BY c.fecha_hora_inicio 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_dashboard_resumen`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_dashboard_resumen` AS SELECT
    -- Citas
    fn_total_citas_mes(YEAR(NOW()), MONTH(NOW()))               AS citas_mes_actual,
    fn_tasa_asistencia_mes(YEAR(NOW()), MONTH(NOW()))           AS tasa_asistencia_mes,
    (SELECT COUNT(*) FROM citas
     WHERE DATE(fecha_hora_inicio) = CURDATE()
       AND estado NOT IN ('cancelada','no_asistio'))            AS citas_hoy,
    (SELECT COUNT(*) FROM citas
     WHERE estado = 'programada'
       AND fecha_hora_inicio > NOW())                           AS citas_futuras_pendientes,
    -- Pacientes
    fn_total_pacientes_nuevos_mes(YEAR(NOW()), MONTH(NOW()))    AS pacientes_nuevos_mes,
    (SELECT COUNT(*) FROM pacientes WHERE activo = 1)           AS total_pacientes_activos,
    -- Ingresos
    fn_ingresos_mes(YEAR(NOW()), MONTH(NOW()))                  AS ingresos_mes_actual,
    fn_ingresos_anio(YEAR(NOW()))                               AS ingresos_anio_actual,
    (SELECT COALESCE(SUM(saldo),0) FROM pagos
     WHERE estado != 'pagado')                                  AS total_saldo_pendiente,
    -- Tratamientos
    fn_tratamiento_mas_frecuente_mes(YEAR(NOW()), MONTH(NOW())) AS tratamiento_estrella_mes,
    -- Inventario
    (SELECT COUNT(*) FROM productos
     WHERE stock_actual <= stock_minimo AND activo = 1)         AS productos_stock_bajo 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_historial_clinico`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_historial_clinico` AS SELECT
    p.id                                                        AS paciente_id,
    p.numero_historia,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    c.id                                                        AS cita_id,
    DATE(c.fecha_hora_inicio)                                   AS fecha_cita,
    c.motivo_consulta,
    c.estado                                                    AS estado_cita,
    c.es_primera_vez,
    -- Doctor
    COALESCE(
        CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido)),
        'Odontóloga principal'
    )                                                           AS atendido_por,
    de.especialidad,
    -- Diagnóstico
    d.id                                                        AS diagnostico_id,
    d.descripcion                                               AS diagnostico,
    d.gingivitis,
    d.enfermedad_periodontal,
    d.indice_cpo_cariados,
    d.indice_cpo_perdidos,
    d.indice_cpo_obturados,
    -- Tratamiento
    tt.nombre                                                   AS tratamiento,
    tt.categoria                                                AS categoria_tratamiento,
    tr.numero_diente,
    tr.precio_aplicado,
    tr.notas                                                    AS notas_tratamiento,
    -- Pago
    pg.monto_total,
    pg.monto_abonado,
    pg.saldo,
    fn_descripcion_estado_pago(pg.estado)                      AS estado_pago
FROM pacientes p
JOIN citas c                      ON c.paciente_id  = p.id
LEFT JOIN doctores_externos de    ON de.id           = c.doctor_externo_id
LEFT JOIN diagnosticos d          ON d.cita_id       = c.id
LEFT JOIN tratamientos_realizados tr ON tr.cita_id   = c.id
LEFT JOIN tipos_tratamiento tt    ON tt.id            = tr.tipo_tratamiento_id
LEFT JOIN pagos pg                ON pg.cita_id       = c.id
WHERE p.activo = 1
ORDER BY p.id, c.fecha_hora_inicio DESC 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_ingresos_por_mes_anio_actual`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_ingresos_por_mes_anio_actual` AS SELECT
    mes.numero_mes,
    mes.nombre_mes,
    COALESCE(SUM(a.monto), 0)                                  AS ingresos_citas,
    COALESCE((
        SELECT SUM(vp.total)
        FROM ventas_producto vp
        WHERE YEAR(vp.created_at)  = YEAR(NOW())
          AND MONTH(vp.created_at) = mes.numero_mes
    ), 0)                                                      AS ingresos_productos,
    COALESCE(SUM(a.monto), 0) + COALESCE((
        SELECT SUM(vp.total)
        FROM ventas_producto vp
        WHERE YEAR(vp.created_at)  = YEAR(NOW())
          AND MONTH(vp.created_at) = mes.numero_mes
    ), 0)                                                      AS total_mes
FROM (
    SELECT 1  AS numero_mes, 'Enero'      AS nombre_mes UNION ALL
    SELECT 2,  'Febrero'   UNION ALL
    SELECT 3,  'Marzo'     UNION ALL
    SELECT 4,  'Abril'     UNION ALL
    SELECT 5,  'Mayo'      UNION ALL
    SELECT 6,  'Junio'     UNION ALL
    SELECT 7,  'Julio'     UNION ALL
    SELECT 8,  'Agosto'    UNION ALL
    SELECT 9,  'Septiembre' UNION ALL
    SELECT 10, 'Octubre'   UNION ALL
    SELECT 11, 'Noviembre' UNION ALL
    SELECT 12, 'Diciembre'
) mes
LEFT JOIN abonos a ON YEAR(a.fecha) = YEAR(NOW()) AND MONTH(a.fecha) = mes.numero_mes
GROUP BY mes.numero_mes, mes.nombre_mes
ORDER BY mes.numero_mes 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_inventario_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_inventario_completo` AS SELECT
    pr.id,
    pr.nombre,
    pr.marca,
    pr.descripcion,
    pr.precio_venta,
    pr.stock_actual,
    pr.stock_minimo,
    (pr.stock_actual - pr.stock_minimo)                        AS margen_stock,
    fn_producto_bajo_stock(pr.id)                              AS alerta_stock_bajo,
    CASE
        WHEN pr.stock_actual = 0        THEN 'Sin stock'
        WHEN pr.stock_actual <= pr.stock_minimo THEN 'Stock bajo'
        ELSE 'Stock normal'
    END                                                        AS estado_stock,
    pr.activo,
    pr.updated_at                                              AS ultima_actualizacion
FROM productos pr
ORDER BY fn_producto_bajo_stock(pr.id) DESC, pr.nombre 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_movimientos_inventario_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_movimientos_inventario_completo` AS SELECT
    mi.id                                                       AS movimiento_id,
    mi.created_at                                              AS fecha,
    pr.nombre                                                   AS producto,
    pr.marca,
    mi.tipo,
    CASE mi.tipo
        WHEN 'entrada' THEN 'Entrada de stock'
        WHEN 'salida'  THEN 'Salida de stock'
        WHEN 'ajuste'  THEN 'Ajuste de inventario'
    END                                                        AS tipo_descripcion,
    mi.cantidad,
    mi.stock_antes,
    mi.stock_despues,
    mi.motivo,
    CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido))              AS registrado_por
FROM movimientos_inventario mi
JOIN productos pr ON pr.id = mi.producto_id
JOIN usuarios u   ON u.id  = mi.usuario_id
ORDER BY mi.created_at DESC 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_odontograma_paciente`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_odontograma_paciente` AS SELECT
    p.id                                                        AS paciente_id,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.numero_historia,
    dd.numero_diente,
    dd.condicion,
    dd.superficie,
    dd.observacion,
    DATE(c.fecha_hora_inicio)                                   AS fecha_diagnostico
FROM pacientes p
JOIN citas c          ON c.paciente_id   = p.id
JOIN diagnosticos d   ON d.cita_id       = c.id
JOIN dientes_diagnostico dd ON dd.diagnostico_id = d.id
WHERE c.estado = 'completada'
  AND c.fecha_hora_inicio = (
      SELECT MAX(c2.fecha_hora_inicio)
      FROM citas c2
      JOIN diagnosticos d2       ON d2.cita_id = c2.id
      JOIN dientes_diagnostico dd2 ON dd2.diagnostico_id = d2.id
      WHERE c2.paciente_id = p.id
        AND dd2.numero_diente = dd.numero_diente
        AND c2.estado = 'completada'
  ) 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_pacientes_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_pacientes_completo` AS SELECT
    p.id,
    p.numero_historia,
    p.nombre,
    p.apellido,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))   AS nombre_completo,
    p.fecha_nacimiento,
    fn_calcular_edad(p.fecha_nacimiento)             AS edad,
    p.sexo,
    CASE p.sexo WHEN 'M' THEN 'Masculino' ELSE 'Femenino' END AS sexo_descripcion,
    p.telefono,
    p.email, -- <--- Nueva columna agregada
    p.direccion,
    p.es_menor,
    p.activo,
    fn_total_citas_paciente(p.id)                   AS total_citas,
    fn_es_paciente_frecuente(p.id)                  AS es_frecuente,
    fn_saldo_pendiente_paciente(p.id)               AS saldo_pendiente,
    fn_total_pagado_paciente(p.id)                  AS total_pagado,
    fn_ultima_cita_paciente(p.id)                   AS ultima_cita,
    fn_proxima_cita_paciente(p.id)                  AS proxima_cita,
    fn_dias_sin_visita(p.id)                        AS dias_sin_visita,
    am.diabetes,
    am.alergias_medicamentos,
    am.detalle_alergias,
    am.problemas_hemorragicos,
    am.problemas_cardiacos,
    am.problemas_renales,
    am.embarazo,
    am.presion_arterial,
    am.otros                                        AS otros_antecedentes,
    p.created_at                                    AS fecha_registro
FROM pacientes p
LEFT JOIN antecedentes_medicos am ON am.paciente_id = p.id 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_pagos_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_pagos_completo` AS SELECT
    pg.id                                                       AS pago_id,
    pg.created_at                                               AS fecha_pago,
    p.id                                                        AS paciente_id,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.numero_historia,
    c.id                                                        AS cita_id,
    DATE(c.fecha_hora_inicio)                                   AS fecha_cita,
    pg.monto_total,
    pg.monto_abonado,
    pg.saldo,
    pg.metodo_pago_inicial,
    pg.referencia_transferencia,
    pg.estado,
    fn_descripcion_estado_pago(pg.estado)                       AS estado_descripcion,
    CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido))              AS cobrado_por,
    fn_total_tratamientos_cita(c.id)                           AS total_tratamientos_cita
FROM pagos pg
JOIN pacientes p ON p.id = pg.paciente_id
JOIN citas c     ON c.id = pg.cita_id
JOIN usuarios u  ON u.id = pg.usuario_id 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_pagos_pendientes`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_pagos_pendientes` AS SELECT
    pg.id                                                       AS pago_id,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.numero_historia,
    p.telefono,
    DATE(c.fecha_hora_inicio)                                   AS fecha_cita,
    pg.monto_total,
    pg.monto_abonado,
    pg.saldo,
    pg.estado,
    pg.created_at                                               AS fecha_pago,
    DATEDIFF(CURDATE(), DATE(pg.created_at))                   AS dias_pendiente
FROM pagos pg
JOIN pacientes p ON p.id = pg.paciente_id
JOIN citas c     ON c.id = pg.cita_id
WHERE pg.estado IN ('pendiente', 'parcial')
ORDER BY dias_pendiente DESC 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_recordatorios_pendientes`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_recordatorios_pendientes` AS SELECT
    r.id                                                        AS recordatorio_id,
    r.canal,
    r.estado,
    r.intento,
    c.id                                                        AS cita_id,
    c.fecha_hora_inicio,
    DATE(c.fecha_hora_inicio)                                   AS fecha_cita,
    TIME(c.fecha_hora_inicio)                                   AS hora_cita,
    DATEDIFF(DATE(c.fecha_hora_inicio), CURDATE())              AS dias_para_cita,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))               AS paciente_nombre,
    p.telefono,
    p.email -- <--- Nueva columna para notificaciones por correo
FROM recordatorios r
JOIN citas c     ON c.id = r.cita_id
JOIN pacientes p ON p.id = c.paciente_id
WHERE r.estado = 'pendiente'
  AND c.estado = 'programada'
  AND c.fecha_hora_inicio > NOW()
ORDER BY c.fecha_hora_inicio 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_tratamientos_estadisticas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_tratamientos_estadisticas` AS SELECT
    tt.id                                                       AS tipo_tratamiento_id,
    tt.nombre,
    tt.categoria,
    tt.precio                                                   AS precio_catalogo,
    COUNT(tr.id)                                               AS veces_realizado,
    COALESCE(SUM(tr.precio_aplicado), 0)                       AS ingresos_totales,
    COALESCE(AVG(tr.precio_aplicado), 0)                       AS precio_promedio_aplicado,
    COALESCE(MIN(tr.precio_aplicado), 0)                       AS precio_minimo_aplicado,
    COALESCE(MAX(tr.precio_aplicado), 0)                       AS precio_maximo_aplicado
FROM tipos_tratamiento tt
LEFT JOIN tratamientos_realizados tr ON tr.tipo_tratamiento_id = tt.id
LEFT JOIN citas c ON c.id = tr.cita_id AND c.estado = 'completada'
WHERE tt.activo = 1
GROUP BY tt.id, tt.nombre, tt.categoria, tt.precio
ORDER BY veces_realizado DESC 
;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_ventas_producto_completo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_ventas_producto_completo` AS SELECT
    vp.id                                                       AS venta_id,
    vp.created_at                                              AS fecha_venta,
    CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido))              AS paciente_nombre,
    p.numero_historia,
    vp.total,
    vp.metodo_pago,
    vp.referencia,
    CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido))              AS vendido_por,
    -- Detalle de productos
    pr.nombre                                                   AS producto,
    pr.marca,
    dv.cantidad,
    dv.precio_unitario,
    dv.subtotal
FROM ventas_producto vp
LEFT JOIN pacientes p  ON p.id  = vp.paciente_id
JOIN usuarios u        ON u.id  = vp.usuario_id
JOIN detalle_venta dv  ON dv.venta_id    = vp.id
JOIN productos pr      ON pr.id = dv.producto_id
ORDER BY vp.created_at DESC 
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
