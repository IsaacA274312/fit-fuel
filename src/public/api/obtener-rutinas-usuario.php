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
    $usuarioId = $_SESSION['user_id'];
    
    // Obtener rutinas asignadas al usuario
    $stmt = $db->prepare("
        SELECT 
            ur.id as asignacion_id,
            ur.fecha_asignacion,
            ur.activo,
            r.id as rutina_id,
            r.nombre,
            r.descripcion,
            r.duracion_semanas,
            r.nivel as nivel_dificultad,
            'General' as objetivo,
            u.nombre as instructor_nombre,
            u.apellido as instructor_apellido
        FROM usuario_rutinas ur
        INNER JOIN rutinas r ON ur.rutina_id = r.id
        INNER JOIN usuarios u ON r.instructor_id = u.id
        WHERE ur.usuario_id = :usuario_id
        ORDER BY ur.fecha_asignacion DESC
    ");
    
    $stmt->execute([':usuario_id' => $usuarioId]);
    $rutinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Para cada rutina, obtener los ejercicios
    foreach ($rutinas as &$rutina) {
        $stmtEj = $db->prepare("
            SELECT 
                re.orden,
                re.series,
                re.repeticiones,
                re.descanso_seg,
                re.dia_semana,
                e.nombre,
                e.grupo_muscular,
                e.gif_url
            FROM rutina_ejercicios re
            INNER JOIN ejercicios e ON re.ejercicio_id = e.id
            WHERE re.rutina_id = :rutina_id
            ORDER BY re.dia_semana, re.orden
        ");
        $stmtEj->execute([':rutina_id' => $rutina['rutina_id']]);
        $rutina['ejercicios'] = $stmtEj->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'rutinas' => $rutinas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error obtener-rutinas-usuario.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => 'Error al obtener rutinas: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
