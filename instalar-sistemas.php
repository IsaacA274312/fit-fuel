<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=fit_fuel;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sql = file_get_contents('db/INSTALACION-COMPLETA.sql');
    
    // Dividir por ; y ejecutar cada statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $ejecutados = 0;
    $errores = 0;
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement) && !preg_match('/^SELECT/', $statement)) {
            try {
                $pdo->exec($statement);
                $ejecutados++;
            } catch (PDOException $e) {
                // Ignorar errores de "ya existe" o "columna duplicada"
                if (strpos($e->getMessage(), 'Duplicate column') === false && 
                    strpos($e->getMessage(), 'already exists') === false &&
                    strpos($e->getMessage(), 'Duplicate entry') === false) {
                    echo "Error en statement: " . substr($statement, 0, 50) . "...\n";
                    echo "Error: " . $e->getMessage() . "\n\n";
                    $errores++;
                }
            }
        }
    }
    
    echo "✓ Instalación completada\n\n";
    echo "Statements ejecutados: $ejecutados\n";
    if ($errores > 0) echo "Errores: $errores\n\n";
    
    // Verificar instalación
    echo "=== VERIFICACIÓN ===\n\n";
    
    // Cupones
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cupones WHERE activo = 1");
    $cupones = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Cupones activos: {$cupones['total']}\n";
    
    $stmt = $pdo->query("SELECT codigo, valor_descuento, tipo_descuento FROM cupones WHERE activo = 1 LIMIT 3");
    while ($cupon = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $desc = $cupon['tipo_descuento'] === 'porcentaje' ? $cupon['valor_descuento'].'%' : '$'.$cupon['valor_descuento'];
        echo "  - {$cupon['codigo']}: {$desc}\n";
    }
    
    // Notificaciones
    echo "\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = 2 AND leida = 0");
    $notifs = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Notificaciones de prueba (usuario 2): {$notifs['total']} no leídas\n";
    
    $stmt = $pdo->query("SELECT titulo, icono FROM notificaciones WHERE usuario_id = 2 LIMIT 5");
    while ($notif = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$notif['icono']} {$notif['titulo']}\n";
    }
    
    // Tablas creadas
    echo "\n✓ Tablas del sistema:\n";
    $tablas = ['cupones', 'cupones_usados', 'progreso_usuario', 'objetivos_usuario', 'notificaciones', 'notificaciones_preferencias'];
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ $tabla\n";
        } else {
            echo "  ✗ $tabla (NO CREADA)\n";
        }
    }
    
    echo "\n=== INSTRUCCIONES ===\n";
    echo "1. Inicia sesión como usuario (no instructor/nutriólogo)\n";
    echo "2. Verás el badge rojo con '5' en Notificaciones\n";
    echo "3. Prueba el carrito con cupón: BIENVENIDO10\n";
    echo "4. Registra tu progreso en 📊 Mi Progreso\n";
    echo "\n¡Sistema listo para usar! 🚀\n";
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
    echo "\nAsegúrate de que:\n";
    echo "1. XAMPP esté ejecutándose\n";
    echo "2. MySQL esté activo\n";
    echo "3. La base de datos 'fit_fuel' exista\n";
}
