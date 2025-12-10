-- Crear tabla para asignación de rutinas a usuarios
CREATE TABLE IF NOT EXISTS usuario_rutinas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    rutina_id INT UNSIGNED NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_asignacion (usuario_id, rutina_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resultado
SELECT '✅ Tabla usuario_rutinas creada correctamente' AS Resultado;
