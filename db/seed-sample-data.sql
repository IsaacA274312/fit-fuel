-- Script de datos de ejemplo para FitAndFuel
-- Ejecutar después de crear las tablas base

USE fitandfuel;

-- Insertar categorías de productos
INSERT INTO categorias (nombre, descripcion, creado_en) VALUES
('Suplementos', 'Proteínas, creatina, vitaminas y suplementos deportivos', NOW()),
('Equipamiento', 'Pesas, bandas elásticas, mancuernas y accesorios', NOW()),
('Ropa Deportiva', 'Ropa y calzado para entrenamiento', NOW()),
('Nutrición', 'Alimentos saludables, snacks y bebidas', NOW()),
('Accesorios', 'Guantes, straps, cinturones y más', NOW());

-- Insertar productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen_url, creado_en) VALUES
-- Suplementos
('Proteína Whey 2kg', 'Proteína de suero de leche aislada, sabor chocolate', 899.00, 45, 1, 'whey-protein.jpg', NOW()),
('Creatina Monohidrato 300g', 'Creatina pura micronizada para rendimiento', 349.00, 60, 1, 'creatine.jpg', NOW()),
('BCAA 2:1:1 500g', 'Aminoácidos ramificados para recuperación muscular', 449.00, 35, 1, 'bcaa.jpg', NOW()),
('Pre-Workout Extreme', 'Fórmula energética pre-entrenamiento', 599.00, 28, 1, 'preworkout.jpg', NOW()),
('Multivitamínico Premium', 'Complejo vitamínico completo 60 cápsulas', 279.00, 50, 1, 'vitamins.jpg', NOW()),

-- Equipamiento
('Mancuernas Ajustables 20kg', 'Set de mancuernas con discos intercambiables', 1499.00, 15, 2, 'dumbbells.jpg', NOW()),
('Banda Elástica Set 5pz', 'Bandas de resistencia con diferentes niveles', 399.00, 40, 2, 'bands.jpg', NOW()),
('Barra Olímpica 20kg', 'Barra profesional para levantamiento', 2899.00, 8, 2, 'barbell.jpg', NOW()),
('Kettlebell 16kg', 'Pesa rusa de hierro fundido', 799.00, 22, 2, 'kettlebell.jpg', NOW()),
('Tapete Yoga Premium', 'Tapete antiderrapante 6mm grosor', 549.00, 30, 2, 'yoga-mat.jpg', NOW()),

-- Ropa Deportiva
('Playera Dry-Fit Hombre', 'Playera deportiva transpirable talla M-XL', 349.00, 55, 3, 'shirt-m.jpg', NOW()),
('Leggings Deportivos Mujer', 'Mallas de compresión para entrenamiento', 499.00, 48, 3, 'leggings.jpg', NOW()),
('Short Running Unisex', 'Short ligero con bolsillos laterales', 379.00, 42, 3, 'shorts.jpg', NOW()),
('Tenis Training Pro', 'Calzado deportivo de alto rendimiento', 1899.00, 25, 3, 'shoes.jpg', NOW()),

-- Nutrición
('Barras Proteicas Caja 12pz', 'Snack proteico sabor chocolate chip', 299.00, 70, 4, 'protein-bars.jpg', NOW()),
('Mantequilla de Maní Natural', 'Mantequilla 100% maní sin azúcar 500g', 159.00, 65, 4, 'peanut-butter.jpg', NOW()),
('Bebida Isotónica 1L', 'Hidratación con electrolitos sabor limón', 45.00, 120, 4, 'isotonic.jpg', NOW()),
('Avena Instantánea 1kg', 'Hojuelas de avena integral', 89.00, 85, 4, 'oats.jpg', NOW()),

-- Accesorios
('Guantes Gym Pro', 'Guantes con muñequera ajustable talla L', 249.00, 38, 5, 'gloves.jpg', NOW()),
('Cinturón Levantamiento', 'Cinturón de cuero para soporte lumbar', 699.00, 18, 5, 'belt.jpg', NOW()),
('Shaker Mezclador 700ml', 'Vaso mezclador con compartimento', 129.00, 95, 5, 'shaker.jpg', NOW()),
('Straps Muñeca Par', 'Correas de agarre para pull-ups', 189.00, 45, 5, 'straps.jpg', NOW());

-- Insertar clientes de ejemplo
INSERT INTO clientes (nombre, apellido, email, telefono, direccion, creado_en) VALUES
('Ana', 'Martínez López', 'ana.martinez@email.com', '5512345678', 'Av. Reforma 123, CDMX', NOW()),
('Carlos', 'Rodríguez Pérez', 'carlos.rodriguez@email.com', '5523456789', 'Calle Juárez 45, Guadalajara', NOW()),
('María', 'González Sánchez', 'maria.gonzalez@email.com', '5534567890', 'Paseo de la Reforma 89, CDMX', NOW()),
('Luis', 'Hernández Torres', 'luis.hernandez@email.com', '5545678901', 'Blvd. Insurgentes 234, Monterrey', NOW()),
('Diana', 'Ramírez Cruz', 'diana.ramirez@email.com', '5556789012', 'Av. Universidad 567, Puebla', NOW()),
('Roberto', 'Flores Morales', 'roberto.flores@email.com', '5567890123', 'Calle Madero 78, Querétaro', NOW()),
('Laura', 'Castro Ruiz', 'laura.castro@email.com', '5578901234', 'Av. Constitución 345, León', NOW()),
('Jorge', 'Mendoza Vega', 'jorge.mendoza@email.com', '5589012345', 'Calle Hidalgo 12, Morelia', NOW());

-- Insertar algunos pedidos de ejemplo
INSERT INTO pedidos (cliente_id, total, estado, fecha_pedido, creado_en) VALUES
(1, 1798.00, 'completado', '2025-11-15 10:30:00', '2025-11-15 10:30:00'),
(2, 899.00, 'completado', '2025-11-18 14:20:00', '2025-11-18 14:20:00'),
(3, 2648.00, 'en_proceso', '2025-11-25 09:15:00', '2025-11-25 09:15:00'),
(4, 1048.00, 'completado', '2025-11-20 16:45:00', '2025-11-20 16:45:00'),
(5, 548.00, 'pendiente', '2025-11-28 11:00:00', '2025-11-28 11:00:00'),
(1, 1349.00, 'en_proceso', '2025-11-27 13:30:00', '2025-11-27 13:30:00'),
(6, 699.00, 'completado', '2025-11-22 15:20:00', '2025-11-22 15:20:00'),
(7, 2298.00, 'completado', '2025-11-19 10:00:00', '2025-11-19 10:00:00');

-- Detalles de pedidos (items de cada pedido)
-- Pedido 1 de Ana
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(1, 1, 2, 899.00);  -- 2 Proteínas Whey

-- Pedido 2 de Carlos
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(2, 1, 1, 899.00);  -- 1 Proteína Whey

-- Pedido 3 de María (en proceso)
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(3, 6, 1, 1499.00), -- Mancuernas
(3, 10, 2, 549.00),  -- 2 Tapetes Yoga
(3, 21, 2, 25.50);   -- Bebidas (descuento)

-- Pedido 4 de Luis
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(4, 2, 2, 349.00),  -- 2 Creatinas
(4, 11, 1, 349.00); -- 1 Playera

-- Pedido 5 de Diana (pendiente)
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(5, 10, 1, 549.00); -- Tapete Yoga

-- Pedido 6 de Ana (segundo pedido, en proceso)
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(6, 3, 3, 449.00);  -- 3 BCAA

-- Pedido 7 de Roberto
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(7, 20, 1, 699.00); -- Cinturón

-- Pedido 8 de Laura
INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES
(8, 8, 1, 2899.00); -- Barra Olímpica

-- Mostrar resumen
SELECT 'Datos insertados correctamente' AS Resultado;
SELECT COUNT(*) AS Total_Categorias FROM categorias;
SELECT COUNT(*) AS Total_Productos FROM productos;
SELECT COUNT(*) AS Total_Clientes FROM clientes;
SELECT COUNT(*) AS Total_Pedidos FROM pedidos;
SELECT COUNT(*) AS Total_Detalles FROM detalle_pedidos;
