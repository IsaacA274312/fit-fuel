@echo off
cls
echo ================================
echo   Creando estructura del backend
echo ================================

:: Crear carpeta principal (opcional)
:: mkdir ProyectoBackend
:: cd ProyectoBackend

:: Carpetas base
mkdir db
mkdir src

:: Dentro de src
cd src
mkdir controllers
mkdir models
mkdir routes
mkdir services
mkdir config
mkdir public
mkdir tests

:: Subcarpetas de public
cd public
mkdir css
mkdir js
mkdir images

:: Regresar a raíz del proyecto
cd ..
cd ..

echo =================================
echo   Estructura generada con exito!
echo =================================

pause
