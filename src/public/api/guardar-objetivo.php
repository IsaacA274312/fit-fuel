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
    $data = json_decode(file_get_contents('php://input'), true);
    
    $usuarioId = $_SESSION['user_id'];
    
    $stmt = $db->prepare("
        INSERT INTO objetivos_usuario (
            usuario_id, tipo_objetivo, descripcion, valor_actual, valor_objetivo, 
            unidad, fecha_inicio, fecha_objetivo
        ) VALUES (
            :usuario_id, :tipo_objetivo, :descripcion, :valor_actual, :valor_objetivo,
            :unidad, :fecha_inicio, :fecha_objetivo
        )
    ");
    
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':tipo_objetivo' => $data['tipo_objetivo'],
        ':descripcion' => $data['descripcion'],
        ':valor_actual' => $data['valor_actual'] ?? null,
        ':valor_objetivo' => $data['valor_objetivo'],
        ':unidad' => $data['unidad'] ?? 'kg',
        ':fecha_inicio' => $data['fecha_inicio'] ?? date('Y-m-d'),
        ':fecha_objetivo' => $data['fecha_objetivo'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Objetivo creado exitosamente',
        'objetivo_id' => $db->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar objetivo: ' . $e->getMessage()]);
}
