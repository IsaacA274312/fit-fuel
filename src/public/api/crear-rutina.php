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
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['nombre']) || empty(trim($data['nombre']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nombre de rutina requerido']);
        exit;
    }

    $db->beginTransaction();

    // Insertar rutina
    $stmtRutina = $db->prepare("
        INSERT INTO rutinas (instructor_id, nombre, descripcion, nivel, duracion_semanas)
        VALUES (:instructor_id, :nombre, :descripcion, :nivel, :duracion_semanas)
    ");
    
    $stmtRutina->execute([
        ':instructor_id' => $_SESSION['user_id'],
        ':nombre' => trim($data['nombre']),
        ':descripcion' => $data['descripcion'] ?? null,
        ':nivel' => $data['nivel'] ?? 'intermedio',
        ':duracion_semanas' => $data['duracion_semanas'] ?? 8
    ]);
    
    $rutinaId = $db->lastInsertId();

    // Insertar ejercicios por día
    if (isset($data['ejercicios']) && is_array($data['ejercicios'])) {
        $stmtEjercicio = $db->prepare("
            INSERT INTO rutina_ejercicios 
            (rutina_id, ejercicio_id, orden, series, repeticiones, descanso_seg, notas)
            VALUES (:rutina_id, :ejercicio_id, :orden, :series, :repeticiones, :descanso_seg, :notas)
        ");

        foreach ($data['ejercicios'] as $ejercicio) {
            $stmtEjercicio->execute([
                ':rutina_id' => $rutinaId,
                ':ejercicio_id' => $ejercicio['ejercicio_id'],
                ':orden' => $ejercicio['orden'] ?? 1,
                ':series' => $ejercicio['series'] ?? 3,
                ':repeticiones' => $ejercicio['repeticiones'] ?? '8-12',
                ':descanso_seg' => $ejercicio['descanso_segundos'] ?? 60,
                ':notas' => $ejercicio['notas'] ?? null
            ]);
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Rutina creada exitosamente',
        'rutina_id' => $rutinaId
    ]);

} catch (PDOException $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al crear rutina: ' . $e->getMessage()]);
}
