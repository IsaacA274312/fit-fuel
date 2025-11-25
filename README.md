# FitAndFuel

Repositorio mínimo para autenticación (registro / login) con PHP + PDO y frontend responsive.

## Requisitos
- XAMPP (Apache + MySQL)
- PHP 7.4+ con PDO MySQL
- Navegador moderno

## Instalación rápida (Windows)
1. Arrancar Apache y MySQL desde XAMPP.
2. Importar la base de datos:
   - phpMyAdmin → importar `c:\xampp\htdocs\fitandfuel\db\fit&fuel.sql`
   - o CLI:
     ```
     "C:\xampp\mysql\bin\mysql.exe" -u root < "c:\xampp\htdocs\fitandfuel\db\fit&fuel.sql"
     ```
3. Verificar credenciales en `src\config\db.php`.

## Estructura relevante
- src/
  - config/db.php — conexión PDO
  - public/
    - index.html — frontend (registro/login)
    - register.php, login.php, logout.php — endpoints API
  - views/
    - dashboard.php — página protegida
  - images/ — colocar `logo.png` y `fondo.jpg` aquí

## Uso
- Abrir en navegador:
  ```
  http://localhost/fitandfuel/src/public/index.html
  ```
- Registra usuario y luego inicia sesión. Dashboard usa sesión PHP.

## Endpoints (JSON)
- POST src/public/register.php
  - body: { nombre, apellido_paterno, apellido_materno, email, password }
- POST src/public/login.php
  - body: { email, password }
- POST src/public/logout.php

Ejemplo curl:
```
curl -X POST http://localhost/fitandfuel/src/public/register.php -H "Content-Type: application/json" -d "{\"nombre\":\"A\",\"apellido_paterno\":\"B\",\"apellido_materno\":\"C\",\"email\":\"a@b.com\",\"password\":\"123456\"}"
```

## Seguridad y notas
- Contraseñas almacenadas con `password_hash`.
- En producción: usar HTTPS, SameSite cookies, y mover `public` a la raíz pública.
- Ajustar permisos y ocultar archivos de configuración sensibles.

## Git — error `src refspec main does not match any`
Ese error ocurre si no existe la rama `main` o no hay commits locales. Solución típica:
```
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/IsaacA274312/fitandfuel.git   # si aún no existe remote
git push -u origin main
```

## Contacto / próximos pasos
- Separar apellidos en columnas (ALTER TABLE) si quieres.
- Añadir validación servidor más estricta y protección CSRF.

