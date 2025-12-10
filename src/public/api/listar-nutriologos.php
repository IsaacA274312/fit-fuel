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
    
    // Obtener todos los nutriólogos activos
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            COUNT(DISTINCT un.usuario_id) as total_clientes
        FROM usuarios u
        LEFT JOIN usuario_nutriologo un ON u.id = un.nutriologo_id AND un.activo = 1
        WHERE u.tipo_usuario = 'nutriologo'
        GROUP BY u.id
        ORDER BY u.nombre ASC
    ");
    
    $stmt->execute();
    $nutriologos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si es usuario, marcar cuál es su nutriólogo actual
    if ($_SESSION['tipo_usuario'] === 'usuario') {
        $stmtMi = $db->prepare("
            SELECT nutriologo_id 
            FROM usuario_nutriologo 
            WHERE usuario_id = :usuario_id AND activo = 1
        ");
        $stmtMi->execute([':usuario_id' => $_SESSION['user_id']]);
        $miNutriologo = $stmtMi->fetch(PDO::FETCH_ASSOC);
        $miNutriologoId = $miNutriologo['nutriologo_id'] ?? null;
        
        foreach ($nutriologos as &$nutriologo) {
            $nutriologo['es_mi_nutriologo'] = ($nutriologo['id'] == $miNutriologoId);
        }
    }
    
    echo json_encode([
        'success' => true,
        'nutriologos' => $nutriologos
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener nutriólogos: ' . $e->getMessage()]);
}
