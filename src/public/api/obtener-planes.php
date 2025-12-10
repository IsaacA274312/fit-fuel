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
    
    // Obtener planes alimenticios disponibles (predefinidos o de nutriólogos)
    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.objetivo,
            p.calorias_diarias,
            p.duracion_dias,
            p.precio,
            u.nombre as nutriologo_nombre,
            u.apellido as nutriologo_apellido
        FROM planes_alimenticios p
        LEFT JOIN usuarios u ON p.nutriologo_id = u.id
        WHERE p.publico = 1 OR p.nutriologo_id IN (
            SELECT nutriologo_id 
            FROM usuario_nutriologo 
            WHERE usuario_id = :usuario_id AND activo = 1
        )
        ORDER BY p.nombre
    ");
    
    $stmt->execute([':usuario_id' => $_SESSION['user_id']]);
    $planes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'planes' => $planes
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener planes: ' . $e->getMessage()]);
}
