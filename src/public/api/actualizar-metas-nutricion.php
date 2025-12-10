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
        INSERT INTO metas_nutricionales 
        (usuario_id, calorias_objetivo, proteinas_objetivo, carbohidratos_objetivo, grasas_objetivo, agua_vasos_objetivo, peso_objetivo)
        VALUES (:usuario_id, :calorias, :proteinas, :carbos, :grasas, :agua, :peso)
        ON DUPLICATE KEY UPDATE
        calorias_objetivo = :calorias,
        proteinas_objetivo = :proteinas,
        carbohidratos_objetivo = :carbos,
        grasas_objetivo = :grasas,
        agua_vasos_objetivo = :agua,
        peso_objetivo = :peso
    ");
    
    $stmt->execute([
        ':usuario_id' => $_SESSION['user_id'],
        ':calorias' => $data['calorias_objetivo'],
        ':proteinas' => $data['proteinas_objetivo'],
        ':carbos' => $data['carbohidratos_objetivo'],
        ':grasas' => $data['grasas_objetivo'],
        ':agua' => $data['agua_vasos_objetivo'] ?? 8,
        ':peso' => $data['peso_objetivo'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'mensaje' => 'Metas actualizadas']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
