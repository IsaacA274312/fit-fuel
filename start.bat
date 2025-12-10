@echo off
title FitAndFuel - Servidor de Desarrollo

echo ================================================
echo   FitAndFuel - Iniciando Servidor
echo ================================================
echo.

REM Verificar que node_modules exista
if not exist "node_modules" (
    echo [AVISO] Dependencias no instaladas
    echo Instalando dependencias...
    call npm install
    if %errorlevel% neq 0 (
        echo [ERROR] Error al instalar dependencias
        pause
        exit /b 1
    )
)

echo [INFO] Iniciando servidor Node.js en modo desarrollo...
echo.
echo Servidor corriendo en: http://localhost:3000
echo.
echo IMPORTANTE:
echo - Asegurate de que XAMPP este corriendo (Apache + MySQL)
echo - Accede a la aplicacion en:
echo   http://localhost/fitandfuel/src/public/index.html
echo.
echo Presiona Ctrl+C para detener el servidor
echo ================================================
echo.

REM Iniciar servidor
npm run dev
