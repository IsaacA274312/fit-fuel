<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario sea nutriólogo
if (empty($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'nutriologo') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    $nutriologoId = $_SESSION['user_id'];

    // Obtener estadísticas del nutriólogo
    $stats = [
        'clientes' => 0,
        'planes' => 0,
        'consultas' => 0,
        'activos' => 0
    ];

    // Contar clientes únicos asignados
    $stmtClientes = $db->prepare("
        SELECT COUNT(DISTINCT usuario_id) as total
        FROM usuario_nutriologo
        WHERE nutriologo_id = :nutriologo_id AND activo = 1
    ");
    $stmtClientes->execute([':nutriologo_id' => $nutriologoId]);
    $resultClientes = $stmtClientes->fetch(PDO::FETCH_ASSOC);
    $stats['clientes'] = (int)$resultClientes['total'];

    // Contar planes creados
    $stmtPlanes = $db->prepare("
        SELECT COUNT(*) as total
        FROM planes_alimenticios
        WHERE nutriologo_id = :nutriologo_id
    ");
    $stmtPlanes->execute([':nutriologo_id' => $nutriologoId]);
    $resultPlanes = $stmtPlanes->fetch(PDO::FETCH_ASSOC);
    $stats['planes'] = (int)$resultPlanes['total'];

    // Contar consultas este mes
    $stmtConsultas = $db->prepare("
        SELECT COUNT(*) as total
        FROM consultas_nutricion
        WHERE nutriologo_id = :nutriologo_id
        AND MONTH(fecha_consulta) = MONTH(CURDATE())
        AND YEAR(fecha_consulta) = YEAR(CURDATE())
    ");
    $stmtConsultas->execute([':nutriologo_id' => $nutriologoId]);
    $resultConsultas = $stmtConsultas->fetch(PDO::FETCH_ASSOC);
    $stats['consultas'] = (int)$resultConsultas['total'];

    // Contar planes activos (activo = 1)
    $stmtActivos = $db->prepare("
        SELECT COUNT(*) as total
        FROM planes_alimenticios
        WHERE nutriologo_id = :nutriologo_id
        AND activo = 1
    ");
    $stmtActivos->execute([':nutriologo_id' => $nutriologoId]);
    $resultActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC);
    $stats['activos'] = (int)$resultActivos['total'];

    // Obtener lista de clientes
    $stmtListaClientes = $db->prepare("
        SELECT DISTINCT
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            COUNT(DISTINCT pa.id) as planes_totales,
            MAX(pa.fecha_inicio) as ultimo_plan
        FROM usuarios u
        INNER JOIN planes_alimenticios pa ON u.id = pa.usuario_id
        WHERE pa.nutriologo_id = :nutriologo_id
        GROUP BY u.id, u.nombre, u.apellido, u.email
        ORDER BY u.nombre ASC
    ");
    $stmtListaClientes->execute([':nutriologo_id' => $nutriologoId]);
    $clientes = $stmtListaClientes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener planes alimenticios
    $stmtPlanesList = $db->prepare("
        SELECT 
            pa.id,
            pa.nombre,
            pa.descripcion,
            pa.objetivo,
            pa.calorias_diarias,
            pa.fecha_inicio,
            pa.fecha_fin,
            pa.activo,
            u.nombre as cliente_nombre,
            u.apellido as cliente_apellido
        FROM planes_alimenticios pa
        INNER JOIN usuarios u ON pa.usuario_id = u.id
        WHERE pa.nutriologo_id = :nutriologo_id
        ORDER BY pa.fecha_inicio DESC
        LIMIT 20
    ");
    $stmtPlanesList->execute([':nutriologo_id' => $nutriologoId]);
    $planes = $stmtPlanesList->fetchAll(PDO::FETCH_ASSOC);

    // Obtener consultas próximas
    $stmtConsultasProximas = $db->prepare("
        SELECT 
            cn.id,
            cn.fecha_consulta,
            cn.hora_consulta,
            cn.motivo,
            cn.notas,
            u.nombre as cliente_nombre,
            u.apellido as cliente_apellido
        FROM consultas_nutricion cn
        INNER JOIN usuarios u ON cn.usuario_id = u.id
        WHERE cn.nutriologo_id = :nutriologo_id
        AND cn.fecha_consulta >= CURDATE()
        ORDER BY cn.fecha_consulta ASC, cn.hora_consulta ASC
        LIMIT 10
    ");
    $stmtConsultasProximas->execute([':nutriologo_id' => $nutriologoId]);
    $consultasProximas = $stmtConsultasProximas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'clientes' => $clientes,
        'planes' => $planes,
        'consultasProximas' => $consultasProximas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
