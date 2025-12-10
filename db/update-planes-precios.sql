-- Actualizar planes alimenticios con precios y hacerlos públicos
UPDATE planes_alimenticios 
SET precio = CASE 
    WHEN nombre LIKE '%Pérdida%' OR nombre LIKE '%peso%' THEN 499.00
    WHEN nombre LIKE '%Ganancia%' OR nombre LIKE '%músculo%' THEN 599.00
    WHEN nombre LIKE '%Manteni%' THEN 449.00
    WHEN nombre LIKE '%Vegano%' OR nombre LIKE '%Vegetariano%' THEN 549.00
    ELSE 399.00
END,
publico = 1
WHERE precio IS NULL OR precio = 0;

-- Insertar planes de ejemplo si no existen
INSERT IGNORE INTO planes_alimenticios 
(nutriologo_id, usuario_id, nombre, descripcion, objetivo, calorias_diarias, duracion_dias, fecha_inicio, activo, publico, precio)
VALUES
(4, NULL, 'Plan Pérdida de Peso', 'Plan diseñado para pérdida de grasa sostenible con déficit calórico controlado', 'Pérdida de peso', 1800, 30, NULL, 1, 1, 499.00),
(4, NULL, 'Plan Ganancia Muscular', 'Plan hipercalórico para aumento de masa muscular con enfoque en proteínas', 'Ganancia muscular', 3000, 45, NULL, 1, 1, 599.00),
(4, NULL, 'Plan Mantenimiento', 'Plan equilibrado para mantener tu peso actual y mejorar composición corporal', 'Mantenimiento', 2200, 30, NULL, 1, 1, 449.00),
(4, NULL, 'Plan Vegano Fitness', 'Plan basado en plantas con proteínas vegetales de alta calidad', 'Salud y fitness', 2400, 30, NULL, 1, 1, 549.00),
(4, NULL, 'Plan Definición Avanzada', 'Plan para definición muscular con carbohidratos cíclicos', 'Definición', 2000, 60, NULL, 1, 1, 699.00);

SELECT 'Planes actualizados correctamente' AS resultado;
SELECT COUNT(*) as total_planes, SUM(publico) as planes_publicos FROM planes_alimenticios;
