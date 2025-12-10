<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $usuarioId = $data['usuario_id']; // ID del destinatario
    $tipo = $data['tipo'];
    $titulo = $data['titulo'];
    $mensaje = $data['mensaje'];
    $icono = $data['icono'] ?? '🔔';
    $url = $data['url'] ?? null;
    $importante = $data['importante'] ?? false;
    $remitenteId = $_SESSION['user_id'];
    $datosAdicionales = isset($data['datos_adicionales']) ? json_encode($data['datos_adicionales']) : null;
    
    // Verificar preferencias del usuario
    $stmt = $db->prepare("SELECT * FROM notificaciones_preferencias WHERE usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $usuarioId]);
    $preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si no tiene preferencias, crear con valores por defecto (todo activado)
    if (!$preferencias) {
        $stmt = $db->prepare("INSERT INTO notificaciones_preferencias (usuario_id) VALUES (:usuario_id)");
        $stmt->execute([':usuario_id' => $usuarioId]);
        $preferencias = [
            'rutinas_asignadas' => 1,
            'planes_actualizados' => 1,
            'mensajes' => 1,
            'pedidos' => 1,
            'objetivos' => 1,
            'recordatorios' => 1,
            'notificaciones_sistema' => 1
        ];
    }
    
    // Verificar si el usuario quiere este tipo de notificación
    $tipoColumna = [
        'rutina_asignada' => 'rutinas_asignadas',
        'plan_actualizado' => 'planes_actualizados',
        'mensaje' => 'mensajes',
        'pedido' => 'pedidos',
        'objetivo_completado' => 'objetivos',
        'recordatorio' => 'recordatorios',
        'sistema' => 'notificaciones_sistema'
    ];
    
    $columna = $tipoColumna[$tipo] ?? 'notificaciones_sistema';
    
    if (!$preferencias[$columna]) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario ha desactivado este tipo de notificaciones',
            'enviada' => false
        ]);
        exit;
    }
    
    // Crear notificación
    $stmt = $db->prepare("
        INSERT INTO notificaciones (
            usuario_id, tipo, titulo, mensaje, icono, url, importante, remitente_id, datos_adicionales
        ) VALUES (
            :usuario_id, :tipo, :titulo, :mensaje, :icono, :url, :importante, :remitente_id, :datos_adicionales
        )
    ");
    
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':tipo' => $tipo,
        ':titulo' => $titulo,
        ':mensaje' => $mensaje,
        ':icono' => $icono,
        ':url' => $url,
        ':importante' => $importante ? 1 : 0,
        ':remitente_id' => $remitenteId,
        ':datos_adicionales' => $datosAdicionales
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Notificación enviada',
        'notificacion_id' => $db->lastInsertId(),
        'enviada' => true
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al crear notificación: ' . $e->getMessage()]);
}
