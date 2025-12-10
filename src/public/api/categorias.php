<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (empty($_SESSION['user_id']) || strtolower($_SESSION['tipo_usuario'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require __DIR__ . '/../../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT * FROM categorias ORDER BY nombre');
        $categorias = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $categorias]);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = $data['id'] ?? null;
        
        if ($id) {
            $stmt = $pdo->prepare('UPDATE categorias SET nombre=?, descripcion=? WHERE id=?');
            $stmt->execute([$data['nombre'], $data['descripcion'] ?? null, $id]);
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO categorias (nombre, descripcion, creado_en) VALUES (?, ?, NOW())');
            $stmt->execute([$data['nombre'], $data['descripcion'] ?? null]);
            echo json_encode(['success' => true, 'message' => 'Categoría creada', 'id' => $pdo->lastInsertId()]);
        }
    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $data);
        $id = $data['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
