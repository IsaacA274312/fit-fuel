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
    
    // Obtener productos con sus categorías
    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            p.imagen_url,
            c.nombre as categoria
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.stock > 0
        ORDER BY c.nombre, p.nombre
    ");
    
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agrupar por categoría
    $productosPorCategoria = [];
    foreach ($productos as $producto) {
        $categoria = $producto['categoria'] ?? 'Sin Categoría';
        if (!isset($productosPorCategoria[$categoria])) {
            $productosPorCategoria[$categoria] = [];
        }
        $productosPorCategoria[$categoria][] = $producto;
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'productosPorCategoria' => $productosPorCategoria
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener productos: ' . $e->getMessage()]);
}
