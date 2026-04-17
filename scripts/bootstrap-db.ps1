param(
    [string]$MySqlExe = "C:\xampp\mysql\bin\mysql.exe",
    [string]$PhpExe = "C:\xampp\php\php.exe",
    [string]$DbHost = "localhost",
    [string]$DbPort = "3306",
    [string]$DbName = "db_gero_finanzas",
    [string]$DbUser = "root",
    [string]$DbPassword = ""
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path $MySqlExe)) {
    throw "No se encontro mysql.exe en la ruta: $MySqlExe"
}

if (-not (Test-Path $PhpExe)) {
    throw "No se encontro php.exe en la ruta: $PhpExe"
}

$createDbSql = "CREATE DATABASE IF NOT EXISTS ``$DbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

$mysqlArgs = @(
    "-h", $DbHost,
    "-P", $DbPort,
    "-u", $DbUser,
    "-e", $createDbSql
)

if ($DbPassword -ne "") {
    $mysqlArgs += "-p$DbPassword"
}

Write-Host "[1/2] Creando base de datos si no existe: $DbName"
& $MySqlExe @mysqlArgs

Write-Host "[2/2] Ejecutando migraciones"
Push-Location $PSScriptRoot
Push-Location ".."
& $PhpExe spark migrate
$exitCode = $LASTEXITCODE
Pop-Location
Pop-Location

if ($exitCode -ne 0) {
    throw "La ejecucion de migraciones fallo con codigo: $exitCode"
}

Write-Host "Bootstrap completado correctamente."
