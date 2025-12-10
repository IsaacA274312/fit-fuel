-- Script para resetear la base de datos FitAndFuel
-- ¡CUIDADO! Este script eliminará TODOS los datos

USE fitandfuel;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar todas las tablas
DROP TABLE IF EXISTS seguimiento_rutina;
DROP TABLE IF EXISTS rutina_ejercicios;
DROP TABLE IF EXISTS ejercicios;
DROP TABLE IF EXISTS rutinas;
DROP TABLE IF EXISTS suplemento_recomendaciones;
DROP TABLE IF EXISTS suplementos;
DROP TABLE IF EXISTS comidas;
DROP TABLE IF EXISTS planes_alimenticios;
DROP TABLE IF EXISTS evaluaciones;
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS usuario_membresia;
DROP TABLE IF EXISTS membresias;
DROP TABLE IF EXISTS perfiles_profesionales;
DROP TABLE IF EXISTS usuarios;

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Base de datos reseteada. Ahora ejecuta fit-fuel.sql para recrear las tablas.' as mensaje;
