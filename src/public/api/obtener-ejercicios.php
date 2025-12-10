<?php
session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Obtener todos los ejercicios disponibles
    $stmt = $db->prepare("
        SELECT 
            id,
            nombre,
            descripcion,
            grupo_muscular,
            tipo,
            video_url,
            gif_url,
            equipo_requerido
        FROM ejercicios
        ORDER BY grupo_muscular, nombre
    ");
    
    $stmt->execute();
    $ejercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por grupo muscular
    $ejerciciosPorGrupo = [];
    foreach ($ejercicios as $ejercicio) {
        $grupo = $ejercicio['grupo_muscular'] ?? 'General';
        if (!isset($ejerciciosPorGrupo[$grupo])) {
            $ejerciciosPorGrupo[$grupo] = [];
        }
        $ejerciciosPorGrupo[$grupo][] = $ejercicio;
    }

    echo json_encode([
        'success' => true,
        'ejercicios' => $ejercicios,
        'ejerciciosPorGrupo' => $ejerciciosPorGrupo
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener ejercicios: ' . $e->getMessage()]);
}
