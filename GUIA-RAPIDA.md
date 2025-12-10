# 🚀 Guía Rápida de Inicio - FitAndFuel

## Pasos para poner en marcha el proyecto

### 1️⃣ Verificar Requisitos
Ejecuta el script de verificación:
```bash
check-system.bat
```

### 2️⃣ Instalar Dependencias
Ejecuta el script de instalación:
```bash
install.bat
```

O manualmente:
```bash
npm install
```

### 3️⃣ Configurar Base de Datos

#### Opción A: Nueva instalación
1. Abre XAMPP Control Panel
2. Inicia **Apache** y **MySQL**
3. Abre phpMyAdmin: `http://localhost/phpmyadmin`
4. Importa el archivo: `db/fit-fuel.sql`

#### Opción B: Si ya tienes una tabla usuarios
1. Ejecuta la migración: `db/migrations/001_update_usuarios_table.sql`

### 4️⃣ Verificar Conexión
Abre en tu navegador:
```
http://localhost/fitandfuel/src/test-db.php
```

Este script te mostrará:
- ✅ Estado de la conexión
- 📊 Tablas existentes
- 🔍 Estructura de la tabla usuarios
- 💡 Columnas faltantes (si hay)

### 5️⃣ Configurar Variables de Entorno
Edita el archivo `.env` si tus credenciales de MySQL son diferentes:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fitandfuel
DB_USER=root
DB_PASS=tu_contraseña
PORT=3000
JWT_SECRET=cambia_esto_en_produccion
```

### 6️⃣ Iniciar Servidor
Ejecuta el script de inicio:
```bash
start.bat
```

O manualmente:
```bash
npm run dev
```

### 7️⃣ Acceder a la Aplicación

**Página de Login:**
```
http://localhost/fitandfuel/src/public/index.html
```

**API REST (Node.js):**
```
http://localhost:3000
```

---

## 🔧 Solución de Problemas Comunes

### ❌ Error: "Cannot connect to MySQL"
**Solución:**
1. Verifica que MySQL esté corriendo en XAMPP
2. Confirma que la base de datos `fitandfuel` existe
3. Revisa las credenciales en:
   - `.env` (para Node.js)
   - `src/config/db.php` (para PHP)

### ❌ Error: "Table 'usuarios' doesn't exist"
**Solución:**
1. Importa el script SQL: `db/fit-fuel.sql`
2. O ejecuta manualmente:
   ```bash
   mysql -u root -p < db/fit-fuel.sql
   ```

### ❌ Error: "Column 'telefono' doesn't exist"
**Solución:**
Ejecuta la migración desde phpMyAdmin:
```
db/migrations/001_update_usuarios_table.sql
```

### ❌ Página en blanco o estilos no cargan
**Solución:**
1. Verifica que Apache esté corriendo
2. Asegúrate de acceder a la URL correcta:
   ```
   http://localhost/fitandfuel/src/public/index.html
   ```
3. Verifica que exista: `src/views/css/styles.css`

### ❌ Error: "Cannot find module"
**Solución:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### ❌ Sesión PHP no funciona
**Solución:**
1. Verifica permisos de escritura en la carpeta temporal de PHP
2. En `php.ini`, asegúrate de que `session.save_path` esté configurado

---

## 📁 URLs Importantes

| Descripción | URL |
|-------------|-----|
| Login/Registro | `http://localhost/fitandfuel/src/public/index.html` |
| Test de BD | `http://localhost/fitandfuel/src/test-db.php` |
| phpMyAdmin | `http://localhost/phpmyadmin` |
| API Node.js | `http://localhost:3000` |

---

## 👥 Roles de Usuario

Al registrarte, puedes elegir entre:
- **usuario** - Miembro del gimnasio
- **instructor** - Instructor de fitness
- **nutriologo** - Nutriólogo
- **admin** - Administrador

Cada rol tiene su propio dashboard.

---

## 📝 Siguiente Paso

Después de iniciar sesión, serás redirigido automáticamente al dashboard correspondiente a tu rol:
- `src/views/user/dashboard.php`
- `src/views/instructor/dashboard.php`
- `src/views/nutriologo/dashboard.php`
- `src/views/admin/dashboard.php`

---

## 🆘 Soporte

Si encuentras problemas:
1. Ejecuta `check-system.bat` para verificar el sistema
2. Ejecuta `http://localhost/fitandfuel/src/test-db.php` para verificar la BD
3. Revisa los logs de Apache: `c:\xampp\apache\logs\error.log`
4. Revisa los logs de MySQL: `c:\xampp\mysql\data\mysql_error.log`
