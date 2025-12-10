-- Tablas adicionales para funcionalidad de dashboards
-- Ejecutar después de fit-fuel.sql

USE fitandfuel;

-- Tabla para asistencias de usuarios
CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_asistencia DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo_actividad VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_asistencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para clases programadas
CREATE TABLE IF NOT EXISTS clases_programadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    instructor_id INT UNSIGNED,
    fecha_clase DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    nombre_clase VARCHAR(200),
    sala VARCHAR(100),
    asistio TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_fecha (usuario_id, fecha_clase),
    INDEX idx_instructor (instructor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nota: Las tablas rutinas, rutina_ejercicios y usuario_rutinas ya fueron creadas en add-ejercicios-animaciones.sql

-- Tabla para consultas de nutrición
CREATE TABLE IF NOT EXISTS consultas_nutricion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    nutriologo_id INT UNSIGNED NOT NULL,
    fecha_consulta DATE NOT NULL,
    hora_consulta TIME,
    motivo VARCHAR(500),
    notas TEXT,
    peso DECIMAL(5,2),
    imc DECIMAL(5,2),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (nutriologo_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_nutriologo (nutriologo_id),
    INDEX idx_fecha (fecha_consulta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de ejemplo para pruebas
-- Asistencias de ejemplo (requiere usuarios existentes)
INSERT INTO asistencias (usuario_id, fecha_asistencia, tipo_actividad) VALUES
(1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Entrenamiento de fuerza'),
(1, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Cardio'),
(1, DATE_SUB(NOW(), INTERVAL 5 DAY), 'Yoga');

-- Clases programadas de ejemplo
INSERT INTO clases_programadas (usuario_id, instructor_id, fecha_clase, hora_inicio, nombre_clase, sala, asistio) VALUES
(1, NULL, CURDATE(), '17:00:00', 'Entrenamiento de fuerza', 'Sala 2', 0),
(1, NULL, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:30:00', 'Cardio HIIT', 'Sala 1', 0),
(1, NULL, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:00:00', 'Yoga y flexibilidad', 'Sala 3', 0);

-- Rutinas de ejemplo
INSERT INTO rutinas (instructor_id, nombre, descripcion, nivel, duracion_semanas) VALUES
(2, 'Rutina Principiantes', 'Rutina básica para comenzar', 'principiante', 4),
(2, 'Fuerza Intermedia', 'Entrenamiento de fuerza nivel medio', 'intermedio', 8);

-- Consultas de nutrición de ejemplo
INSERT INTO consultas_nutricion (usuario_id, nutriologo_id, fecha_consulta, hora_consulta, motivo) VALUES
(1, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 'Evaluación inicial'),
(1, 3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:00:00', 'Seguimiento mensual');
