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
    
    $notificacionId = $data['notificacion_id'] ?? null;
    $marcarTodas = $data['marcar_todas'] ?? false;
    $usuarioId = $_SESSION['user_id'];
    
    if ($marcarTodas) {
        // Marcar todas como leídas
        $stmt = $db->prepare("
            UPDATE notificaciones 
            SET leida = 1, fecha_leida = NOW() 
            WHERE usuario_id = :usuario_id AND leida = 0
        ");
        $stmt->execute([':usuario_id' => $usuarioId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
            'actualizadas' => $stmt->rowCount()
        ]);
        
    } elseif ($notificacionId) {
        // Marcar una específica como leída
        $stmt = $db->prepare("
            UPDATE notificaciones 
            SET leida = 1, fecha_leida = NOW() 
            WHERE id = :id AND usuario_id = :usuario_id
        ");
        $stmt->execute([
            ':id' => $notificacionId,
            ':usuario_id' => $usuarioId
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Notificación no encontrada'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Parámetros inválidos'
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al marcar notificación: ' . $e->getMessage()]);
}
