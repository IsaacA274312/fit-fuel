<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario sea instructor
if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $instructorId = $_SESSION['user_id'];

    // Obtener estadísticas del instructor
    $stats = [
        'clientes' => 0,
        'rutinas' => 0,
        'clases' => 0,
        'asistencias' => 0
    ];

    // Contar clientes únicos asignados
    $stmtClientes = $db->prepare("
        SELECT COUNT(DISTINCT usuario_id) as total
        FROM usuario_instructor
        WHERE instructor_id = :instructor_id AND activo = 1
    ");
    $stmtClientes->execute([':instructor_id' => $instructorId]);
    $resultClientes = $stmtClientes->fetch(PDO::FETCH_ASSOC);
    $stats['clientes'] = (int)$resultClientes['total'];

    // Contar rutinas creadas
    $stmtRutinas = $db->prepare("
        SELECT COUNT(*) as total
        FROM rutinas
        WHERE instructor_id = :instructor_id
    ");
    $stmtRutinas->execute([':instructor_id' => $instructorId]);
    $resultRutinas = $stmtRutinas->fetch(PDO::FETCH_ASSOC);
    $stats['rutinas'] = (int)$resultRutinas['total'];

    // Contar clases programadas este mes
    $stmtClases = $db->prepare("
        SELECT COUNT(*) as total
        FROM clases_programadas
        WHERE instructor_id = :instructor_id
        AND MONTH(fecha_clase) = MONTH(CURDATE())
        AND YEAR(fecha_clase) = YEAR(CURDATE())
    ");
    $stmtClases->execute([':instructor_id' => $instructorId]);
    $resultClases = $stmtClases->fetch(PDO::FETCH_ASSOC);
    $stats['clases'] = (int)$resultClases['total'];

    // Contar asistencias este mes
    $stmtAsistencias = $db->prepare("
        SELECT COUNT(*) as total
        FROM clases_programadas
        WHERE instructor_id = :instructor_id
        AND asistio = 1
        AND MONTH(fecha_clase) = MONTH(CURDATE())
        AND YEAR(fecha_clase) = YEAR(CURDATE())
    ");
    $stmtAsistencias->execute([':instructor_id' => $instructorId]);
    $resultAsistencias = $stmtAsistencias->fetch(PDO::FETCH_ASSOC);
    $stats['asistencias'] = (int)$resultAsistencias['total'];

    // Obtener lista de clientes
    $stmtListaClientes = $db->prepare("
        SELECT DISTINCT
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            ui.fecha_asignacion,
            COUNT(DISTINCT cp.id) as clases_totales,
            SUM(CASE WHEN cp.asistio = 1 THEN 1 ELSE 0 END) as asistencias
        FROM usuario_instructor ui
        INNER JOIN usuarios u ON ui.usuario_id = u.id
        LEFT JOIN clases_programadas cp ON u.id = cp.usuario_id AND cp.instructor_id = :instructor_id
        WHERE ui.instructor_id = :instructor_id AND ui.activo = 1
        GROUP BY u.id, u.nombre, u.apellido, u.email, ui.fecha_asignacion
        ORDER BY ui.fecha_asignacion DESC
    ");
    $stmtListaClientes->execute([':instructor_id' => $instructorId]);
    $clientes = $stmtListaClientes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener clases programadas próximas
    $stmtProximas = $db->prepare("
        SELECT 
            cp.id,
            cp.fecha_clase,
            cp.hora_inicio,
            cp.nombre_clase,
            cp.sala,
            u.nombre as cliente_nombre,
            u.apellido as cliente_apellido
        FROM clases_programadas cp
        INNER JOIN usuarios u ON cp.usuario_id = u.id
        WHERE cp.instructor_id = :instructor_id
        AND cp.fecha_clase >= CURDATE()
        ORDER BY cp.fecha_clase ASC, cp.hora_inicio ASC
        LIMIT 10
    ");
    $stmtProximas->execute([':instructor_id' => $instructorId]);
    $programacion = $stmtProximas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener rutinas del instructor
    $stmtRutinasList = $db->prepare("
        SELECT 
            r.id,
            r.nombre,
            r.descripcion,
            r.nivel,
            r.duracion_semanas,
            r.creado_en,
            COUNT(DISTINCT ur.usuario_id) as usuarios_asignados
        FROM rutinas r
        LEFT JOIN usuario_rutinas ur ON r.id = ur.rutina_id
        WHERE r.instructor_id = :instructor_id
        GROUP BY r.id
        ORDER BY r.creado_en DESC
    ");
    $stmtRutinasList->execute([':instructor_id' => $instructorId]);
    $rutinas = $stmtRutinasList->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'clientes' => $clientes,
        'programacion' => $programacion,
        'rutinas' => $rutinas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
