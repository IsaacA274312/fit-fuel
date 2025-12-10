@echo off
echo ================================================
echo   FitAndFuel - Script de Instalacion
echo ================================================
echo.

REM Verificar si Node.js esta instalado
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Node.js no esta instalado.
    echo Por favor, instala Node.js desde https://nodejs.org/
    pause
    exit /b 1
)

REM Verificar si npm esta instalado
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] npm no esta instalado.
    pause
    exit /b 1
)

echo [OK] Node.js y npm detectados
echo.

REM Instalar dependencias
echo Instalando dependencias de Node.js...
call npm install
if %errorlevel% neq 0 (
    echo [ERROR] Error al instalar dependencias
    pause
    exit /b 1
)

echo.
echo [OK] Dependencias instaladas correctamente
echo.

echo ================================================
echo   Instrucciones siguientes:
echo ================================================
echo.
echo 1. Asegurate de que XAMPP este corriendo
echo    - Apache
echo    - MySQL
echo.
echo 2. Importa la base de datos:
echo    - Abre phpMyAdmin: http://localhost/phpmyadmin
echo    - Ejecuta el script: db/fit-fuel.sql
echo    - O usa: mysql -u root -p ^< db/fit-fuel.sql
echo.
echo 3. Si ya tienes una tabla usuarios, ejecuta:
echo    - db/migrations/001_update_usuarios_table.sql
echo.
echo 4. Verifica el archivo .env con tus credenciales MySQL
echo.
echo 5. Inicia el servidor:
echo    - npm run dev (desarrollo)
echo    - npm start (produccion)
echo.
echo 6. Accede a la aplicacion:
echo    - http://localhost/fitandfuel/src/public/index.html
echo.
echo ================================================
pause
