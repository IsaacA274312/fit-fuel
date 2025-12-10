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
        // Listar todos los usuarios
        $stmt = $pdo->query('SELECT id, nombre, apellido, email, tipo_usuario, telefono, fecha_nacimiento, genero, creado_en FROM usuarios ORDER BY creado_en DESC');
        $usuarios = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $usuarios]);
    } elseif ($method === 'POST') {
        // Crear o actualizar usuario
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = $data['id'] ?? null;
        
        if ($id) {
            // Actualizar
            $stmt = $pdo->prepare('UPDATE usuarios SET nombre=?, apellido=?, email=?, tipo_usuario=?, telefono=?, genero=? WHERE id=?');
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $data['tipo_usuario'],
                $data['telefono'] ?? null,
                $data['genero'] ?? null,
                $id
            ]);
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado']);
        } else {
            // Crear nuevo
            $hash = password_hash($data['password'] ?? 'Temporal123!', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, password, tipo_usuario, telefono, genero, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $hash,
                $data['tipo_usuario'] ?? 'usuario',
                $data['telefono'] ?? null,
                $data['genero'] ?? null
            ]);
            echo json_encode(['success' => true, 'message' => 'Usuario creado', 'id' => $pdo->lastInsertId()]);
        }
    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $data);
        $id = $data['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID requerido']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
