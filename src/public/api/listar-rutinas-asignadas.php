<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Solo instructores pueden ver sus asignaciones']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $instructorId = $_SESSION['user_id'];
    
    // Obtener rutinas asignadas por este instructor
    $stmt = $db->prepare("
        SELECT 
            ur.id as asignacion_id,
            ur.fecha_asignacion,
            ur.activo,
            u.id as cliente_id,
            u.nombre as cliente_nombre,
            u.apellido as cliente_apellido,
            u.email as cliente_email,
            r.id as rutina_id,
            r.nombre as rutina_nombre,
            r.descripcion as rutina_descripcion,
            r.nivel,
            r.duracion_semanas
        FROM usuario_rutinas ur
        INNER JOIN usuarios u ON ur.usuario_id = u.id
        INNER JOIN rutinas r ON ur.rutina_id = r.id
        WHERE r.instructor_id = :instructor_id
        ORDER BY ur.fecha_asignacion DESC
    ");
    
    $stmt->execute([':instructor_id' => $instructorId]);
    $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'asignaciones' => $asignaciones
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener asignaciones: ' . $e->getMessage()]);
}
