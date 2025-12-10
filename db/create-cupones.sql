-- Crear tabla de cupones de descuento
CREATE TABLE IF NOT EXISTS cupones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    tipo_descuento ENUM('porcentaje', 'monto_fijo') DEFAULT 'porcentaje',
    valor_descuento DECIMAL(10,2) NOT NULL,
    monto_minimo DECIMAL(10,2) DEFAULT 0,
    fecha_inicio DATE,
    fecha_fin DATE,
    usos_maximos INT DEFAULT NULL,
    usos_actuales INT DEFAULT 0,
    activo BOOLEAN DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de uso de cupones por usuario
CREATE TABLE IF NOT EXISTS cupones_usados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cupon_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    pedido_id INT UNSIGNED NOT NULL,
    descuento_aplicado DECIMAL(10,2) NOT NULL,
    fecha_uso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cupon_id) REFERENCES cupones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_cupon (cupon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columnas a tabla pedidos si no existen
ALTER TABLE pedidos 
ADD COLUMN IF NOT EXISTS cupon_id INT UNSIGNED DEFAULT NULL,
ADD COLUMN IF NOT EXISTS descuento DECIMAL(10,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) DEFAULT 0;

-- Insertar cupones de ejemplo
INSERT INTO cupones (codigo, descripcion, tipo_descuento, valor_descuento, monto_minimo, fecha_inicio, fecha_fin, usos_maximos, activo) VALUES
('BIENVENIDO10', 'Cupón de bienvenida 10% descuento', 'porcentaje', 10.00, 500.00, '2025-01-01', '2025-12-31', 100, 1),
('FITMAS25', 'Descuento especial de fin de año 25%', 'porcentaje', 25.00, 1000.00, '2025-12-01', '2025-12-31', 50, 1),
('VERANO100', '$100 de descuento en compras mayores a $2000', 'monto_fijo', 100.00, 2000.00, '2025-06-01', '2025-08-31', NULL, 1),
('PROTEINA15', '15% en productos de proteína', 'porcentaje', 15.00, 0, '2025-01-01', '2025-12-31', NULL, 1),
('PRIMERACOMPRA', 'Primera compra 20% descuento', 'porcentaje', 20.00, 0, '2025-01-01', '2025-12-31', NULL, 1);

SELECT 'Sistema de cupones creado correctamente' AS resultado;
SELECT COUNT(*) as total_cupones FROM cupones WHERE activo = 1;
