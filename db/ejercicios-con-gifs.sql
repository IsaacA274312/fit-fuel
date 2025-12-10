-- Agregar columna gif_url a ejercicios
ALTER TABLE ejercicios ADD COLUMN gif_url VARCHAR(255) AFTER video_url;

-- Insertar ejercicios con GIFs de ejemplo (URLs públicas de GIFs de ejercicios)
INSERT INTO ejercicios (nombre, descripcion, grupo_muscular, tipo, equipo_requerido, gif_url) VALUES
('Press de Banca', 'Ejercicio fundamental para pecho, también trabaja tríceps y hombros', 'Pecho', 'fuerza', 'Barra, Banco', 'https://media.tenor.com/HqKxO8NB_cMAAAAM/bench-press-workout.gif'),
('Sentadilla con Barra', 'Ejercicio compuesto que trabaja piernas y glúteos', 'Piernas', 'fuerza', 'Barra, Rack', 'https://media.tenor.com/kpvELMjCbzsAAAAM/squat-workout.gif'),
('Peso Muerto', 'Ejercicio de cadena posterior que trabaja espalda baja, glúteos y femorales', 'Espalda', 'fuerza', 'Barra', 'https://media.tenor.com/tBx6Qa_EeLMAAAAM/deadlift-workout.gif'),
('Dominadas', 'Ejercicio de peso corporal para dorsales y bíceps', 'Espalda', 'fuerza', 'Barra de dominadas', 'https://media.tenor.com/4X_xZBqE3LQAAAAM/pull-ups-workout.gif'),
('Press Militar', 'Ejercicio para hombros con barra', 'Hombros', 'fuerza', 'Barra', 'https://media.tenor.com/YjGxKqvVu_sAAAAM/overhead-press-shoulder-press.gif'),
('Curl de Bíceps', 'Ejercicio de aislamiento para bíceps', 'Brazos', 'fuerza', 'Mancuernas o Barra', 'https://media.tenor.com/XBvkPzQnXWEAAAAM/bicep-curl-workout.gif'),
('Press Francés', 'Ejercicio para tríceps con barra o mancuernas', 'Brazos', 'fuerza', 'Barra EZ o Mancuernas', 'https://media.tenor.com/9UhxkqBkbmwAAAAM/skull-crusher-triceps.gif'),
('Fondos en Paralelas', 'Ejercicio compuesto para pecho y tríceps', 'Pecho', 'fuerza', 'Paralelas', 'https://media.tenor.com/LQxYjQr0JRQAAAAM/dips-workout.gif'),
('Remo con Barra', 'Ejercicio para dorsales y trapecio medio', 'Espalda', 'fuerza', 'Barra', 'https://media.tenor.com/xPqvLd3Xk6QAAAAM/barbell-row-workout.gif'),
('Zancadas', 'Ejercicio unilateral para piernas y glúteos', 'Piernas', 'fuerza', 'Mancuernas', 'https://media.tenor.com/C4kJhQ0VTVEAAAAM/lunges-workout.gif'),
('Plancha Abdominal', 'Ejercicio isométrico para core', 'Abdomen', 'fuerza', 'Ninguno', 'https://media.tenor.com/xg4fZPqaXd8AAAAM/plank-workout.gif'),
('Burpees', 'Ejercicio cardio de cuerpo completo', 'Cardio', 'cardio', 'Ninguno', 'https://media.tenor.com/Hqm7XhKUfPwAAAAM/burpees-workout.gif'),
('Mountain Climbers', 'Ejercicio cardio que trabaja core y resistencia', 'Abdomen', 'cardio', 'Ninguno', 'https://media.tenor.com/YkEoPYCYkU8AAAAM/mountain-climbers-workout.gif'),
('Jumping Jacks', 'Ejercicio cardiovascular básico', 'Cardio', 'cardio', 'Ninguno', 'https://media.tenor.com/0eSlqPyXaIAAAAAM/jumping-jacks-workout.gif'),
('Flexiones de Pecho', 'Ejercicio de peso corporal para pecho, hombros y tríceps', 'Pecho', 'fuerza', 'Ninguno', 'https://media.tenor.com/KGLNqkCN3vMAAAAM/push-ups-workout.gif'),
('Elevaciones Laterales', 'Ejercicio de aislamiento para hombros', 'Hombros', 'fuerza', 'Mancuernas', 'https://media.tenor.com/UfTp9Kv9mRgAAAAM/lateral-raise-shoulder.gif'),
('Hip Thrust', 'Ejercicio para glúteos y femorales', 'Glúteos', 'fuerza', 'Barra, Banco', 'https://media.tenor.com/YWnPqMsNE3UAAAAM/hip-thrust-workout.gif'),
('Crunch Abdominal', 'Ejercicio básico para abdominales superiores', 'Abdomen', 'fuerza', 'Ninguno', 'https://media.tenor.com/RKhCBjsxdwEAAAAM/crunches-abs.gif'),
('Russian Twist', 'Ejercicio para oblicuos', 'Abdomen', 'fuerza', 'Mancuerna o Disco', 'https://media.tenor.com/vJBqR_GFhsEAAAAM/russian-twist-abs.gif'),
('Box Jump', 'Ejercicio pliométrico para potencia de piernas', 'Piernas', 'cardio', 'Cajón pliométrico', 'https://media.tenor.com/kDq9A5y8M9cAAAAM/box-jump-workout.gif');

-- Resultado
SELECT '✅ Ejercicios con GIFs creados correctamente' AS Resultado;
SELECT COUNT(*) AS Total_Ejercicios FROM ejercicios;
