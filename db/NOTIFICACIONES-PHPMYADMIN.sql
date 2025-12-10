-- ============================================
-- SISTEMA DE NOTIFICACIONES - EJECUTAR EN PHPMYADMIN
-- ============================================

CREATE TABLE IF NOT EXISTS notificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    tipo ENUM('rutina_asignada', 'plan_actualizado', 'mensaje', 'pedido', 'objetivo_completado', 'recordatorio', 'sistema') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    icono VARCHAR(50) DEFAULT '🔔',
    url VARCHAR(255) DEFAULT NULL COMMENT 'URL a la que redirige al hacer clic',
    leida BOOLEAN DEFAULT 0,
    importante BOOLEAN DEFAULT 0,
    remitente_id INT UNSIGNED DEFAULT NULL COMMENT 'ID del usuario que generó la notificación',
    datos_adicionales JSON DEFAULT NULL COMMENT 'Datos extras en formato JSON',
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

SELECT 'Sistema de notificaciones creado correctamente' AS Resultado;
SELECT 'Las notificaciones se actualizarán automáticamente cada 30 segundos' AS Info;
