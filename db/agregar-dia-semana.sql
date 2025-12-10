-- Agregar columna dia_semana a rutina_ejercicios
ALTER TABLE rutina_ejercicios ADD COLUMN dia_semana TINYINT(1) DEFAULT NULL COMMENT '1=Lunes, 7=Domingo' AFTER orden;

-- Resultado
SELECT '✅ Columna dia_semana agregada correctamente' AS Resultado;
