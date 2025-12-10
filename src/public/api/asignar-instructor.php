<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'usuario') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Solo usuarios pueden asignar instructores']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['instructor_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de instructor requerido']);
        exit;
    }
    
    $usuarioId = $_SESSION['user_id'];
    $instructorId = $data['instructor_id'];
    
    // Verificar que el instructor existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = :id AND tipo_usuario = 'instructor'");
    $stmt->execute([':id' => $instructorId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Instructor no encontrado']);
        exit;
    }
    
    // Insertar o actualizar asignación
    $stmt = $db->prepare("
        INSERT INTO usuario_instructor (usuario_id, instructor_id, activo)
        VALUES (:usuario_id, :instructor_id, 1)
        ON DUPLICATE KEY UPDATE activo = 1, fecha_asignacion = NOW()
    ");
    
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':instructor_id' => $instructorId
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Instructor asignado correctamente']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al asignar instructor: ' . $e->getMessage()]);
}
