<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=fit_fuel;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sql = file_get_contents('db/create-cupones.sql');
    
    // Dividir por ; y ejecutar cada statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignorar errores de "tabla ya existe"
                if (strpos($e->getMessage(), 'Duplicate column') === false && 
                    strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }
    
    echo "✓ Sistema de cupones creado correctamente\n\n";
    
    // Verificar cupones creados
    $stmt = $pdo->query("SELECT codigo, descripcion, valor_descuento, tipo_descuento FROM cupones WHERE activo = 1");
    $cupones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Cupones disponibles:\n";
    foreach ($cupones as $cupon) {
        $descuento = $cupon['tipo_descuento'] === 'porcentaje' 
            ? $cupon['valor_descuento'] . '%' 
            : '$' . $cupon['valor_descuento'];
        echo "- {$cupon['codigo']}: {$descuento} - {$cupon['descripcion']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
