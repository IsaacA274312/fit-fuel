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
    $data = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance()->getConnection();
    
    $rutina_id = $data['rutina_id'] ?? null;
    
    // Crear nuevo entrenamiento
    $stmt = $db->prepare("
        INSERT INTO entrenamientos (usuario_id, rutina_id, fecha_inicio)
        VALUES (:usuario_id, :rutina_id, NOW())
    ");
    
    $stmt->execute([
        ':usuario_id' => $_SESSION['user_id'],
        ':rutina_id' => $rutina_id
    ]);
    
    $entrenamiento_id = $db->lastInsertId();
    
    // Si hay rutina, cargar ejercicios
    $ejercicios = [];
    if ($rutina_id) {
        $stmt = $db->prepare("
            SELECT 
                re.ejercicio_id,
                re.series,
                re.repeticiones,
                re.descanso_segundos,
                re.notas,
                e.nombre,
                e.grupo_muscular,
                e.gif_url
            FROM rutina_ejercicios re
            INNER JOIN ejercicios e ON re.ejercicio_id = e.id
            WHERE re.rutina_id = :rutina_id
            ORDER BY re.orden
        ");
        $stmt->execute([':rutina_id' => $rutina_id]);
        $ejercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener historial de cada ejercicio
        foreach ($ejercicios as &$ejercicio) {
            $stmt = $db->prepare("
                SELECT peso_kg, repeticiones, fecha_registro
                FROM series_ejercicios se
                INNER JOIN entrenamientos ent ON se.entrenamiento_id = ent.id
                WHERE ent.usuario_id = :usuario_id 
                AND se.ejercicio_id = :ejercicio_id
                AND se.completada = 1
                ORDER BY se.fecha_registro DESC
                LIMIT 5
            ");
            $stmt->execute([
                ':usuario_id' => $_SESSION['user_id'],
                ':ejercicio_id' => $ejercicio['ejercicio_id']
            ]);
            $ejercicio['historial'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    echo json_encode([
        'success' => true,
        'entrenamiento_id' => $entrenamiento_id,
        'ejercicios' => $ejercicios
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
