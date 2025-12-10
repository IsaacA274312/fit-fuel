<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['asignacion_id']) || !isset($data['activo'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }
    
    $asignacionId = $data['asignacion_id'];
    $activo = $data['activo'];
    $instructorId = $_SESSION['user_id'];
    
    // Verificar que la rutina pertenece al instructor
    $stmt = $db->prepare("
        SELECT ur.id 
        FROM usuario_rutinas ur
        INNER JOIN rutinas r ON ur.rutina_id = r.id
        WHERE ur.id = :asignacion_id AND r.instructor_id = :instructor_id
    ");
    $stmt->execute([':asignacion_id' => $asignacionId, ':instructor_id' => $instructorId]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Esta rutina no te pertenece']);
        exit;
    }
    
    // Actualizar estado
    $stmt = $db->prepare("
        UPDATE usuario_rutinas 
        SET activo = :activo 
        WHERE id = :asignacion_id
    ");
    $stmt->execute([':activo' => $activo, ':asignacion_id' => $asignacionId]);
    
    echo json_encode([
        'success' => true,
        'message' => $activo ? 'Rutina activada' : 'Rutina pausada'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $e->getMessage()]);
}
