<?php
session_start();
header('Content-Type: application/json');

// Verificar autenticación de usuario
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $userId = $_SESSION['user_id'];

    // Obtener estadísticas del usuario
    $stats = [
        'sesiones' => 0,
        'diasActivos' => 0,
        'pendientes' => 0,
        'cumplimiento' => 0
    ];

    // Contar sesiones/asistencias del usuario
    $stmtSesiones = $db->prepare("
        SELECT COUNT(*) as total 
        FROM asistencias 
        WHERE usuario_id = :user_id
    ");
    $stmtSesiones->execute([':user_id' => $userId]);
    $resultSesiones = $stmtSesiones->fetch(PDO::FETCH_ASSOC);
    $stats['sesiones'] = (int)$resultSesiones['total'];

    // Contar días activos (días únicos con asistencia)
    $stmtDias = $db->prepare("
        SELECT COUNT(DISTINCT DATE(fecha_asistencia)) as dias
        FROM asistencias
        WHERE usuario_id = :user_id
    ");
    $stmtDias->execute([':user_id' => $userId]);
    $resultDias = $stmtDias->fetch(PDO::FETCH_ASSOC);
    $stats['diasActivos'] = (int)$resultDias['dias'];

    // Contar entrenamientos pendientes (clases futuras)
    $stmtPendientes = $db->prepare("
        SELECT COUNT(*) as total
        FROM clases_programadas
        WHERE usuario_id = :user_id 
        AND fecha_clase >= CURDATE()
        AND asistio = 0
    ");
    $stmtPendientes->execute([':user_id' => $userId]);
    $resultPendientes = $stmtPendientes->fetch(PDO::FETCH_ASSOC);
    $stats['pendientes'] = (int)$resultPendientes['total'];

    // Calcular cumplimiento (asistencias vs clases programadas en el mes actual)
    $stmtCumplimiento = $db->prepare("
        SELECT 
            COUNT(*) as programadas,
            SUM(CASE WHEN asistio = 1 THEN 1 ELSE 0 END) as asistidas
        FROM clases_programadas
        WHERE usuario_id = :user_id
        AND MONTH(fecha_clase) = MONTH(CURDATE())
        AND YEAR(fecha_clase) = YEAR(CURDATE())
    ");
    $stmtCumplimiento->execute([':user_id' => $userId]);
    $resultCumplimiento = $stmtCumplimiento->fetch(PDO::FETCH_ASSOC);
    
    if ($resultCumplimiento['programadas'] > 0) {
        $stats['cumplimiento'] = round(($resultCumplimiento['asistidas'] / $resultCumplimiento['programadas']) * 100);
    }

    // Obtener calendario del mes actual
    $calendario = [];
    $stmtCalendario = $db->prepare("
        SELECT 
            DAY(fecha_clase) as dia,
            MAX(CASE WHEN asistio = 1 THEN 1 ELSE 0 END) as asistio,
            COUNT(*) as tiene_programado
        FROM clases_programadas
        WHERE usuario_id = :user_id
        AND MONTH(fecha_clase) = MONTH(CURDATE())
        AND YEAR(fecha_clase) = YEAR(CURDATE())
        GROUP BY DAY(fecha_clase)
    ");
    $stmtCalendario->execute([':user_id' => $userId]);
    while ($row = $stmtCalendario->fetch(PDO::FETCH_ASSOC)) {
        $calendario[(int)$row['dia']] = [
            'asistio' => (int)$row['asistio'] === 1,
            'programado' => (int)$row['tiene_programado'] > 0
        ];
    }

    // Obtener entrenamientos programados próximos
    $stmtProgramados = $db->prepare("
        SELECT 
            cp.id,
            cp.fecha_clase,
            cp.hora_inicio,
            cp.nombre_clase,
            cp.sala,
            u.nombre as instructor_nombre,
            u.apellido as instructor_apellido
        FROM clases_programadas cp
        LEFT JOIN usuarios u ON cp.instructor_id = u.id
        WHERE cp.usuario_id = :user_id
        AND cp.fecha_clase >= CURDATE()
        AND cp.asistio = 0
        ORDER BY cp.fecha_clase ASC, cp.hora_inicio ASC
        LIMIT 5
    ");
    $stmtProgramados->execute([':user_id' => $userId]);
    $entrenamientos = $stmtProgramados->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'calendario' => $calendario,
        'entrenamientos' => $entrenamientos
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
