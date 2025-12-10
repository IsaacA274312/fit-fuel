<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $instructorId = $_SESSION['user_id'];
    
    // Obtener clientes con estadísticas
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            ui.fecha_asignacion,
            COUNT(DISTINCT ur.id) as rutinas_asignadas,
            COUNT(DISTINCT CASE WHEN ur.activo = 1 THEN ur.id END) as rutinas_activas,
            COUNT(DISTINCT pu.id) as registros_progreso,
            MAX(pu.fecha_registro) as ultima_actividad
        FROM usuario_instructor ui
        INNER JOIN usuarios u ON ui.usuario_id = u.id
        LEFT JOIN usuario_rutinas ur ON u.id = ur.usuario_id
        LEFT JOIN progreso_usuario pu ON u.id = pu.usuario_id
        WHERE ui.instructor_id = :instructor_id
        AND ui.activo = 1
        GROUP BY u.id, u.nombre, u.apellido, u.email, ui.fecha_asignacion
        ORDER BY ui.fecha_asignacion DESC
    ");
    
    $stmt->execute([':instructor_id' => $instructorId]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'total' => count($clientes)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
