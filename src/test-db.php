<?php
// Script de prueba para verificar la conexión a la base de datos
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head><meta charset='utf-8'><title>Test de Conexión - FitAndFuel</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:40px auto;padding:20px;background:#1a1a1a;color:#fff}";
echo ".success{background:#22c55e;color:#fff;padding:15px;border-radius:8px;margin:10px 0}";
echo ".error{background:#ef4444;color:#fff;padding:15px;border-radius:8px;margin:10px 0}";
echo ".info{background:#3b82f6;color:#fff;padding:15px;border-radius:8px;margin:10px 0}";
echo "h1{color:#f07008}h2{color:#fbbf24}pre{background:#262626;padding:15px;border-radius:8px;overflow:auto}</style>";
echo "</head><body>";

echo "<h1>🔍 FitAndFuel - Test de Conexión</h1>";

// Verificar archivo de configuración
if (!file_exists(__DIR__ . '/config/db.php')) {
    echo "<div class='error'>❌ ERROR: No se encuentra el archivo config/db.php</div>";
    echo "</body></html>";
    exit;
}

echo "<div class='info'>✓ Archivo de configuración encontrado</div>";

// Intentar conectar
try {
    require __DIR__ . '/config/db.php';
    echo "<div class='success'>✓ Conexión a la base de datos exitosa</div>";
    
    // Probar consulta
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<div class='info'>📊 Versión de MySQL/MariaDB: {$version['version']}</div>";
    
    // Verificar si existe la tabla usuarios
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>✓ Tabla 'usuarios' encontrada</div>";
        
        // Mostrar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll();
        
        echo "<h2>Estructura de la tabla 'usuarios':</h2>";
        echo "<pre>";
        foreach ($columns as $col) {
            echo sprintf("%-25s %-20s %s\n", 
                $col['Field'], 
                $col['Type'], 
                $col['Null'] === 'NO' ? 'NOT NULL' : 'NULL'
            );
        }
        echo "</pre>";
        
        // Contar usuarios
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $count = $stmt->fetch();
        echo "<div class='info'>👥 Total de usuarios registrados: {$count['total']}</div>";
        
        // Verificar columnas necesarias
        $requiredColumns = ['telefono', 'fecha_nacimiento', 'genero', 'tipo_usuario'];
        $existingColumns = array_column($columns, 'Field');
        
        echo "<h2>Verificación de columnas requeridas:</h2>";
        foreach ($requiredColumns as $col) {
            if (in_array($col, $existingColumns)) {
                echo "<div class='success'>✓ Columna '{$col}' existe</div>";
            } else {
                echo "<div class='error'>❌ Columna '{$col}' NO existe - Ejecuta la migración</div>";
            }
        }
        
    } else {
        echo "<div class='error'>❌ Tabla 'usuarios' NO encontrada</div>";
        echo "<div class='info'>💡 Ejecuta el script: db/fit-fuel.sql</div>";
    }
    
    // Listar todas las tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<h2>Tablas en la base de datos:</h2>";
        echo "<pre>";
        foreach ($tables as $table) {
            echo "- {$table}\n";
        }
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ ERROR de conexión: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>💡 Verifica:</div>";
    echo "<pre>";
    echo "1. Que MySQL esté corriendo en XAMPP\n";
    echo "2. Que la base de datos 'fitandfuel' exista\n";
    echo "3. Las credenciales en src/config/db.php\n";
    echo "</pre>";
}

echo "<hr>";
echo "<p><a href='views/public/index.html' style='color:#f07008'>← Volver al login</a></p>";
echo "</body></html>";
?>
