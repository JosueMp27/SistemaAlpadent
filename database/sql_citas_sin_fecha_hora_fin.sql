-- Documentacion de archivo: Script SQL de soporte usado para consultar o ajustar estructuras relacionadas con citas.
-- Este comentario explica el uso del script sin cambiar sus sentencias SQL.
-- ALPADENT - Ajuste de citas a una sola fecha/hora de inicio
-- Ejecutar sobre la base seleccionada: USE alpadent;

SET @db_name := DATABASE();

DROP VIEW IF EXISTS v_citas_completo;
DROP VIEW IF EXISTS v_citas_hoy;

-- Agrega el tratamiento seleccionado a citas si todavia no existe.
SET @has_tipo_tratamiento_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'citas'
      AND COLUMN_NAME = 'tipo_tratamiento_id'
);

SET @sql := IF(
    @has_tipo_tratamiento_id = 0,
    'ALTER TABLE citas ADD COLUMN tipo_tratamiento_id BIGINT UNSIGNED NULL AFTER usuario_id',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_fk_tipo_tratamiento := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = @db_name
      AND TABLE_NAME = 'citas'
      AND COLUMN_NAME = 'tipo_tratamiento_id'
      AND REFERENCED_TABLE_NAME = 'tipos_tratamiento'
);

SET @sql := IF(
    @has_fk_tipo_tratamiento = 0,
    'ALTER TABLE citas ADD CONSTRAINT fk_citas_tipo_tratamiento FOREIGN KEY (tipo_tratamiento_id) REFERENCES tipos_tratamiento(id) ON DELETE RESTRICT ON UPDATE CASCADE',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Elimina checks conocidos que dependen de fecha_hora_fin si fueron creados por el script SQL original.
ALTER TABLE citas DROP CONSTRAINT IF EXISTS chk_citas_horario;
ALTER TABLE citas DROP CONSTRAINT IF EXISTS chk_citas_hora_fin;
ALTER TABLE citas DROP CONSTRAINT IF EXISTS chk_citas_duracion_minima;

-- Elimina fecha_hora_fin de todas las tablas base que todavia tengan esa columna.
DROP PROCEDURE IF EXISTS sp_drop_fecha_hora_fin_from_all_tables;

DELIMITER //
CREATE PROCEDURE sp_drop_fecha_hora_fin_from_all_tables()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE table_name_to_alter VARCHAR(255);

    DECLARE cur CURSOR FOR
        SELECT TABLE_NAME
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND COLUMN_NAME = 'fecha_hora_fin'
          AND TABLE_NAME IN (
              SELECT TABLE_NAME
              FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_TYPE = 'BASE TABLE'
          );

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO table_name_to_alter;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET @sql := CONCAT('ALTER TABLE `', table_name_to_alter, '` DROP COLUMN `fecha_hora_fin`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;

    CLOSE cur;
END //
DELIMITER ;

CALL sp_drop_fecha_hora_fin_from_all_tables();
DROP PROCEDURE IF EXISTS sp_drop_fecha_hora_fin_from_all_tables;
