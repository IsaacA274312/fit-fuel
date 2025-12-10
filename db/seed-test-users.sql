-- Script rápido para crear un usuario de prueba
-- Ejecuta esto después de importar fit-fuel.sql

USE fitandfuel;

-- Insertar usuario de prueba (admin)
-- Contraseña: Admin123!
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    fecha_nacimiento, 
    genero, 
    tipo_usuario,
    creado_en
) VALUES (
    'Admin',
    'Test Usuario',
    'admin@fitandfuel.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- Admin123!
    '+34 600 000 000',
    '1990-01-01',
    'masculino',
    'admin',
    NOW()
);

-- Insertar usuario regular de prueba
-- Contraseña: User1234!
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    fecha_nacimiento, 
    genero, 
    tipo_usuario,
    creado_en
) VALUES (
    'Usuario',
    'De Prueba',
    'usuario@fitandfuel.com',
    '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yU3/VKc8z6',  -- User1234!
    '+34 600 111 222',
    '1995-06-15',
    'femenino',
    'usuario',
    NOW()
);

-- Insertar instructor de prueba
-- Contraseña: Instructor1!
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    fecha_nacimiento, 
    genero, 
    tipo_usuario,
    creado_en
) VALUES (
    'Carlos',
    'Martínez López',
    'instructor@fitandfuel.com',
    '$2y$10$QZz8RP5hF5C0yXQqKr3oBuU7J5X8qy9xzE8eVgx3KJ8FxJ8cRrQHi',  -- Instructor1!
    '+34 600 222 333',
    '1988-03-20',
    'masculino',
    'instructor',
    NOW()
);

-- Insertar nutriólogo de prueba
-- Contraseña: Nutri1234!
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    fecha_nacimiento, 
    genero, 
    tipo_usuario,
    creado_en
) VALUES (
    'Ana',
    'López García',
    'nutriologo@fitandfuel.com',
    '$2y$10$vKp1qQ8hF5C0yXQqKr3oBuU7J5X8qy9xzE8eVgx3KJ8FxJ8cRrQHi',  -- Nutri1234!
    '+34 600 333 444',
    '1992-09-10',
    'femenino',
    'nutriologo',
    NOW()
);

-- Verificar usuarios creados
SELECT 
    id,
    nombre,
    apellido,
    email,
    tipo_usuario,
    DATE_FORMAT(creado_en, '%Y-%m-%d %H:%i:%s') as creado
FROM usuarios
ORDER BY id DESC;

-- Mostrar credenciales de prueba
SELECT '==== USUARIOS DE PRUEBA ====' as info;
SELECT 'Email: admin@fitandfuel.com | Password: Admin123!' as admin;
SELECT 'Email: usuario@fitandfuel.com | Password: User1234!' as usuario;
SELECT 'Email: instructor@fitandfuel.com | Password: Instructor1!' as instructor;
SELECT 'Email: nutriologo@fitandfuel.com | Password: Nutri1234!' as nutriologo;
