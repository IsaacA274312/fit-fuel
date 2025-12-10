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
    $usuarioId = $_SESSION['user_id'];
    
    // Obtener pedidos del usuario
    $stmt = $db->prepare("
        SELECT 
            p.id,
            p.fecha_pedido,
            p.total,
            p.estado
        FROM pedidos p
        WHERE p.cliente_id = :usuario_id
        ORDER BY p.fecha_pedido DESC
    ");
    
    $stmt->execute([':usuario_id' => $usuarioId]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener detalles de cada pedido
    foreach ($pedidos as &$pedido) {
        $stmtDetalles = $db->prepare("
            SELECT 
                dp.id,
                dp.producto_id,
                dp.cantidad,
                dp.precio_unitario,
                (dp.cantidad * dp.precio_unitario) as subtotal,
                p.nombre as producto_nombre,
                p.imagen_url
            FROM detalle_pedidos dp
            LEFT JOIN productos p ON dp.producto_id = p.id
            WHERE dp.pedido_id = :pedido_id
        ");
        
        $stmtDetalles->execute([':pedido_id' => $pedido['id']]);
        $pedido['items'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar campos faltantes con valores por defecto
        $pedido['subtotal'] = $pedido['total'];
        $pedido['descuento'] = 0;
        $pedido['cupon_codigo'] = null;
        $pedido['direccion_envio'] = '';
        $pedido['metodo_pago'] = '';
    }
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener historial: ' . $e->getMessage()]);
}
