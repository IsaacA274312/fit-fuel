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
    $limite = $_GET['limite'] ?? 20;
    $soloNoLeidas = isset($_GET['no_leidas']) && $_GET['no_leidas'] === '1';
    
    $sql = "
        SELECT 
            n.id,
            n.tipo,
            n.titulo,
            n.mensaje,
            n.icono,
            n.url,
            n.leida,
            n.importante,
            n.fecha_creacion,
            n.fecha_leida,
            n.datos_adicionales,
            u.nombre as remitente_nombre,
            u.apellido as remitente_apellido
        FROM notificaciones n
        LEFT JOIN usuarios u ON n.remitente_id = u.id
        WHERE n.usuario_id = :usuario_id
    ";
    
    if ($soloNoLeidas) {
        $sql .= " AND n.leida = 0";
    }
    
    $sql .= " ORDER BY n.importante DESC, n.fecha_creacion DESC LIMIT :limite";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->execute();
    
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar no leídas
    $stmtCount = $db->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = :usuario_id AND leida = 0");
    $stmtCount->execute([':usuario_id' => $usuarioId]);
    $noLeidas = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'notificaciones' => $notificaciones,
        'total_no_leidas' => $noLeidas
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener notificaciones: ' . $e->getMessage()]);
}
