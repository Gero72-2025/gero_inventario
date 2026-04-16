@echo off
setlocal

set "MYSQL_EXE=C:\xampp\mysql\bin\mysql.exe"
set "PHP_EXE=C:\xampp\php\php.exe"
set "DB_HOST=localhost"
set "DB_PORT=3306"
set "DB_NAME=db_gero_finanzas"
set "DB_USER=root"
set "DB_PASSWORD="

if not exist "%MYSQL_EXE%" (
  echo No se encontro mysql.exe en: %MYSQL_EXE%
  exit /b 1
)

if not exist "%PHP_EXE%" (
  echo No se encontro php.exe en: %PHP_EXE%
  exit /b 1
)

echo [1/2] Creando base de datos si no existe: %DB_NAME%
"%MYSQL_EXE%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -e "CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
if errorlevel 1 (
  echo Error al crear la base de datos.
  exit /b 1
)

echo [2/2] Ejecutando migraciones
"%PHP_EXE%" spark migrate
if errorlevel 1 (
  echo Error al ejecutar migraciones.
  exit /b 1
)

echo Bootstrap completado correctamente.
exit /b 0
