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
    
    // Total de clientes activos
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM usuario_instructor
        WHERE instructor_id = :instructor_id AND activo = 1
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $totalClientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de rutinas creadas
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM rutinas
        WHERE instructor_id = :instructor_id
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $totalRutinas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de rutinas asignadas activas
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM usuario_rutinas ur
        INNER JOIN rutinas r ON ur.rutina_id = r.id
        WHERE r.instructor_id = :instructor_id AND ur.activo = 1
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $rutinasActivas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Clientes activos en los últimos 7 días
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT pu.usuario_id) as total
        FROM progreso_usuario pu
        INNER JOIN usuario_instructor ui ON pu.usuario_id = ui.usuario_id
        WHERE ui.instructor_id = :instructor_id
        AND pu.fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $clientesActivos7d = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Rutinas más asignadas
    $stmt = $db->prepare("
        SELECT 
            r.nombre,
            r.nivel,
            COUNT(ur.id) as total_asignaciones,
            COUNT(CASE WHEN ur.activo = 1 THEN 1 END) as activas
        FROM rutinas r
        LEFT JOIN usuario_rutinas ur ON r.id = ur.rutina_id
        WHERE r.instructor_id = :instructor_id
        GROUP BY r.id, r.nombre, r.nivel
        ORDER BY total_asignaciones DESC
        LIMIT 5
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $rutinasMasAsignadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Últimos clientes asignados
    $stmt = $db->prepare("
        SELECT 
            u.nombre,
            u.apellido,
            ui.fecha_asignacion
        FROM usuario_instructor ui
        INNER JOIN usuarios u ON ui.usuario_id = u.id
        WHERE ui.instructor_id = :instructor_id
        ORDER BY ui.fecha_asignacion DESC
        LIMIT 5
    ");
    $stmt->execute([':instructor_id' => $instructorId]);
    $ultimosClientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'estadisticas' => [
            'total_clientes' => $totalClientes,
            'total_rutinas' => $totalRutinas,
            'rutinas_activas' => $rutinasActivas,
            'clientes_activos_7d' => $clientesActivos7d,
            'rutinas_mas_asignadas' => $rutinasMasAsignadas,
            'ultimos_clientes' => $ultimosClientes
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
