# GERO - Plataforma de Inventario & Presupuesto

## Instalacion en Proyecto Nuevo

1. **Clonar el repositorio:** `C:\xampp\htdocs\portal-finanzas`.
2. **Configurar .env:**
   - Copia `env` a `.env`.
   - Define `app.baseURL = 'http://localhost/portal-finanzas/public'`.
   - Configura las credenciales en `database.default.*`.
3. **Instalar dependencias:** `composer update`.

## Asistentes Automaticos (Instalacion y Actualizacion)

Se incluyen dos asistentes CLI para evitar errores de configuracion manual:

1. `auto_install.php`: pensado para una instalacion nueva.
2. `auto_update.php`: pensado para ambientes ya instalados.

### Instalacion Nueva (recomendada)

```powershell
C:\xampp\php\php.exe auto_install.php
```

Modo web:

```text
http://localhost/portal-finanzas/auto_install.php
```

El asistente solicita:
1. Host MySQL
2. Puerto
3. Usuario
4. Contrasena
5. Nombre de la base de datos

Luego:
1. Actualiza `.env` con `database.default.*`
2. Crea la base si no existe
3. Ejecuta todas las migraciones

### Actualizacion de una Instalacion Existente

```powershell
C:\xampp\php\php.exe auto_update.php
```

Modo web:

```text
http://localhost/portal-finanzas/auto_update.php
```

El asistente:
1. Pide/actualiza credenciales de conexion
2. Detecta migraciones instaladas (tabla `migrations`)
3. Detecta migraciones pendientes (archivos en `app/Database/Migrations` no aplicados)
4. Muestra el estado y solicita confirmacion para aplicar pendientes

## Bootstrap de Base de Datos (Recomendado)

La forma recomendada es usar el script de bootstrap, que:
1. Crea la base de datos si no existe.
2. Ejecuta todas las migraciones.

### Opcion A - PowerShell (recomendada)

```powershell
powershell -ExecutionPolicy Bypass -File scripts/bootstrap-db.ps1
```

### Opcion B - CMD/BAT

```bat
scripts\bootstrap-db.bat
```

### Opcion C - Manual

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS db_gero_finanzas CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
C:\xampp\php\php.exe spark migrate
```

## Configuracion Opcional de Scripts

Si tu entorno no usa rutas por defecto de XAMPP:
1. Edita `scripts/bootstrap-db.ps1` y actualiza `MySqlExe` / `PhpExe`.
2. O edita `scripts/bootstrap-db.bat` y actualiza `MYSQL_EXE` / `PHP_EXE`.

## Actualizacion de Esquema en Desarrollo

Despues del bootstrap inicial, para nuevos cambios de esquema solo ejecuta:

```powershell
C:\xampp\php\php.exe spark migrate
```

## Comandos Útiles
Para iniciar el servidor local:
`C:\xampp\php\php.exe spark serve`

## Flujo de Trabajo
- Crear rama: `feature/nombre-modulo`.
- No hacer push a `main` sin Pull Request aprobado.