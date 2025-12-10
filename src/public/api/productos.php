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
        $stmt = $pdo->query('SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.creado_en DESC');
        $productos = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $productos]);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = $data['id'] ?? null;
        
        if ($id) {
            $stmt = $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=? WHERE id=?');
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'] ?? null,
                $data['precio'],
                $data['stock'] ?? 0,
                $data['categoria_id'] ?? null,
                $id
            ]);
            echo json_encode(['success' => true, 'message' => 'Producto actualizado']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, creado_en) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'] ?? null,
                $data['precio'],
                $data['stock'] ?? 0,
                $data['categoria_id'] ?? null
            ]);
            echo json_encode(['success' => true, 'message' => 'Producto creado', 'id' => $pdo->lastInsertId()]);
        }
    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $data);
        $id = $data['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM productos WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
