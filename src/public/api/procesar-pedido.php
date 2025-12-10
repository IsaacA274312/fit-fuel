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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['items']) || !is_array($data['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Carrito vacío']);
        exit;
    }
    
    $usuarioId = $_SESSION['user_id'];
    $direccionEnvio = $data['direccion'] ?? null;
    $metodoPago = $data['metodo_pago'] ?? 'pendiente';
    $cuponId = $data['cupon_id'] ?? null;
    $descuento = 0;
    
    $db->beginTransaction();
    
    // Calcular total
    $subtotal = 0;
    $itemsValidados = [];
    
    foreach ($data['items'] as $item) {
        if ($item['tipo'] === 'producto') {
            $stmt = $db->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = :id");
            $stmt->execute([':id' => $item['id']]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                $db->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Producto no encontrado: ' . $item['id']]);
                exit;
            }
            
            if ($producto['stock'] < $item['cantidad']) {
                $db->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Stock insuficiente para: ' . $producto['nombre']]);
                exit;
            }
            
            $subtotal = $producto['precio'] * $item['cantidad'];
            $subtotal += $subtotal;
            $itemsValidados[] = [
                'tipo' => 'producto',
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $item['cantidad'],
                'subtotal' => $subtotal
            ];
            
        } elseif ($item['tipo'] === 'plan') {
            $stmt = $db->prepare("SELECT id, nombre, precio FROM planes_alimenticios WHERE id = :id");
            $stmt->execute([':id' => $item['id']]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$plan) {
                $db->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Plan no encontrado: ' . $item['id']]);
                exit;
            }
            
            $subtotal = $plan['precio'];
            $subtotal += $subtotal;
            $itemsValidados[] = [
                'tipo' => 'plan',
                'id' => $plan['id'],
                'nombre' => $plan['nombre'],
                'precio' => $plan['precio'],
                'cantidad' => 1,
                'subtotal' => $subtotal
            ];
        }
    }
    
    // Aplicar cupón si existe
    if ($cuponId) {
        $stmt = $db->prepare("SELECT id, tipo_descuento, valor_descuento FROM cupones WHERE id = :id AND activo = 1");
        $stmt->execute([':id' => $cuponId]);
        $cupon = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cupon) {
            if ($cupon['tipo_descuento'] === 'porcentaje') {
                $descuento = ($subtotal * $cupon['valor_descuento']) / 100;
            } else {
                $descuento = min($cupon['valor_descuento'], $subtotal);
            }
        }
    }
    
    $total = $subtotal - $descuento;
    
    // Crear pedido (usar solo campos que existen en la tabla)
    $stmt = $db->prepare("
        INSERT INTO pedidos (cliente_id, total, estado, fecha_pedido)
        VALUES (:cliente_id, :total, 'pendiente', NOW())
    ");
    
    $stmt->execute([
        ':cliente_id' => $usuarioId,
        ':total' => $total
    ]);
    
    $pedidoId = $db->lastInsertId();
    
    // Registrar uso del cupón
    if ($cuponId && $descuento > 0) {
        $stmt = $db->prepare("
            INSERT INTO cupones_usados (cupon_id, usuario_id, pedido_id, descuento_aplicado)
            VALUES (:cupon_id, :usuario_id, :pedido_id, :descuento)
        ");
        $stmt->execute([
            ':cupon_id' => $cuponId,
            ':usuario_id' => $usuarioId,
            ':pedido_id' => $pedidoId,
            ':descuento' => $descuento
        ]);
        
        // Incrementar contador de usos
        $stmt = $db->prepare("UPDATE cupones SET usos_actuales = usos_actuales + 1 WHERE id = :id");
        $stmt->execute([':id' => $cuponId]);
    }
    
    // Insertar detalles del pedido
    foreach ($itemsValidados as $item) {
        if ($item['tipo'] === 'producto') {
            $stmt = $db->prepare("
                INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario)
                VALUES (:pedido_id, :producto_id, :cantidad, :precio)
            ");
            
            $stmt->execute([
                ':pedido_id' => $pedidoId,
                ':producto_id' => $item['id'],
                ':cantidad' => $item['cantidad'],
                ':precio' => $item['precio']
            ]);
            
            // Actualizar stock
            $stmt = $db->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");
            $stmt->execute([':cantidad' => $item['cantidad'], ':id' => $item['id']]);
        } else {
            // Para planes, guardar como un detalle especial o en tabla separada
            $stmt = $db->prepare("
                INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario)
                VALUES (:pedido_id, :plan_id, 1, :precio)
            ");
            
            $stmt->execute([
                ':pedido_id' => $pedidoId,
                ':plan_id' => $item['id'],
                ':precio' => $item['precio']
            ]);
        }
    }
    
    $db->commit();
    
    // Crear notificación de pedido confirmado
    try {
        $stmtNotif = $db->prepare("
            INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, icono, datos_adicionales)
            VALUES (:usuario_id, 'pedido', :titulo, :mensaje, '📦', :datos)
        ");
        
        $stmtNotif->execute([
            ':usuario_id' => $usuarioId,
            ':titulo' => 'Pedido Confirmado',
            ':mensaje' => 'Tu pedido #' . $pedidoId . ' ha sido confirmado. Total: $' . number_format($total, 2),
            ':datos' => json_encode(['pedido_id' => $pedidoId, 'total' => $total])
        ]);
    } catch (Exception $e) {
        // Si falla la notificación, no afecta al pedido
        error_log('Error al crear notificación: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Pedido creado exitosamente',
        'pedido_id' => $pedidoId,
        'subtotal' => $subtotal,
        'descuento' => $descuento,
        'total' => $total
    ]);
    
} catch (PDOException $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al procesar pedido: ' . $e->getMessage()]);
}
