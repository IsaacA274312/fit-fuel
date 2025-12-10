@echo off
echo ================================================
echo   FitAndFuel - Verificacion del Sistema
echo ================================================
echo.

echo [1/6] Verificando Node.js...
where node >nul 2>nul
if %errorlevel% equ 0 (
    echo [OK] Node.js instalado
    node --version
) else (
    echo [ERROR] Node.js NO instalado
)
echo.

echo [2/6] Verificando npm...
where npm >nul 2>nul
if %errorlevel% equ 0 (
    echo [OK] npm instalado
    npm --version
) else (
    echo [ERROR] npm NO instalado
)
echo.

echo [3/6] Verificando XAMPP - MySQL...
if exist "c:\xampp\mysql\bin\mysql.exe" (
    echo [OK] MySQL encontrado en XAMPP
) else (
    echo [AVISO] MySQL no encontrado en c:\xampp\mysql\bin\
)
echo.

echo [4/6] Verificando XAMPP - Apache...
if exist "c:\xampp\apache\bin\httpd.exe" (
    echo [OK] Apache encontrado en XAMPP
) else (
    echo [AVISO] Apache no encontrado en c:\xampp\apache\bin\
)
echo.

echo [5/6] Verificando archivo .env...
if exist ".env" (
    echo [OK] Archivo .env encontrado
) else (
    echo [AVISO] Archivo .env NO encontrado
    echo Creando archivo .env desde plantilla...
)
echo.

echo [6/6] Verificando node_modules...
if exist "node_modules" (
    echo [OK] Dependencias de Node.js instaladas
) else (
    echo [AVISO] Dependencias NO instaladas
    echo Ejecuta: npm install
)
echo.

echo ================================================
echo   Verificacion de Archivos Criticos
echo ================================================
echo.

if exist "db\fit-fuel.sql" (
    echo [OK] Script SQL encontrado
) else (
    echo [ERROR] Script SQL NO encontrado
)

if exist "src\config\db.js" (
    echo [OK] Configuracion Node.js encontrada
) else (
    echo [ERROR] Configuracion Node.js NO encontrada
)

if exist "src\config\db.php" (
    echo [OK] Configuracion PHP encontrada
) else (
    echo [ERROR] Configuracion PHP NO encontrada
)

if exist "index.js" (
    echo [OK] Servidor principal encontrado
) else (
    echo [ERROR] index.js NO encontrado
)

if exist "src\views\public\index.html" (
    echo [OK] Pagina de login encontrada
) else (
    echo [ERROR] Pagina de login NO encontrada
)

if exist "src\public\css\styles.css" (
    echo [OK] CSS publico encontrado
) else (
    echo [AVISO] CSS publico NO encontrado (se puede copiar de src\views\css\)
)

echo.
echo ================================================
echo   Resumen de Verificacion Completado
echo ================================================
echo.
pause
