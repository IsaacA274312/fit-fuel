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
    $limite = $_GET['limite'] ?? 30; // Últimos 30 registros por defecto
    
    // Obtener registros de progreso
    $stmt = $db->prepare("
        SELECT 
            id,
            fecha_registro,
            peso,
            altura,
            pecho,
            cintura,
            cadera,
            brazo_derecho,
            brazo_izquierdo,
            pierna_derecha,
            pierna_izquierda,
            porcentaje_grasa,
            masa_muscular,
            imc,
            notas,
            foto_frontal,
            foto_lateral,
            foto_trasera,
            creado_en
        FROM progreso_usuario
        WHERE usuario_id = :usuario_id
        ORDER BY fecha_registro DESC
        LIMIT :limite
    ");
    
    $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->execute();
    
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estadísticas
    $estadisticas = [
        'total_registros' => count($registros),
        'primer_registro' => null,
        'ultimo_registro' => null,
        'cambio_peso' => null,
        'cambio_grasa' => null,
        'cambio_cintura' => null
    ];
    
    if (count($registros) > 0) {
        $primero = end($registros);
        $ultimo = reset($registros);
        
        $estadisticas['primer_registro'] = $primero['fecha_registro'];
        $estadisticas['ultimo_registro'] = $ultimo['fecha_registro'];
        
        if ($primero['peso'] && $ultimo['peso']) {
            $estadisticas['cambio_peso'] = round($ultimo['peso'] - $primero['peso'], 2);
        }
        
        if ($primero['porcentaje_grasa'] && $ultimo['porcentaje_grasa']) {
            $estadisticas['cambio_grasa'] = round($ultimo['porcentaje_grasa'] - $primero['porcentaje_grasa'], 2);
        }
        
        if ($primero['cintura'] && $ultimo['cintura']) {
            $estadisticas['cambio_cintura'] = round($ultimo['cintura'] - $primero['cintura'], 2);
        }
    }
    
    echo json_encode([
        'success' => true,
        'registros' => $registros,
        'estadisticas' => $estadisticas
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener progreso: ' . $e->getMessage()]);
}
