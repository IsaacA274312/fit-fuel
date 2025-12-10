<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Solo instructores']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $instructorId = $_SESSION['user_id'];
    
    // Obtener clientes asignados al instructor
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            ui.fecha_asignacion
        FROM usuario_instructor ui
        INNER JOIN usuarios u ON ui.usuario_id = u.id
        WHERE ui.instructor_id = :instructor_id 
        AND ui.activo = 1
        ORDER BY u.nombre
    ");
    
    $stmt->execute([':instructor_id' => $instructorId]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'clientes' => $clientes
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
