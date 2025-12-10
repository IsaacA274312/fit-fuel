-- ============================================
-- DATOS DE PRUEBA PARA NOTIFICACIONES
-- Ejecutar después de crear las tablas
-- ============================================

-- Primero ejecuta NOTIFICACIONES-PHPMYADMIN.sql
-- Luego ejecuta este archivo para tener notificaciones de ejemplo

-- Insertar notificaciones de prueba para el usuario con ID 2
-- (Ajusta el usuario_id según tu base de datos)

INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, icono, importante, remitente_id) VALUES
(2, 'rutina_asignada', 'Nueva Rutina Asignada', 'Tu instructor te ha asignado la rutina: Fuerza Full Body', '💪', 1, 3),
(2, 'pedido', 'Pedido Confirmado', 'Tu pedido #1001 ha sido confirmado. Total: $1,299.00', '📦', 0, NULL),
(2, 'objetivo_completado', '¡Objetivo Alcanzado!', 'Has completado tu objetivo de reducir 5kg. ¡Felicidades!', '🎯', 1, NULL),
(2, 'plan_actualizado', 'Plan Alimenticio Actualizado', 'Tu nutriólogo ha actualizado tu plan de alimentación', '🥗', 1, 4),
(2, 'recordatorio', 'Recordatorio de Entrenamiento', 'Tienes una sesión programada para hoy a las 18:00', '⏰', 0, NULL),
(2, 'mensaje', 'Nuevo Mensaje', 'Tu instructor te ha enviado un mensaje sobre tu progreso', '💬', 0, 3),
(2, 'sistema', 'Bienvenido a FitAndFuel', 'Completa tu perfil para obtener mejores recomendaciones', '🔔', 0, NULL);

-- Insertar preferencias por defecto para el usuario
INSERT INTO notificaciones_preferencias (usuario_id) VALUES (2)
ON DUPLICATE KEY UPDATE usuario_id = usuario_id;

SELECT 'Notificaciones de prueba creadas' AS Resultado;
SELECT COUNT(*) as Total_Notificaciones FROM notificaciones WHERE usuario_id = 2;
SELECT COUNT(*) as No_Leidas FROM notificaciones WHERE usuario_id = 2 AND leida = 0;
