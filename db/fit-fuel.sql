
CREATE DATABASE IF NOT EXISTS fitandfuel
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
USE fitandfuel;

-- Eliminar tablas si existen (orden inverso a dependencias)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_pedidos;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
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
SET FOREIGN_KEY_CHECKS = 1;

-- Tabla principal de usuarios (miembros, instructores, nutriólogos, admin)
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(30),
    fecha_nacimiento DATE,
    genero VARCHAR(50) DEFAULT NULL,
    tipo_usuario VARCHAR(50) NOT NULL DEFAULT 'usuario',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (tipo_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías de productos
CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos (tienda)
CREATE TABLE productos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT UNSIGNED DEFAULT 0,
    categoria_id INT UNSIGNED,
    imagen_url VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    INDEX (categoria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes
CREATE TABLE clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(30),
    direccion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX (estado),
    INDEX (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de pedidos
CREATE TABLE detalle_pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    producto_id INT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Perfiles profesionales para instructores y nutriólogos
CREATE TABLE perfiles_profesionales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    titulo VARCHAR(150),
    certificaciones TEXT,
    especialidades VARCHAR(255),
    bio TEXT,
    experiencia_anios TINYINT UNSIGNED,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membresías y relación con usuarios
CREATE TABLE membresias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duracion_dias INT UNSIGNED NOT NULL DEFAULT 30,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuario_membresia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    membresia_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('activa','expirada','cancelada') DEFAULT 'activa',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (membresia_id) REFERENCES membresias(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pagos
CREATE TABLE pagos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED,
    monto DECIMAL(10,2) NOT NULL,
    metodo_pago VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referencia VARCHAR(255),
    estado ENUM('pendiente','completado','fallido','reembolsado') DEFAULT 'completado',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rutinas y ejercicios
CREATE TABLE rutinas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    nivel ENUM('principiante','intermedio','avanzado') DEFAULT 'principiante',
    duracion_semanas TINYINT UNSIGNED DEFAULT 4,
    instructor_id INT UNSIGNED, -- referencia a usuarios con tipo_usuario = instructor
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ejercicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    grupo_muscular VARCHAR(100),
    tipo ENUM('fuerza','cardio','flexibilidad','movilidad') DEFAULT 'fuerza',
    equipo_requerido VARCHAR(100),
    video_url VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rutina_ejercicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT UNSIGNED NOT NULL,
    ejercicio_id INT UNSIGNED NOT NULL,
    orden SMALLINT UNSIGNED DEFAULT 1,
    series SMALLINT UNSIGNED DEFAULT 3,
    repeticiones VARCHAR(50) DEFAULT '8-12',
    descanso_seg SMALLINT UNSIGNED DEFAULT 60,
    peso_recomendado VARCHAR(50),
    notas TEXT,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
    FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_rutina_ejercicio (rutina_id, ejercicio_id, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seguimiento de rutinas de un usuario
CREATE TABLE seguimiento_rutina (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    rutina_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    progreso JSON, -- ej. {"semana1": "completada", "notas": "..."}
    completada BOOLEAN DEFAULT FALSE,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Planes alimenticios y comidas
CREATE TABLE planes_alimenticios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    objetivo ENUM('perdida_peso','ganancia_masa','mantenimiento','salud') DEFAULT 'mantenimiento',
    nutriologo_id INT UNSIGNED, -- referencia a usuarios nutriologo
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nutriologo_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comidas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    tipo_comida ENUM('desayuno','almuerzo','cena','snack') DEFAULT 'almuerzo',
    orden SMALLINT UNSIGNED DEFAULT 1,
    calorias INT UNSIGNED,
    macros JSON, -- { "proteinas":30, "carbohidratos":50, "grasas":20 }
    receta TEXT,
    FOREIGN KEY (plan_id) REFERENCES planes_alimenticios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suplementos y recomendaciones
CREATE TABLE suplementos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(100),
    descripcion TEXT,
    dosis_recomendada VARCHAR(100),
    precio DECIMAL(10,2),
    stock INT UNSIGNED DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE suplemento_recomendaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    suplemento_id INT UNSIGNED NOT NULL,
    recomendado_por INT UNSIGNED, -- usuario (nutriólogo o instructor)
    fecha_inicio DATE,
    fecha_fin DATE,
    instrucciones TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (suplemento_id) REFERENCES suplementos(id) ON DELETE CASCADE,
    FOREIGN KEY (recomendado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Citas (con instructores o nutriólogos)
CREATE TABLE citas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    profesional_id INT UNSIGNED NOT NULL,
    rol_profesional VARCHAR(50) NOT NULL,
    fecha_hora DATETIME NOT NULL,
    duracion_minutos SMALLINT UNSIGNED DEFAULT 60,
    estado ENUM('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
    notas TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (profesional_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Evaluaciones y métricas
CREATE TABLE evaluaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    profesional_id INT UNSIGNED, -- quien realizó la evaluación
    fecha DATE NOT NULL,
    peso_kg DECIMAL(5,2),
    altura_cm DECIMAL(5,2),
    imc DECIMAL(5,2),
    porcentaje_grasa DECIMAL(5,2),
    masa_muscular DECIMAL(6,2),
    notas TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (profesional_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices útiles
CREATE INDEX idx_usuario_email ON usuarios(email);
CREATE INDEX idx_rutina_instructor ON rutinas(instructor_id);
CREATE INDEX idx_plan_nutriologo ON planes_alimenticios(nutriologo_id);
CREATE INDEX idx_citas_fecha ON citas(fecha_hora);

-- Ejemplos mínimos de datos (opcional)
INSERT INTO membresias (nombre, descripcion, precio, duracion_dias)
VALUES
    ('Básica', 'Acceso a rutinas generales y registro', 9.99, 30),
    ('Premium', 'Rutinas personalizadas, seguimiento y consultas', 29.99, 30);

-- Fin