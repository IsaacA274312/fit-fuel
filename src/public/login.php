<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/roleHelper.php';

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
  echo json_encode(['success'=>false,'message'=>'Email y contraseña requeridos']);
  exit;
}

try {
  $stmt = $pdo->prepare('SELECT id, nombre, apellido, email, password, tipo_usuario FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email'=>$email]);
  $user = $stmt->fetch();
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'Error consultando usuario']);
  exit;
}

if (!$user || !password_verify($password, $user['password'])) {
  echo json_encode(['success'=>false,'message'=>'Credenciales inválidas']);
  exit;
}

$normalized = normalize_role($user['tipo_usuario'] ?? '');

$_SESSION['user_id'] = $user['id'];
$_SESSION['nombre'] = $user['nombre'];
$_SESSION['apellido'] = $user['apellido'];
$_SESSION['email'] = $user['email'];
$_SESSION['tipo_usuario'] = $normalized;

$redirect = role_redirect_path($normalized);
echo json_encode(['success'=>true,'message'=>'Login correcto','redirect'=>$redirect]);