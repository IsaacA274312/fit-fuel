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
    
    $alimento_id = $data['alimento_id'] ?? null;
    $cantidad_gramos = $data['cantidad_gramos'] ?? null;
    $tipo_comida = $data['tipo_comida'] ?? null;
    $fecha_registro = $data['fecha_registro'] ?? date('Y-m-d');
    
    if (!$alimento_id || !$cantidad_gramos || !$tipo_comida) {
        throw new Exception('Faltan datos requeridos');
    }
    
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        INSERT INTO registro_comidas 
        (usuario_id, alimento_id, cantidad_gramos, tipo_comida, fecha_registro, hora_registro, notas)
        VALUES (:usuario_id, :alimento_id, :cantidad, :tipo, :fecha, :hora, :notas)
    ");
    
    $stmt->execute([
        ':usuario_id' => $_SESSION['user_id'],
        ':alimento_id' => $alimento_id,
        ':cantidad' => $cantidad_gramos,
        ':tipo' => $tipo_comida,
        ':fecha' => $fecha_registro,
        ':hora' => date('H:i:s'),
        ':notas' => $data['notas'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'id' => $db->lastInsertId(),
        'mensaje' => 'Comida registrada correctamente'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
