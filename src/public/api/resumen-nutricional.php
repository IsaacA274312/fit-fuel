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
    $usuario_id = $_SESSION['user_id'];
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    
    // Obtener consumo del día
    $stmt = $db->prepare("
        SELECT 
            rc.tipo_comida,
            rc.cantidad_gramos,
            rc.hora_registro,
            a.nombre,
            a.marca,
            a.calorias,
            a.proteinas,
            a.carbohidratos,
            a.grasas,
            a.porcion_gramos,
            (rc.cantidad_gramos / a.porcion_gramos * a.calorias) as calorias_consumidas,
            (rc.cantidad_gramos / a.porcion_gramos * a.proteinas) as proteinas_consumidas,
            (rc.cantidad_gramos / a.porcion_gramos * a.carbohidratos) as carbos_consumidos,
            (rc.cantidad_gramos / a.porcion_gramos * a.grasas) as grasas_consumidas,
            rc.id as registro_id
        FROM registro_comidas rc
        INNER JOIN alimentos a ON rc.alimento_id = a.id
        WHERE rc.usuario_id = :usuario_id AND rc.fecha_registro = :fecha
        ORDER BY rc.tipo_comida, rc.hora_registro
    ");
    
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':fecha' => $fecha
    ]);
    
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totales
    $totales = [
        'calorias' => 0,
        'proteinas' => 0,
        'carbohidratos' => 0,
        'grasas' => 0
    ];
    
    $por_comida = [
        'desayuno' => [],
        'comida' => [],
        'cena' => [],
        'snack' => []
    ];
    
    foreach ($registros as $reg) {
        $totales['calorias'] += $reg['calorias_consumidas'];
        $totales['proteinas'] += $reg['proteinas_consumidas'];
        $totales['carbohidratos'] += $reg['carbos_consumidos'];
        $totales['grasas'] += $reg['grasas_consumidas'];
        
        $por_comida[$reg['tipo_comida']][] = $reg;
    }
    
    // Obtener metas
    $stmt = $db->prepare("SELECT * FROM metas_nutricionales WHERE usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $metas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener agua
    $stmt = $db->prepare("SELECT vasos FROM registro_agua WHERE usuario_id = :usuario_id AND fecha_registro = :fecha");
    $stmt->execute([':usuario_id' => $usuario_id, ':fecha' => $fecha]);
    $agua = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'totales' => $totales,
        'por_comida' => $por_comida,
        'metas' => $metas,
        'agua' => $agua ? $agua['vasos'] : 0,
        'fecha' => $fecha
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
