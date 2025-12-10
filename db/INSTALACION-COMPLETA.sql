-- ============================================
-- SCRIPT COMPLETO DE INSTALACIÓN
-- Ejecuta este archivo único en phpMyAdmin
-- ============================================

-- 1. SISTEMA DE CUPONES
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

-- Agregar columnas a pedidos
ALTER TABLE pedidos 
ADD COLUMN IF NOT EXISTS cupon_id INT UNSIGNED DEFAULT NULL,
ADD COLUMN IF NOT EXISTS descuento DECIMAL(10,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) DEFAULT 0;

-- Cupones de ejemplo
INSERT INTO cupones (codigo, descripcion, tipo_descuento, valor_descuento, monto_minimo, fecha_inicio, fecha_fin, usos_maximos, activo) VALUES
('BIENVENIDO10', 'Cupón de bienvenida 10% descuento', 'porcentaje', 10.00, 500.00, '2025-01-01', '2025-12-31', 100, 1),
('FITMAS25', 'Descuento especial de fin de año 25%', 'porcentaje', 25.00, 1000.00, '2025-12-01', '2025-12-31', 50, 1),
('VERANO100', '$100 de descuento en compras mayores a $2000', 'monto_fijo', 100.00, 2000.00, '2025-06-01', '2025-08-31', NULL, 1),
('PROTEINA15', '15% en productos de proteína', 'porcentaje', 15.00, 0, '2025-01-01', '2025-12-31', NULL, 1),
('PRIMERACOMPRA', 'Primera compra 20% descuento', 'porcentaje', 20.00, 0, '2025-01-01', '2025-12-31', NULL, 1)
ON DUPLICATE KEY UPDATE codigo = codigo;

-- 2. SISTEMA DE PROGRESO
CREATE TABLE IF NOT EXISTS progreso_usuario (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_registro DATE NOT NULL,
    peso DECIMAL(5,2) DEFAULT NULL,
    altura DECIMAL(5,2) DEFAULT NULL,
    pecho DECIMAL(5,2) DEFAULT NULL,
    cintura DECIMAL(5,2) DEFAULT NULL,
    cadera DECIMAL(5,2) DEFAULT NULL,
    brazo_derecho DECIMAL(5,2) DEFAULT NULL,
    brazo_izquierdo DECIMAL(5,2) DEFAULT NULL,
    pierna_derecha DECIMAL(5,2) DEFAULT NULL,
    pierna_izquierda DECIMAL(5,2) DEFAULT NULL,
    porcentaje_grasa DECIMAL(4,2) DEFAULT NULL,
    masa_muscular DECIMAL(5,2) DEFAULT NULL,
    imc DECIMAL(4,2) DEFAULT NULL,
    notas TEXT DEFAULT NULL,
    foto_frontal VARCHAR(255) DEFAULT NULL,
    foto_lateral VARCHAR(255) DEFAULT NULL,
    foto_trasera VARCHAR(255) DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_registro),
    INDEX idx_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS objetivos_usuario (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    tipo_objetivo ENUM('peso', 'grasa', 'musculo', 'medidas', 'personalizado') NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    valor_actual DECIMAL(10,2) DEFAULT NULL,
    valor_objetivo DECIMAL(10,2) NOT NULL,
    unidad VARCHAR(20) DEFAULT 'kg',
    fecha_inicio DATE NOT NULL,
    fecha_objetivo DATE DEFAULT NULL,
    completado BOOLEAN DEFAULT 0,
    fecha_completado DATE DEFAULT NULL,
    activo BOOLEAN DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_activo (usuario_id, activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. SISTEMA DE NOTIFICACIONES
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    tipo ENUM('rutina_asignada', 'plan_actualizado', 'mensaje', 'pedido', 'objetivo_completado', 'recordatorio', 'sistema') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    icono VARCHAR(50) DEFAULT '🔔',
    url VARCHAR(255) DEFAULT NULL,
    leida BOOLEAN DEFAULT 0,
    importante BOOLEAN DEFAULT 0,
    remitente_id INT UNSIGNED DEFAULT NULL,
    datos_adicionales JSON DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_leida TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_leida (usuario_id, leida),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notificaciones_preferencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL UNIQUE,
    rutinas_asignadas BOOLEAN DEFAULT 1,
    planes_actualizados BOOLEAN DEFAULT 1,
    mensajes BOOLEAN DEFAULT 1,
    pedidos BOOLEAN DEFAULT 1,
    objetivos BOOLEAN DEFAULT 1,
    recordatorios BOOLEAN DEFAULT 1,
    notificaciones_sistema BOOLEAN DEFAULT 1,
    notificaciones_email BOOLEAN DEFAULT 0,
    sonido BOOLEAN DEFAULT 1,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notificaciones de prueba (ajusta el usuario_id según tu BD)
INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, icono, importante) VALUES
(2, 'rutina_asignada', 'Nueva Rutina Asignada', 'Tu instructor te ha asignado la rutina: Fuerza Full Body', '💪', 1),
(2, 'pedido', 'Pedido Confirmado', 'Tu pedido ha sido confirmado. Total: $1,299.00', '📦', 0),
(2, 'objetivo_completado', '¡Objetivo Alcanzado!', 'Has completado tu objetivo de reducir 5kg. ¡Felicidades!', '🎯', 1),
(2, 'plan_actualizado', 'Plan Actualizado', 'Tu nutriólogo ha actualizado tu plan de alimentación', '🥗', 1),
(2, 'sistema', 'Bienvenido a FitAndFuel', 'Completa tu perfil para mejores recomendaciones', '🔔', 0)
ON DUPLICATE KEY UPDATE titulo = titulo;

SELECT '✓ Todos los sistemas instalados correctamente' AS Resultado;
SELECT 'Sistemas activos: Cupones, Progreso, Notificaciones' AS Info;
SELECT COUNT(*) as Cupones_Activos FROM cupones WHERE activo = 1;
SELECT COUNT(*) as Notificaciones_Test FROM notificaciones WHERE usuario_id = 2;
