<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/roleHelper.php';

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$nombre = trim($data['nombre'] ?? '');
$apP = trim($data['apellido_paterno'] ?? '');
$apM = trim($data['apellido_materno'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$fecha_nacimiento = trim($data['fecha_nacimiento'] ?? '');
$genero = trim($data['genero'] ?? '');
$tipo_raw = trim($data['tipo_usuario'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$nombre || !$apP || !$apM || !$email || !$password) {
  echo json_encode(['success'=>false,'message'=>'Campos requeridos faltan']);
  exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success'=>false,'message'=>'Email inválido']);
  exit;
}
if (strlen($password) < 8 || !preg_match('/[a-z]/',$password) || !preg_match('/[A-Z]/',$password) || !preg_match('/\d/',$password) || !preg_match('/[\W_]/',$password)) {
  echo json_encode(['success'=>false,'message'=>'Contraseña débil']);
  exit;
}
if ($telefono && !preg_match('/^\+?[0-9\-\s]{8,15}$/',$telefono)) {
  echo json_encode(['success'=>false,'message'=>'Teléfono inválido']);
  exit;
}
if ($fecha_nacimiento) {
  $min = strtotime('1900-01-01');
  $max = strtotime('-10 years');
  $ts = strtotime($fecha_nacimiento);
  if ($ts < $min || $ts > $max) {
    echo json_encode(['success'=>false,'message'=>'Fecha nacimiento fuera de rango']);
    exit;
  }
}

$tipo = normalize_role($tipo_raw);

try {
  $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email'=>$email]);
  if ($stmt->fetch()) { echo json_encode(['success'=>false,'message'=>'Email ya registrado']); exit; }
} catch (PDOException $e) {
  echo json_encode(['success'=>false,'message'=>'Error verificando email']);
  exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$apellido = trim($apP.' '.$apM);

try {
  $ins = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, password, telefono, fecha_nacimiento, genero, tipo_usuario, creado_en) VALUES (:nombre,:apellido,:email,:password,:telefono,:fecha_nacimiento,:genero,:tipo_usuario,NOW())');
  $ins->execute([
    'nombre'=>$nombre,
    'apellido'=>$apellido,
    'email'=>$email,
    'password'=>$hash,
    'telefono'=>$telefono ?: null,
    'fecha_nacimiento'=>$fecha_nacimiento ?: null,
    'genero'=>$genero ?: null,
    'tipo_usuario'=>$tipo ?: 'usuario'
  ]);
  echo json_encode(['success'=>true,'message'=>'Registro exitoso']);
} catch (PDOException $e) {
  echo json_encode(['success'=>false,'message'=>'Error al registrar']);
}