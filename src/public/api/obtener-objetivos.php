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
    
    $stmt = $db->prepare("
        SELECT 
            id,
            tipo_objetivo,
            descripcion,
            valor_actual,
            valor_objetivo,
            unidad,
            fecha_inicio,
            fecha_objetivo,
            completado,
            fecha_completado,
            activo,
            creado_en
        FROM objetivos_usuario
        WHERE usuario_id = :usuario_id AND activo = 1
        ORDER BY completado ASC, fecha_inicio DESC
    ");
    
    $stmt->execute([':usuario_id' => $usuarioId]);
    $objetivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular progreso de cada objetivo
    foreach ($objetivos as &$objetivo) {
        if ($objetivo['valor_actual'] !== null && $objetivo['valor_objetivo'] !== null) {
            $inicio = $objetivo['valor_actual'];
            $meta = $objetivo['valor_objetivo'];
            
            // Obtener valor actual más reciente del progreso
            if ($objetivo['tipo_objetivo'] === 'peso') {
                $stmt = $db->prepare("SELECT peso FROM progreso_usuario WHERE usuario_id = :usuario_id AND peso IS NOT NULL ORDER BY fecha_registro DESC LIMIT 1");
                $stmt->execute([':usuario_id' => $usuarioId]);
                $actual = $stmt->fetchColumn();
                if ($actual) {
                    $objetivo['valor_actual'] = $actual;
                }
            } elseif ($objetivo['tipo_objetivo'] === 'grasa') {
                $stmt = $db->prepare("SELECT porcentaje_grasa FROM progreso_usuario WHERE usuario_id = :usuario_id AND porcentaje_grasa IS NOT NULL ORDER BY fecha_registro DESC LIMIT 1");
                $stmt->execute([':usuario_id' => $usuarioId]);
                $actual = $stmt->fetchColumn();
                if ($actual) {
                    $objetivo['valor_actual'] = $actual;
                }
            }
            
            // Calcular porcentaje de progreso
            if ($objetivo['valor_actual'] !== null) {
                $diferencia = abs($inicio - $meta);
                $avance = abs($inicio - $objetivo['valor_actual']);
                $objetivo['porcentaje_progreso'] = $diferencia > 0 ? min(100, round(($avance / $diferencia) * 100, 1)) : 0;
            } else {
                $objetivo['porcentaje_progreso'] = 0;
            }
        } else {
            $objetivo['porcentaje_progreso'] = 0;
        }
    }
    
    echo json_encode([
        'success' => true,
        'objetivos' => $objetivos
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener objetivos: ' . $e->getMessage()]);
}
