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
    
    $vasos = $data['vasos'] ?? 1;
    $fecha = $data['fecha'] ?? date('Y-m-d');
    
    $stmt = $db->prepare("
        INSERT INTO registro_agua (usuario_id, vasos, fecha_registro)
        VALUES (:usuario_id, :vasos, :fecha)
        ON DUPLICATE KEY UPDATE vasos = vasos + :vasos
    ");
    
    $stmt->execute([
        ':usuario_id' => $_SESSION['user_id'],
        ':vasos' => $vasos,
        ':fecha' => $fecha
    ]);
    
    echo json_encode(['success' => true, 'mensaje' => 'Agua registrada']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
