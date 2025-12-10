<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Verificar admin
if (empty($_SESSION['user_id']) || strtolower($_SESSION['tipo_usuario'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require __DIR__ . '/../../config/db.php';

try {
    // Totales
    $totalUsuarios = $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $totalProductos = $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
    $totalCategorias = $pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
    $totalClientes = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
    $totalPedidos = $pdo->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();

    // Usuarios por tipo
    $usuariosPorTipo = $pdo->query('SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario')->fetchAll();
    
    // Productos por categoría
    $productosPorCategoria = $pdo->query('
        SELECT c.nombre, COUNT(p.id) as total 
        FROM categorias c 
        LEFT JOIN productos p ON c.id = p.categoria_id 
        GROUP BY c.id, c.nombre
    ')->fetchAll();

    // Pedidos últimos 7 días
    $pedidosRecientes = $pdo->query('
        SELECT DATE(fecha_pedido) as fecha, COUNT(*) as total 
        FROM pedidos 
        WHERE fecha_pedido >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(fecha_pedido)
        ORDER BY fecha
    ')->fetchAll();

    echo json_encode([
        'success' => true,
        'totales' => [
            'usuarios' => (int)$totalUsuarios,
            'productos' => (int)$totalProductos,
            'categorias' => (int)$totalCategorias,
            'clientes' => (int)$totalClientes,
            'pedidos' => (int)$totalPedidos
        ],
        'usuariosPorTipo' => $usuariosPorTipo,
        'productosPorCategoria' => $productosPorCategoria,
        'pedidosRecientes' => $pedidosRecientes
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al obtener estadísticas']);
}
