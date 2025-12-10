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
    
    // Obtener todos los instructores activos
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.email,
            COUNT(DISTINCT ui.usuario_id) as total_clientes
        FROM usuarios u
        LEFT JOIN usuario_instructor ui ON u.id = ui.instructor_id AND ui.activo = 1
        WHERE u.tipo_usuario = 'instructor'
        GROUP BY u.id
        ORDER BY u.nombre ASC
    ");
    
    $stmt->execute();
    $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si es usuario, marcar cuál es su instructor actual
    if ($_SESSION['tipo_usuario'] === 'usuario') {
        $stmtMi = $db->prepare("
            SELECT instructor_id 
            FROM usuario_instructor 
            WHERE usuario_id = :usuario_id AND activo = 1
        ");
        $stmtMi->execute([':usuario_id' => $_SESSION['user_id']]);
        $miInstructor = $stmtMi->fetch(PDO::FETCH_ASSOC);
        $miInstructorId = $miInstructor['instructor_id'] ?? null;
        
        foreach ($instructores as &$instructor) {
            $instructor['es_mi_instructor'] = ($instructor['id'] == $miInstructorId);
        }
    }
    
    echo json_encode([
        'success' => true,
        'instructores' => $instructores
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener instructores: ' . $e->getMessage()]);
}
