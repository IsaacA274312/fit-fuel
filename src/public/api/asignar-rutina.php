<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Solo instructores pueden asignar rutinas']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['rutina_id']) || empty($data['usuario_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }
    
    $instructorId = $_SESSION['user_id'];
    $rutinaId = $data['rutina_id'];
    $usuarioId = $data['usuario_id'];
    
    // Verificar que la rutina pertenece al instructor
    $stmt = $db->prepare("SELECT id FROM rutinas WHERE id = :id AND instructor_id = :instructor_id");
    $stmt->execute([':id' => $rutinaId, ':instructor_id' => $instructorId]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Esta rutina no te pertenece']);
        exit;
    }
    
    // Verificar que el usuario está asignado al instructor
    $stmt = $db->prepare("SELECT id FROM usuario_instructor WHERE usuario_id = :usuario_id AND instructor_id = :instructor_id AND activo = 1");
    $stmt->execute([':usuario_id' => $usuarioId, ':instructor_id' => $instructorId]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Este usuario no es tu cliente']);
        exit;
    }
    
    // Asignar rutina
    $stmt = $db->prepare("
        INSERT INTO usuario_rutinas (usuario_id, rutina_id, activo)
        VALUES (:usuario_id, :rutina_id, 1)
        ON DUPLICATE KEY UPDATE activo = 1, fecha_asignacion = NOW()
    ");
    
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':rutina_id' => $rutinaId
    ]);
    
    // Obtener nombre de la rutina para la notificación
    $stmt = $db->prepare("SELECT nombre FROM rutinas WHERE id = :id");
    $stmt->execute([':id' => $rutinaId]);
    $rutina = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Crear notificación para el usuario
    $stmtNotif = $db->prepare("
        INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, icono, importante, remitente_id)
        VALUES (:usuario_id, 'rutina_asignada', :titulo, :mensaje, '💪', 1, :remitente_id)
    ");
    
    $stmtNotif->execute([
        ':usuario_id' => $usuarioId,
        ':titulo' => 'Nueva Rutina Asignada',
        ':mensaje' => 'Tu instructor te ha asignado la rutina: ' . $rutina['nombre'],
        ':remitente_id' => $instructorId
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Rutina asignada correctamente']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al asignar rutina: ' . $e->getMessage()]);
}
