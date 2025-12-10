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
    
    $stmt = $db->prepare("
        INSERT INTO series_ejercicios 
        (entrenamiento_id, ejercicio_id, orden, serie_numero, peso_kg, repeticiones, rpe, completada, notas)
        VALUES (:entrenamiento_id, :ejercicio_id, :orden, :serie_numero, :peso, :reps, :rpe, 1, :notas)
    ");
    
    $stmt->execute([
        ':entrenamiento_id' => $data['entrenamiento_id'],
        ':ejercicio_id' => $data['ejercicio_id'],
        ':orden' => $data['orden'] ?? 1,
        ':serie_numero' => $data['serie_numero'],
        ':peso' => $data['peso_kg'] ?? null,
        ':reps' => $data['repeticiones'] ?? null,
        ':rpe' => $data['rpe'] ?? null,
        ':notas' => $data['notas'] ?? null
    ]);
    
    $serie_id = $db->lastInsertId();
    
    // Verificar si es personal record
    if (isset($data['peso_kg']) && $data['peso_kg'] > 0) {
        $stmt = $db->prepare("
            SELECT valor FROM personal_records 
            WHERE usuario_id = :usuario_id 
            AND ejercicio_id = :ejercicio_id 
            AND tipo_record = 'peso_maximo'
        ");
        $stmt->execute([
            ':usuario_id' => $_SESSION['user_id'],
            ':ejercicio_id' => $data['ejercicio_id']
        ]);
        $pr_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pr_actual || $data['peso_kg'] > $pr_actual['valor']) {
            $stmt = $db->prepare("
                INSERT INTO personal_records 
                (usuario_id, ejercicio_id, tipo_record, valor, fecha_logro, serie_id)
                VALUES (:usuario_id, :ejercicio_id, 'peso_maximo', :valor, NOW(), :serie_id)
                ON DUPLICATE KEY UPDATE 
                valor = :valor, fecha_logro = NOW(), serie_id = :serie_id
            ");
            $stmt->execute([
                ':usuario_id' => $_SESSION['user_id'],
                ':ejercicio_id' => $data['ejercicio_id'],
                ':valor' => $data['peso_kg'],
                ':serie_id' => $serie_id
            ]);
            
            $es_pr = true;
        }
    }
    
    echo json_encode([
        'success' => true,
        'serie_id' => $serie_id,
        'es_pr' => $es_pr ?? false
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
