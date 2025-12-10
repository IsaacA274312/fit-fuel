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
    $registro_id = $_GET['id'] ?? null;
    
    if (!$registro_id) {
        throw new Exception('ID de registro no proporcionado');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Verificar que el registro pertenece al usuario
    $stmt = $db->prepare("
        DELETE FROM registro_comidas 
        WHERE id = :id AND usuario_id = :usuario_id
    ");
    
    $stmt->execute([
        ':id' => $registro_id,
        ':usuario_id' => $_SESSION['user_id']
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Registro eliminado correctamente'
        ]);
    } else {
        throw new Exception('No se pudo eliminar el registro');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
