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
    
    $entrenamiento_id = $data['entrenamiento_id'];
    
    // Calcular duración
    $stmt = $db->prepare("
        SELECT fecha_inicio FROM entrenamientos WHERE id = :id AND usuario_id = :usuario_id
    ");
    $stmt->execute([
        ':id' => $entrenamiento_id,
        ':usuario_id' => $_SESSION['user_id']
    ]);
    $entrenamiento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$entrenamiento) {
        throw new Exception('Entrenamiento no encontrado');
    }
    
    $inicio = new DateTime($entrenamiento['fecha_inicio']);
    $fin = new DateTime();
    $duracion = $fin->diff($inicio)->i + ($fin->diff($inicio)->h * 60);
    
    // Calcular volumen total
    $stmt = $db->prepare("
        SELECT SUM(peso_kg * repeticiones) as volumen
        FROM series_ejercicios
        WHERE entrenamiento_id = :id AND completada = 1
    ");
    $stmt->execute([':id' => $entrenamiento_id]);
    $volumen = $stmt->fetch(PDO::FETCH_ASSOC)['volumen'] ?? 0;
    
    // Finalizar entrenamiento
    $stmt = $db->prepare("
        UPDATE entrenamientos 
        SET fecha_fin = NOW(),
            duracion_minutos = :duracion,
            volumen_total = :volumen,
            completado = 1,
            notas = :notas
        WHERE id = :id AND usuario_id = :usuario_id
    ");
    
    $stmt->execute([
        ':id' => $entrenamiento_id,
        ':duracion' => $duracion,
        ':volumen' => $volumen,
        ':notas' => $data['notas'] ?? null,
        ':usuario_id' => $_SESSION['user_id']
    ]);
    
    echo json_encode([
        'success' => true,
        'duracion_minutos' => $duracion,
        'volumen_total' => $volumen
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
