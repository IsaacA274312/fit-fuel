<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../../config/db.php';

$codigo = $_GET['codigo'] ?? '';

if (empty($codigo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Código de cupón requerido']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $usuarioId = $_SESSION['user_id'];
    
    // Validar cupón
    $stmt = $db->prepare("
        SELECT 
            id,
            codigo,
            descripcion,
            tipo_descuento,
            valor_descuento,
            monto_minimo,
            fecha_inicio,
            fecha_fin,
            usos_maximos,
            usos_actuales,
            activo
        FROM cupones
        WHERE codigo = :codigo
        AND activo = 1
    ");
    
    $stmt->execute([':codigo' => strtoupper(trim($codigo))]);
    $cupon = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cupon) {
        echo json_encode(['success' => false, 'error' => 'Cupón no válido o expirado']);
        exit;
    }
    
    // Validar fechas
    $hoy = date('Y-m-d');
    if ($cupon['fecha_inicio'] && $hoy < $cupon['fecha_inicio']) {
        echo json_encode(['success' => false, 'error' => 'Este cupón aún no está disponible']);
        exit;
    }
    if ($cupon['fecha_fin'] && $hoy > $cupon['fecha_fin']) {
        echo json_encode(['success' => false, 'error' => 'Este cupón ha expirado']);
        exit;
    }
    
    // Validar usos máximos
    if ($cupon['usos_maximos'] !== null && $cupon['usos_actuales'] >= $cupon['usos_maximos']) {
        echo json_encode(['success' => false, 'error' => 'Este cupón ha alcanzado su límite de usos']);
        exit;
    }
    
    // Verificar si el usuario ya usó este cupón
    $stmt = $db->prepare("
        SELECT COUNT(*) as usos 
        FROM cupones_usados 
        WHERE cupon_id = :cupon_id AND usuario_id = :usuario_id
    ");
    $stmt->execute([':cupon_id' => $cupon['id'], ':usuario_id' => $usuarioId]);
    $yaUsado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($yaUsado['usos'] > 0) {
        echo json_encode(['success' => false, 'error' => 'Ya has usado este cupón anteriormente']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'cupon' => [
            'id' => $cupon['id'],
            'codigo' => $cupon['codigo'],
            'descripcion' => $cupon['descripcion'],
            'tipo_descuento' => $cupon['tipo_descuento'],
            'valor_descuento' => $cupon['valor_descuento'],
            'monto_minimo' => $cupon['monto_minimo']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al validar cupón: ' . $e->getMessage()]);
}
