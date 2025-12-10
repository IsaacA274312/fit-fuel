-- Migración para actualizar la tabla usuarios con los campos necesarios
-- Ejecutar solo si ya tienes una tabla usuarios existente

USE fitandfuel;

-- Verificar y agregar columnas si no existen
SET @exist_telefono = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'telefono'
);

SET @exist_fecha_nacimiento = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'fecha_nacimiento'
);

SET @exist_genero = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'genero'
);

SET @exist_tipo_usuario = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'tipo_usuario'
);

SET @exist_creado_en = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'creado_en'
);

SET @exist_actualizado_en = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND COLUMN_NAME = 'actualizado_en'
);

-- Agregar columna telefono si no existe
SET @sql_telefono = IF(@exist_telefono = 0, 
    'ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(30) DEFAULT NULL AFTER password',
    'SELECT "La columna telefono ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_telefono;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna fecha_nacimiento si no existe
SET @sql_fecha = IF(@exist_fecha_nacimiento = 0, 
    'ALTER TABLE usuarios ADD COLUMN fecha_nacimiento DATE DEFAULT NULL AFTER telefono',
    'SELECT "La columna fecha_nacimiento ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_fecha;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna genero si no existe
SET @sql_genero = IF(@exist_genero = 0, 
    'ALTER TABLE usuarios ADD COLUMN genero VARCHAR(50) DEFAULT NULL AFTER fecha_nacimiento',
    'SELECT "La columna genero ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_genero;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna tipo_usuario si no existe
SET @sql_tipo = IF(@exist_tipo_usuario = 0, 
    'ALTER TABLE usuarios ADD COLUMN tipo_usuario VARCHAR(50) NOT NULL DEFAULT "usuario" AFTER genero',
    'SELECT "La columna tipo_usuario ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_tipo;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna creado_en si no existe
SET @sql_creado = IF(@exist_creado_en = 0, 
    'ALTER TABLE usuarios ADD COLUMN creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER tipo_usuario',
    'SELECT "La columna creado_en ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_creado;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar columna actualizado_en si no existe
SET @sql_actualizado = IF(@exist_actualizado_en = 0, 
    'ALTER TABLE usuarios ADD COLUMN actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER creado_en',
    'SELECT "La columna actualizado_en ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_actualizado;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar índice en tipo_usuario si no existe
SET @exist_index = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'fitandfuel' 
    AND TABLE_NAME = 'usuarios' 
    AND INDEX_NAME = 'tipo_usuario'
);

SET @sql_index = IF(@exist_index = 0, 
    'ALTER TABLE usuarios ADD INDEX (tipo_usuario)',
    'SELECT "El índice tipo_usuario ya existe" AS mensaje'
);
PREPARE stmt FROM @sql_index;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mostrar resultado
SELECT 'Migración completada exitosamente' AS resultado;
SHOW COLUMNS FROM usuarios;
