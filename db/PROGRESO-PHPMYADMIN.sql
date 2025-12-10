-- ============================================
-- SISTEMA DE PROGRESO Y MÉTRICAS - EJECUTAR EN PHPMYADMIN
-- ============================================

-- Tabla de registros de progreso
CREATE TABLE IF NOT EXISTS progreso_usuario (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_registro DATE NOT NULL,
    peso DECIMAL(5,2) DEFAULT NULL COMMENT 'Peso en kg',
    altura DECIMAL(5,2) DEFAULT NULL COMMENT 'Altura en cm',
    pecho DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia pecho en cm',
    cintura DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia cintura en cm',
    cadera DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia cadera en cm',
    brazo_derecho DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia brazo derecho en cm',
    brazo_izquierdo DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia brazo izquierdo en cm',
    pierna_derecha DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia pierna derecha en cm',
    pierna_izquierda DECIMAL(5,2) DEFAULT NULL COMMENT 'Circunferencia pierna izquierda en cm',
    porcentaje_grasa DECIMAL(4,2) DEFAULT NULL COMMENT 'Porcentaje de grasa corporal',
    masa_muscular DECIMAL(5,2) DEFAULT NULL COMMENT 'Masa muscular en kg',
    imc DECIMAL(4,2) DEFAULT NULL COMMENT 'Índice de masa corporal (calculado)',
    notas TEXT DEFAULT NULL COMMENT 'Notas adicionales del registro',
    foto_frontal VARCHAR(255) DEFAULT NULL,
    foto_lateral VARCHAR(255) DEFAULT NULL,
    foto_trasera VARCHAR(255) DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_registro),
    INDEX idx_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de objetivos del usuario
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

SELECT 'Sistema de progreso y métricas creado correctamente' AS Resultado;
SELECT 'Ahora puedes registrar tu progreso desde el dashboard' AS Info;
