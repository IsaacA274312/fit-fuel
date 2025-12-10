-- Tabla para ejercicios con animaciones
USE fitandfuel;

-- La tabla ejercicios ya existe en fit-fuel.sql, solo insertar datos
-- Insertar ejercicios populares con GIFs de ejemplo
INSERT INTO ejercicios (nombre, descripcion, grupo_muscular, tipo, video_url) VALUES
-- Piernas
('Sentadilla Libre', 'Sentadilla con barra en la espalda. 1. Coloca la barra sobre los trapecios 2. Pies al ancho de hombros 3. Desciende hasta que muslos estén paralelos 4. Empuja con talones para subir', 'Piernas', 'fuerza', 
 'https://media.giphy.com/media/1qWOrkDSk8fSus4b9K/giphy.gif'),

('Peso Muerto', 'Levantamiento desde el suelo. 1. Barra sobre los pies 2. Espalda recta, pecho arriba 3. Agarre firme 4. Extiende caderas y rodillas simultáneamente', 'Espalda', 'fuerza',
 'https://media.giphy.com/media/ZXlW0KDxPmRvq/giphy.gif'),

('Zancadas', 'Estocadas alternadas. 1. Da un paso amplio hacia adelante 2. Baja hasta que ambas rodillas formen 90° 3. Empuja con el talón delantero 4. Alterna las piernas', 'Piernas', 'fuerza',
 'https://media.giphy.com/media/28iXAGinCi5TF77PtG/giphy.gif'),

-- Pecho
('Press de Banca', 'Press horizontal con barra. 1. Acostado en banco plano 2. Barra a la altura del pecho 3. Baja controladamente 4. Empuja explosivamente', 'Pecho', 'fuerza',
 'https://media.giphy.com/media/3oKIPpFh0vUjMhr542/giphy.gif'),

('Flexiones', 'Push-ups clásicos. 1. Cuerpo en línea recta 2. Manos al ancho de hombros 3. Baja hasta casi tocar el suelo 4. Empuja hasta extensión completa', 'Pecho', 'fuerza',
 'https://media.giphy.com/media/ZcUJt2NnyYuXl0u7qh/giphy.gif'),

-- Espalda
('Dominadas', 'Pull-ups en barra fija. 1. Agarre amplio en barra 2. Cuerpo colgando 3. Tira hasta que barbilla supere la barra 4. Baja controladamente', 'Espalda', 'fuerza',
 'https://media.giphy.com/media/ZXlW0KDxPmRvq/giphy.gif'),

('Remo con Barra', 'Remo inclinado. 1. Torso inclinado 45° 2. Barra colgando 3. Tira hacia el abdomen 4. Contrae omóplatos', 'Espalda', 'fuerza',
 'https://media.giphy.com/media/1qWOrkDSk8fSus4b9K/giphy.gif'),

-- Brazos
('Curl de Bíceps', 'Curl con barra. 1. Codos pegados al cuerpo 2. Barra al frente 3. Flexiona los codos 4. Baja controladamente', 'Brazos', 'fuerza',
 'https://media.giphy.com/media/fvT2lZ7UFAvHpPjmVe/giphy.gif'),

('Press Francés', 'Extensión de tríceps acostado. 1. Acostado, brazos extendidos 2. Baja la barra hacia la frente 3. Extiende los codos 4. Mantén codos fijos', 'Brazos', 'fuerza',
 'https://media.giphy.com/media/1qWOrkDSk8fSus4b9K/giphy.gif'),

('Fondos en Paralelas', 'Dips para tríceps. 1. Cuerpo suspendido en paralelas 2. Inclínate ligeramente hacia adelante 3. Baja flexionando codos 4. Empuja hasta extensión', 'Brazos', 'fuerza',
 'https://media.giphy.com/media/ZXlW0KDxPmRvq/giphy.gif'),

-- Hombros
('Press Militar', 'Press de hombros de pie. 1. Barra a altura de hombros 2. Pies firmes 3. Empuja barra hacia arriba 4. Baja controladamente', 'Hombros', 'fuerza',
 'https://media.giphy.com/media/3oKIPpFh0vUjMhr542/giphy.gif'),

('Elevaciones Laterales', 'Vuelos laterales con mancuernas. 1. Mancuernas a los lados 2. Ligera flexión de codos 3. Eleva hasta altura de hombros 4. Baja lentamente', 'Hombros', 'fuerza',
 'https://media.giphy.com/media/28iXAGinCi5TF77PtG/giphy.gif'),

-- Core
('Plancha', 'Plank isométrico. 1. Posición de flexión sobre antebrazos 2. Cuerpo en línea recta 3. Contrae abdomen 4. Mantén la posición', 'Core', 'fuerza',
 'https://media.giphy.com/media/fvT2lZ7UFAvHpPjmVe/giphy.gif'),

('Abdominales Bicicleta', 'Crunch con rotación. 1. Acostado boca arriba 2. Manos detrás de la cabeza 3. Lleva codo a rodilla opuesta 4. Alterna lados', 'Core', 'fuerza',
 'https://media.giphy.com/media/ZcUJt2NnyYuXl0u7qh/giphy.gif'),

-- Cardio
('Burpees', 'Ejercicio de cuerpo completo. 1. Desde de pie, baja a plancha 2. Haz una flexión 3. Lleva pies hacia las manos 4. Salta con brazos arriba', 'Full Body', 'cardio',
 'https://media.giphy.com/media/1qWOrkDSk8fSus4b9K/giphy.gif'),

('Mountain Climbers', 'Escaladores. 1. Posición de plancha 2. Lleva rodilla al pecho 3. Alterna rápidamente 4. Mantén core activo', 'Core', 'cardio',
 'https://media.giphy.com/media/ZXlW0KDxPmRvq/giphy.gif');

-- Ejemplo de rutina con ejercicios asignados
-- (Requiere que exista una rutina con id=1)
INSERT INTO rutina_ejercicios (rutina_id, ejercicio_id, orden, series, repeticiones, descanso_seg, notas) 
SELECT 1, id, 
    CASE 
        WHEN nombre = 'Sentadilla Libre' THEN 1
        WHEN nombre = 'Press de Banca' THEN 2
        WHEN nombre = 'Remo con Barra' THEN 3
        WHEN nombre = 'Press Militar' THEN 4
        WHEN nombre = 'Curl de Bíceps' THEN 5
    END as orden,
    CASE 
        WHEN nombre IN ('Sentadilla Libre', 'Press de Banca') THEN 4
        ELSE 3
    END as series,
    CASE 
        WHEN nombre IN ('Sentadilla Libre', 'Press de Banca') THEN '5-8'
        ELSE '8-12'
    END as repeticiones,
    90 as descanso_seg,
    'Ejercicio fundamental' as notas
FROM ejercicios 
WHERE nombre IN ('Sentadilla Libre', 'Press de Banca', 'Remo con Barra', 'Press Militar', 'Curl de Bíceps')
AND EXISTS (SELECT 1 FROM rutinas WHERE id = 1)
LIMIT 5;
