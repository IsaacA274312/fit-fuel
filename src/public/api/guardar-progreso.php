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
    $data = json_decode(file_get_contents('php://input'), true);
    
    $usuarioId = $_SESSION['user_id'];
    $fechaRegistro = $data['fecha_registro'] ?? date('Y-m-d');
    
    // Calcular IMC si hay peso y altura
    $imc = null;
    if (!empty($data['peso']) && !empty($data['altura'])) {
        $alturaMetros = $data['altura'] / 100;
        $imc = $data['peso'] / ($alturaMetros * $alturaMetros);
    }
    
    // Verificar si ya existe un registro para esta fecha
    $stmt = $db->prepare("SELECT id FROM progreso_usuario WHERE usuario_id = :usuario_id AND fecha_registro = :fecha");
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':fecha' => $fechaRegistro
    ]);
    
    $registroExistente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($registroExistente) {
        // Actualizar registro existente
        $stmt = $db->prepare("
            UPDATE progreso_usuario SET
                peso = :peso,
                altura = :altura,
                pecho = :pecho,
                cintura = :cintura,
                cadera = :cadera,
                brazo_derecho = :brazo_derecho,
                brazo_izquierdo = :brazo_izquierdo,
                pierna_derecha = :pierna_derecha,
                pierna_izquierda = :pierna_izquierda,
                porcentaje_grasa = :porcentaje_grasa,
                masa_muscular = :masa_muscular,
                imc = :imc,
                notas = :notas
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':peso' => $data['peso'] ?? null,
            ':altura' => $data['altura'] ?? null,
            ':pecho' => $data['pecho'] ?? null,
            ':cintura' => $data['cintura'] ?? null,
            ':cadera' => $data['cadera'] ?? null,
            ':brazo_derecho' => $data['brazo_derecho'] ?? null,
            ':brazo_izquierdo' => $data['brazo_izquierdo'] ?? null,
            ':pierna_derecha' => $data['pierna_derecha'] ?? null,
            ':pierna_izquierda' => $data['pierna_izquierda'] ?? null,
            ':porcentaje_grasa' => $data['porcentaje_grasa'] ?? null,
            ':masa_muscular' => $data['masa_muscular'] ?? null,
            ':imc' => $imc,
            ':notas' => $data['notas'] ?? null,
            ':id' => $registroExistente['id']
        ]);
        
        $registroId = $registroExistente['id'];
        $mensaje = 'Registro actualizado exitosamente';
        
    } else {
        // Crear nuevo registro
        $stmt = $db->prepare("
            INSERT INTO progreso_usuario (
                usuario_id, fecha_registro, peso, altura, pecho, cintura, cadera,
                brazo_derecho, brazo_izquierdo, pierna_derecha, pierna_izquierda,
                porcentaje_grasa, masa_muscular, imc, notas
            ) VALUES (
                :usuario_id, :fecha_registro, :peso, :altura, :pecho, :cintura, :cadera,
                :brazo_derecho, :brazo_izquierdo, :pierna_derecha, :pierna_izquierda,
                :porcentaje_grasa, :masa_muscular, :imc, :notas
            )
        ");
        
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':fecha_registro' => $fechaRegistro,
            ':peso' => $data['peso'] ?? null,
            ':altura' => $data['altura'] ?? null,
            ':pecho' => $data['pecho'] ?? null,
            ':cintura' => $data['cintura'] ?? null,
            ':cadera' => $data['cadera'] ?? null,
            ':brazo_derecho' => $data['brazo_derecho'] ?? null,
            ':brazo_izquierdo' => $data['brazo_izquierdo'] ?? null,
            ':pierna_derecha' => $data['pierna_derecha'] ?? null,
            ':pierna_izquierda' => $data['pierna_izquierda'] ?? null,
            ':porcentaje_grasa' => $data['porcentaje_grasa'] ?? null,
            ':masa_muscular' => $data['masa_muscular'] ?? null,
            ':imc' => $imc,
            ':notas' => $data['notas'] ?? null
        ]);
        
        $registroId = $db->lastInsertId();
        $mensaje = 'Registro guardado exitosamente';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'registro_id' => $registroId,
        'imc' => $imc
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar registro: ' . $e->getMessage()]);
}
