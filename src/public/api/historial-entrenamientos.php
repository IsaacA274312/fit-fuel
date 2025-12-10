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
    $usuario_id = $_SESSION['user_id'];
    
    // Obtener historial de entrenamientos
    $stmt = $db->prepare("
        SELECT 
            e.id,
            e.fecha_inicio,
            e.fecha_fin,
            e.duracion_minutos,
            e.volumen_total,
            r.nombre as rutina_nombre,
            COUNT(DISTINCT se.ejercicio_id) as total_ejercicios,
            COUNT(se.id) as total_series
        FROM entrenamientos e
        LEFT JOIN rutinas r ON e.rutina_id = r.id
        LEFT JOIN series_ejercicios se ON e.id = se.entrenamiento_id
        WHERE e.usuario_id = :usuario_id AND e.completado = 1
        GROUP BY e.id
        ORDER BY e.fecha_inicio DESC
        LIMIT 30
    ");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $entrenamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener personal records
    $stmt = $db->prepare("
        SELECT 
            pr.*,
            ej.nombre as ejercicio_nombre
        FROM personal_records pr
        INNER JOIN ejercicios ej ON pr.ejercicio_id = ej.id
        WHERE pr.usuario_id = :usuario_id
        ORDER BY pr.fecha_logro DESC
    ");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estadísticas del mes
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_entrenamientos,
            SUM(duracion_minutos) as minutos_totales,
            SUM(volumen_total) as volumen_total,
            AVG(duracion_minutos) as promedio_duracion
        FROM entrenamientos
        WHERE usuario_id = :usuario_id 
        AND completado = 1
        AND fecha_inicio >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'entrenamientos' => $entrenamientos,
        'records' => $records,
        'stats_mes' => $stats
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
