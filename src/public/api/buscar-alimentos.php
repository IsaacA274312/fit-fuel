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
    
    $busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
    
    $query = "SELECT * FROM alimentos WHERE 1=1";
    $params = [];
    
    if ($busqueda !== '') {
        $query .= " AND (nombre LIKE :busqueda OR marca LIKE :busqueda)";
        $params[':busqueda'] = '%' . $busqueda . '%';
    }
    
    if ($categoria !== '') {
        $query .= " AND categoria = :categoria";
        $params[':categoria'] = $categoria;
    }
    
    $query .= " ORDER BY verificado DESC, nombre ASC LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $alimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'alimentos' => $alimentos
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
