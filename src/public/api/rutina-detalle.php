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
    $rutinaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($rutinaId === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de rutina requerido']);
        exit;
    }

    // Verificar que la rutina pertenece al instructor
    $stmtCheck = $db->prepare("
        SELECT id, nombre, descripcion, nivel, duracion_semanas, fecha_creacion
        FROM rutinas
        WHERE id = :rutina_id AND instructor_id = :instructor_id
    ");
    $stmtCheck->execute([
        ':rutina_id' => $rutinaId,
        ':instructor_id' => $_SESSION['user_id']
    ]);
    
    $rutina = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    if (!$rutina) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Rutina no encontrada']);
        exit;
    }

    // Obtener ejercicios de la rutina
    $stmtEjercicios = $db->prepare("
        SELECT 
            re.id as rutina_ejercicio_id,
            re.orden,
            re.series,
            re.repeticiones,
            re.descanso_seg,
            re.notas,
            e.id as ejercicio_id,
            e.nombre,
            e.descripcion,
            e.grupo_muscular,
            e.tipo,
            e.video_url,
            e.gif_url,
            e.equipo_requerido
        FROM rutina_ejercicios re
        INNER JOIN ejercicios e ON re.ejercicio_id = e.id
        WHERE re.rutina_id = :rutina_id
        ORDER BY re.orden ASC
    ");
    $stmtEjercicios->execute([':rutina_id' => $rutinaId]);
    $ejercicios = $stmtEjercicios->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'rutina' => $rutina,
        'ejercicios' => $ejercicios
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener rutina: ' . $e->getMessage()]);
}
