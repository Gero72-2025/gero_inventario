<?php

declare(strict_types=1);

/**
 * Shared helpers for installation/update assistants.
 */
function isCli(): bool
{
    return PHP_SAPI === 'cli';
}

function projectRoot(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . '..';
}

function envFilePath(): string
{
    return projectRoot() . DIRECTORY_SEPARATOR . '.env';
}

function prompt(string $label, ?string $default = null): string
{
    $suffix = $default !== null ? " [$default]" : '';
    fwrite(STDOUT, $label . $suffix . ': ');
    $input = fgets(STDIN);
    if ($input === false) {
        return $default ?? '';
    }

    $value = trim($input);
    if ($value === '' && $default !== null) {
        return $default;
    }

    return $value;
}

function promptYesNo(string $label, bool $defaultYes = true): bool
{
    $default = $defaultYes ? 'Y/n' : 'y/N';
    $answer = strtolower(prompt($label . " ($default)", ''));

    if ($answer === '') {
        return $defaultYes;
    }

    return in_array($answer, ['y', 'yes', 's', 'si'], true);
}

function readEnvDatabaseDefaults(string $envPath): array
{
    $defaults = [
        'hostname' => 'localhost',
        'port'     => '3306',
        'username' => 'root',
        'password' => '',
        'database' => 'db_gero_finanzas',
    ];

    if (! is_file($envPath)) {
        return $defaults;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return $defaults;
    }

    $map = [
        'database.default.hostname' => 'hostname',
        'database.default.port'     => 'port',
        'database.default.username' => 'username',
        'database.default.password' => 'password',
        'database.default.database' => 'database',
    ];

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        if (! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (! array_key_exists($key, $map)) {
            continue;
        }

        $defaults[$map[$key]] = trim($value, " \t\n\r\0\x0B'\"");
    }

    return $defaults;
}

function updateEnvDatabaseSettings(string $envPath, array $dbConfig): void
{
    $settings = [
        'database.default.hostname' => $dbConfig['hostname'],
        'database.default.database' => $dbConfig['database'],
        'database.default.username' => $dbConfig['username'],
        'database.default.password' => $dbConfig['password'],
        'database.default.DBDriver' => 'MySQLi',
        'database.default.DBPrefix' => '',
        'database.default.port'     => $dbConfig['port'],
    ];

    $existing = is_file($envPath) ? file($envPath, FILE_IGNORE_NEW_LINES) : [];
    if ($existing === false) {
        throw new RuntimeException('No fue posible leer el archivo .env.');
    }

    $updated = [];
    $writtenKeys = [];

    foreach ($existing as $line) {
        $newLine = $line;
        if (str_contains($line, '=')) {
            [$key] = explode('=', $line, 2);
            $trimmedKey = trim($key);
            if (array_key_exists($trimmedKey, $settings)) {
                $newLine = $trimmedKey . ' = ' . $settings[$trimmedKey];
                $writtenKeys[$trimmedKey] = true;
            }
        }
        $updated[] = $newLine;
    }

    foreach ($settings as $key => $value) {
        if (! isset($writtenKeys[$key])) {
            $updated[] = $key . ' = ' . $value;
        }
    }

    $result = file_put_contents($envPath, implode(PHP_EOL, $updated) . PHP_EOL);
    if ($result === false) {
        throw new RuntimeException('No fue posible actualizar el archivo .env.');
    }
}

function ensureDatabaseExists(array $dbConfig): void
{
    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('No fue posible inicializar MySQLi.');
    }

    $connected = @$mysqli->real_connect(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        null,
        (int) $dbConfig['port']
    );

    if (! $connected) {
        throw new RuntimeException('No fue posible conectar a MySQL: ' . mysqli_connect_error());
    }

    $dbName = $mysqli->real_escape_string($dbConfig['database']);
    $sql = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";

    if (! $mysqli->query($sql)) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('Error al crear la base de datos: ' . $error);
    }

    $mysqli->close();
}

function createAdminUser(array $dbConfig, array $adminData): void
{
    $alias = trim((string) ($adminData['alias'] ?? ''));
    $email = trim((string) ($adminData['email'] ?? ''));
    $password = (string) ($adminData['password'] ?? '');

    if ($alias === '') {
        throw new RuntimeException('El alias del administrador es obligatorio.');
    }

    if (mb_strlen($alias) > 50) {
        throw new RuntimeException('El alias del administrador no puede exceder 50 caracteres.');
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('El email del administrador no es valido.');
    }

    if (mb_strlen($email) > 100) {
        throw new RuntimeException('El email del administrador no puede exceder 100 caracteres.');
    }

    if (mb_strlen($password) < 8) {
        throw new RuntimeException('La contrasena del administrador debe tener al menos 8 caracteres.');
    }

    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('No fue posible inicializar MySQLi.');
    }

    $connected = @$mysqli->real_connect(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'],
        (int) $dbConfig['port']
    );

    if (! $connected) {
        throw new RuntimeException('No fue posible conectar a MySQL: ' . mysqli_connect_error());
    }

    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'usuarios'");
    if (! $tableCheck || $tableCheck->num_rows === 0) {
        $mysqli->close();
        throw new RuntimeException('La tabla usuarios no existe. Ejecuta migraciones antes de crear el administrador.');
    }

    $rolesTableCheck = $mysqli->query("SHOW TABLES LIKE 'cat_roles'");
    if (! $rolesTableCheck || $rolesTableCheck->num_rows === 0) {
        $mysqli->close();
        throw new RuntimeException('La tabla cat_roles no existe. Ejecuta migraciones antes de crear el administrador.');
    }

    $rolAdminStmt = $mysqli->prepare("SELECT id FROM cat_roles WHERE nombre_rol = 'Administrador' AND deleted_at IS NULL LIMIT 1");
    if ($rolAdminStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar consulta de rol Administrador: ' . $error);
    }

    if (! $rolAdminStmt->execute()) {
        $error = $rolAdminStmt->error;
        $rolAdminStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible consultar el rol Administrador: ' . $error);
    }

    $rolAdminResult = $rolAdminStmt->get_result();
    $rolAdminRow = $rolAdminResult !== false ? $rolAdminResult->fetch_assoc() : null;
    $rolAdminStmt->close();

    if (! is_array($rolAdminRow) || ! isset($rolAdminRow['id'])) {
        $mysqli->close();
        throw new RuntimeException('No existe el rol Administrador en cat_roles.');
    }

    $rolAdminId = (int) $rolAdminRow['id'];

    $existsStmt = $mysqli->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    if ($existsStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar validacion de email: ' . $error);
    }

    $existsStmt->bind_param('s', $email);
    if (! $existsStmt->execute()) {
        $error = $existsStmt->error;
        $existsStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible validar el email del administrador: ' . $error);
    }

    $existsResult = $existsStmt->get_result();
    if ($existsResult !== false && $existsResult->num_rows > 0) {
        $existsStmt->close();
        $mysqli->close();
        throw new RuntimeException('Ya existe un usuario con ese email.');
    }
    $existsStmt->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        $mysqli->close();
        throw new RuntimeException('No fue posible generar el hash de la contrasena.');
    }

    $insertStmt = $mysqli->prepare('INSERT INTO usuarios (alias, email, password, token_recuperacion, esta_conectado, ultimo_login, id_rol, id_usuario, created_at, updated_at, deleted_at) VALUES (?, ?, ?, NULL, 0, NULL, ?, NULL, NOW(), NOW(), NULL)');
    if ($insertStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar insercion del administrador: ' . $error);
    }

    $insertStmt->bind_param('sssi', $alias, $email, $hashedPassword, $rolAdminId);
    if (! $insertStmt->execute()) {
        $error = $insertStmt->error;
        $insertStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible crear el usuario administrador: ' . $error);
    }

    $newId = (int) $mysqli->insert_id;
    $insertStmt->close();

    $updateAuditStmt = $mysqli->prepare('UPDATE usuarios SET id_usuario = ? WHERE id = ?');
    if ($updateAuditStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar actualizacion de auditoria: ' . $error);
    }

    $updateAuditStmt->bind_param('ii', $newId, $newId);
    if (! $updateAuditStmt->execute()) {
        $error = $updateAuditStmt->error;
        $updateAuditStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible actualizar auditoria del administrador: ' . $error);
    }

    $updateAuditStmt->close();
    $mysqli->close();
}

function syncSystemPermisos(array $dbConfig): array
{
    $accionesSistema = discoverSystemControllerActions();

    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('No fue posible inicializar MySQLi.');
    }

    $connected = @$mysqli->real_connect(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'],
        (int) $dbConfig['port']
    );

    if (! $connected) {
        throw new RuntimeException('No fue posible conectar a MySQL: ' . mysqli_connect_error());
    }

    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'permisos'");
    if (! $tableCheck || $tableCheck->num_rows === 0) {
        $mysqli->close();
        throw new RuntimeException('La tabla permisos no existe. Ejecuta migraciones antes de sincronizar acciones del sistema.');
    }

    $existingResult = $mysqli->query('SELECT id, nombre_permiso, deleted_at FROM permisos');
    if (! $existingResult) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible consultar permisos existentes: ' . $error);
    }

    $existing = [];
    while ($row = $existingResult->fetch_assoc()) {
        $nombrePermiso = (string) ($row['nombre_permiso'] ?? '');
        if ($nombrePermiso === '') {
            continue;
        }

        $existing[$nombrePermiso] = [
            'id'         => (int) ($row['id'] ?? 0),
            'deleted_at' => $row['deleted_at'] ?? null,
        ];
    }
    $existingResult->free();

    $insertStmt = $mysqli->prepare('INSERT INTO permisos (nombre_permiso, controlador, accion, id_usuario, created_at, updated_at, deleted_at) VALUES (?, ?, ?, NULL, NOW(), NOW(), NULL)');
    if ($insertStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar insercion en permisos: ' . $error);
    }

    $updateStmt = $mysqli->prepare('UPDATE permisos SET controlador = ?, accion = ?, deleted_at = NULL, updated_at = NOW(), id_usuario = NULL WHERE id = ?');
    if ($updateStmt === false) {
        $error = $mysqli->error;
        $insertStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar actualizacion en permisos: ' . $error);
    }

    $insertados = 0;
    $restaurados = 0;
    $yaActivos = 0;

    foreach ($accionesSistema as $accionSistema) {
        $nombrePermiso = (string) ($accionSistema['nombre_permiso'] ?? '');
        $controlador = (string) ($accionSistema['controlador'] ?? '');
        $accion = (string) ($accionSistema['accion'] ?? '');

        if ($nombrePermiso === '' || $controlador === '' || $accion === '') {
            continue;
        }

        if (isset($existing[$nombrePermiso])) {
            $registro = $existing[$nombrePermiso];
            if (! empty($registro['deleted_at'])) {
                $idPermiso = (int) $registro['id'];
                $updateStmt->bind_param('ssi', $controlador, $accion, $idPermiso);
                if (! $updateStmt->execute()) {
                    $error = $updateStmt->error;
                    $updateStmt->close();
                    $insertStmt->close();
                    $mysqli->close();
                    throw new RuntimeException('No fue posible restaurar permiso ' . $nombrePermiso . ': ' . $error);
                }
                $restaurados++;
            } else {
                $yaActivos++;
            }
            continue;
        }

        $insertStmt->bind_param('sss', $nombrePermiso, $controlador, $accion);
        if (! $insertStmt->execute()) {
            $error = $insertStmt->error;
            $updateStmt->close();
            $insertStmt->close();
            $mysqli->close();
            throw new RuntimeException('No fue posible insertar permiso ' . $nombrePermiso . ': ' . $error);
        }

        $insertados++;
    }

    $updateStmt->close();
    $insertStmt->close();
    $mysqli->close();

    return [
        'detectados'   => count($accionesSistema),
        'insertados'   => $insertados,
        'restaurados'  => $restaurados,
        'ya_activos'   => $yaActivos,
    ];
}

function discoverSystemControllerActions(): array
{
    $controllersPath = projectRoot() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers';
    if (! is_dir($controllersPath)) {
        return [];
    }

    $baseControllerPath = $controllersPath . DIRECTORY_SEPARATOR . 'BaseController.php';
    $baseMethods = is_file($baseControllerPath) ? parsePublicMethodsFromPhpFile($baseControllerPath) : [];

    $acciones = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($controllersPath, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if (! $fileInfo instanceof SplFileInfo) {
            continue;
        }

        if (strtolower((string) $fileInfo->getExtension()) !== 'php') {
            continue;
        }

        $realPath = (string) $fileInfo->getRealPath();
        if ($realPath === '' || str_ends_with($realPath, DIRECTORY_SEPARATOR . 'BaseController.php')) {
            continue;
        }

        $controllerName = pathinfo($realPath, PATHINFO_FILENAME);
        $methods = parsePublicMethodsFromPhpFile($realPath);

        foreach ($methods as $methodName) {
            if (str_starts_with($methodName, '__')) {
                continue;
            }

            if (in_array($methodName, $baseMethods, true)) {
                continue;
            }

            $nombrePermiso = $controllerName . '::' . $methodName;
            $acciones[$nombrePermiso] = [
                'nombre_permiso' => $nombrePermiso,
                'controlador'    => $controllerName,
                'accion'         => $methodName,
            ];
        }
    }

    ksort($acciones);
    return array_values($acciones);
}

function parsePublicMethodsFromPhpFile(string $filePath): array
{
    if (! is_file($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        return [];
    }

    $matches = [];
    preg_match_all('/public\s+function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $content, $matches);

    if (! isset($matches[1]) || ! is_array($matches[1])) {
        return [];
    }

    return array_values(array_unique(array_map(static fn(string $name): string => trim($name), $matches[1])));
}

function syncAdministradorRolePermisos(array $dbConfig): array
{
    $syncPermisos = syncSystemPermisos($dbConfig);

    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('No fue posible inicializar MySQLi.');
    }

    $connected = @$mysqli->real_connect(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'],
        (int) $dbConfig['port']
    );

    if (! $connected) {
        throw new RuntimeException('No fue posible conectar a MySQL: ' . mysqli_connect_error());
    }

    $requiredTables = ['cat_roles', 'permisos', 'rol_permisos'];
    foreach ($requiredTables as $tableName) {
        $tableCheck = $mysqli->query("SHOW TABLES LIKE '" . $mysqli->real_escape_string($tableName) . "'");
        if (! $tableCheck || $tableCheck->num_rows === 0) {
            $mysqli->close();
            throw new RuntimeException('La tabla ' . $tableName . ' no existe. Ejecuta migraciones antes de sincronizar permisos.');
        }
    }

    $rolAdminStmt = $mysqli->prepare("SELECT id FROM cat_roles WHERE nombre_rol = 'Administrador' AND deleted_at IS NULL LIMIT 1");
    if ($rolAdminStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar consulta del rol Administrador: ' . $error);
    }

    if (! $rolAdminStmt->execute()) {
        $error = $rolAdminStmt->error;
        $rolAdminStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible obtener el rol Administrador: ' . $error);
    }

    $rolAdminResult = $rolAdminStmt->get_result();
    $rolAdminRow = $rolAdminResult !== false ? $rolAdminResult->fetch_assoc() : null;
    $rolAdminStmt->close();

    if (! is_array($rolAdminRow) || ! isset($rolAdminRow['id'])) {
        $mysqli->close();
        throw new RuntimeException('No existe el rol Administrador activo en cat_roles.');
    }

    $idRolAdmin = (int) $rolAdminRow['id'];

    $permisosResult = $mysqli->query('SELECT id FROM permisos WHERE deleted_at IS NULL');
    if (! $permisosResult) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible consultar permisos activos: ' . $error);
    }

    $idsPermiso = [];
    while ($row = $permisosResult->fetch_assoc()) {
        $idsPermiso[] = (int) $row['id'];
    }
    $permisosResult->free();

    $existentesResult = $mysqli->query('SELECT id, id_permiso, deleted_at FROM rol_permisos WHERE id_rol = ' . $idRolAdmin);
    if (! $existentesResult) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible consultar permisos del rol Administrador: ' . $error);
    }

    $existentes = [];
    while ($row = $existentesResult->fetch_assoc()) {
        $idPermiso = (int) ($row['id_permiso'] ?? 0);
        if ($idPermiso <= 0) {
            continue;
        }

        $existentes[$idPermiso] = [
            'id'         => (int) ($row['id'] ?? 0),
            'deleted_at' => $row['deleted_at'] ?? null,
        ];
    }
    $existentesResult->free();

    $insertStmt = $mysqli->prepare('INSERT INTO rol_permisos (id_rol, id_permiso, id_usuario, created_at, updated_at, deleted_at) VALUES (?, ?, NULL, NOW(), NOW(), NULL)');
    if ($insertStmt === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar insercion en rol_permisos: ' . $error);
    }

    $restoreStmt = $mysqli->prepare('UPDATE rol_permisos SET deleted_at = NULL, updated_at = NOW(), id_usuario = NULL WHERE id = ?');
    if ($restoreStmt === false) {
        $error = $mysqli->error;
        $insertStmt->close();
        $mysqli->close();
        throw new RuntimeException('No fue posible preparar restauracion en rol_permisos: ' . $error);
    }

    $insertados = 0;
    $restaurados = 0;
    $yaActivos = 0;

    foreach ($idsPermiso as $idPermiso) {
        if (isset($existentes[$idPermiso])) {
            $registro = $existentes[$idPermiso];
            if (! empty($registro['deleted_at'])) {
                $idRolPermiso = (int) $registro['id'];
                $restoreStmt->bind_param('i', $idRolPermiso);
                if (! $restoreStmt->execute()) {
                    $error = $restoreStmt->error;
                    $restoreStmt->close();
                    $insertStmt->close();
                    $mysqli->close();
                    throw new RuntimeException('No fue posible restaurar un permiso del rol Administrador: ' . $error);
                }
                $restaurados++;
            } else {
                $yaActivos++;
            }
            continue;
        }

        $insertStmt->bind_param('ii', $idRolAdmin, $idPermiso);
        if (! $insertStmt->execute()) {
            $error = $insertStmt->error;
            $restoreStmt->close();
            $insertStmt->close();
            $mysqli->close();
            throw new RuntimeException('No fue posible insertar un permiso para el rol Administrador: ' . $error);
        }

        $insertados++;
    }

    $restoreStmt->close();
    $insertStmt->close();
    $mysqli->close();

    return [
        'permisos_detectados'   => $syncPermisos['detectados'] ?? 0,
        'permisos_insertados'   => $syncPermisos['insertados'] ?? 0,
        'permisos_restaurados'  => $syncPermisos['restaurados'] ?? 0,
        'permisos_ya_activos'   => $syncPermisos['ya_activos'] ?? 0,
        'rol_id'      => $idRolAdmin,
        'total'       => count($idsPermiso),
        'insertados'  => $insertados,
        'restaurados' => $restaurados,
        'ya_activos'  => $yaActivos,
    ];
}

function runSparkMigrate(string $projectRoot): int
{
    $phpBinary = resolvePhpCliBinary();
    $command = escapeshellarg($phpBinary) . ' spark migrate';

    $currentDir = getcwd();
    chdir($projectRoot);
    passthru($command, $exitCode);
    if ($currentDir !== false) {
        chdir($currentDir);
    }

    return (int) $exitCode;
}

function runSparkMigrateWithOutput(string $projectRoot): array
{
    $phpBinary = resolvePhpCliBinary();
    $command = escapeshellarg($phpBinary) . ' spark migrate';

    $descriptorSpec = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes, $projectRoot);
    if (! is_resource($process)) {
        throw new RuntimeException('No fue posible iniciar el proceso de migracion.');
    }

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return [
        'exitCode' => (int) $exitCode,
        'output'   => (string) ($stdout . PHP_EOL . $stderr),
    ];
}

function resolvePhpCliBinary(): string
{
    $custom = getenv('INSTALLER_PHP_BINARY');
    if (is_string($custom) && $custom !== '') {
        return $custom;
    }

    $candidates = [];

    if (defined('PHP_BINARY') && is_string(PHP_BINARY) && PHP_BINARY !== '') {
        $candidates[] = PHP_BINARY;
    }

    if (DIRECTORY_SEPARATOR === '\\') {
        $candidates[] = 'C:\\xampp\\php\\php.exe';
        $candidates[] = 'php.exe';
    } else {
        $candidates[] = '/usr/bin/php';
        $candidates[] = '/usr/local/bin/php';
        $candidates[] = 'php';
    }

    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }

        if (str_contains($candidate, 'httpd')) {
            continue;
        }

        if (is_file($candidate)) {
            return $candidate;
        }

        if (! str_contains($candidate, DIRECTORY_SEPARATOR)) {
            return $candidate;
        }
    }

    throw new RuntimeException('No se encontro un ejecutable PHP CLI valido. Define INSTALLER_PHP_BINARY con la ruta de php.exe.');
}

function migrationFiles(string $migrationsPath): array
{
    if (! is_dir($migrationsPath)) {
        return [];
    }

    $files = glob($migrationsPath . DIRECTORY_SEPARATOR . '*.php');
    if ($files === false) {
        return [];
    }

    $list = [];
    foreach ($files as $file) {
        $filename = basename($file);
        if (! preg_match('/^(\d{4}-\d{2}-\d{2}-\d{6})_(.+)\.php$/', $filename, $matches)) {
            continue;
        }

        $className = detectClassName($file);
        $list[] = [
            'version'  => $matches[1],
            'name'     => $matches[2],
            'class'    => $className,
            'filename' => $filename,
        ];
    }

    usort($list, static fn(array $a, array $b): int => strcmp($a['version'], $b['version']));

    return $list;
}

function detectClassName(string $file): string
{
    $contents = file_get_contents($file);
    if ($contents === false) {
        return '';
    }

    if (preg_match('/class\s+([A-Za-z_][A-Za-z0-9_]*)/', $contents, $matches) !== 1) {
        return '';
    }

    return $matches[1];
}

function installedMigrations(array $dbConfig): array
{
    $mysqli = mysqli_init();
    if ($mysqli === false) {
        throw new RuntimeException('No fue posible inicializar MySQLi.');
    }

    $connected = @$mysqli->real_connect(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'],
        (int) $dbConfig['port']
    );

    if (! $connected) {
        throw new RuntimeException('No fue posible conectar a MySQL: ' . mysqli_connect_error());
    }

    $result = $mysqli->query("SHOW TABLES LIKE 'migrations'");
    if (! $result || $result->num_rows === 0) {
        $mysqli->close();
        return [];
    }

    $query = $mysqli->query('SELECT version, class, batch FROM migrations ORDER BY version ASC');
    if ($query === false) {
        $error = $mysqli->error;
        $mysqli->close();
        throw new RuntimeException('No fue posible leer la tabla migrations: ' . $error);
    }

    $rows = [];
    while ($row = $query->fetch_assoc()) {
        $rows[] = [
            'version' => (string) ($row['version'] ?? ''),
            'class'   => (string) ($row['class'] ?? ''),
            'batch'   => (string) ($row['batch'] ?? ''),
        ];
    }

    $query->free();
    $mysqli->close();

    return $rows;
}

function printMigrationStatus(array $files, array $installed): array
{
    $installedByVersion = [];
    $installedByClassBase = [];

    foreach ($installed as $row) {
        $installedByVersion[$row['version']] = $row;
        $classBase = classBasename($row['class']);
        if ($classBase !== '') {
            $installedByClassBase[$classBase] = $row;
        }
    }

    $pending = [];

    fwrite(STDOUT, PHP_EOL . 'Estado de migraciones:' . PHP_EOL);
    fwrite(STDOUT, str_repeat('-', 90) . PHP_EOL);
    fwrite(STDOUT, sprintf("%-20s %-40s %-12s\n", 'Version', 'Clase', 'Estado'));
    fwrite(STDOUT, str_repeat('-', 90) . PHP_EOL);

    foreach ($files as $file) {
        $isInstalled = isset($installedByVersion[$file['version']]);
        if (! $isInstalled && $file['class'] !== '') {
            $isInstalled = isset($installedByClassBase[$file['class']]);
        }

        $status = $isInstalled ? 'INSTALADA' : 'PENDIENTE';
        if (! $isInstalled) {
            $pending[] = $file;
        }

        fwrite(STDOUT, sprintf("%-20s %-40s %-12s\n", $file['version'], $file['class'] ?: $file['name'], $status));
    }

    $fileVersions = array_column($files, 'version');
    $orphans = [];
    foreach ($installed as $row) {
        if (! in_array($row['version'], $fileVersions, true)) {
            $orphans[] = $row;
        }
    }

    if ($orphans !== []) {
        fwrite(STDOUT, str_repeat('-', 90) . PHP_EOL);
        fwrite(STDOUT, 'Migraciones instaladas sin archivo local (revisar):' . PHP_EOL);
        foreach ($orphans as $orphan) {
            fwrite(STDOUT, '- ' . $orphan['version'] . ' | ' . $orphan['class'] . PHP_EOL);
        }
    }

    fwrite(STDOUT, str_repeat('-', 90) . PHP_EOL);
    fwrite(STDOUT, 'Pendientes: ' . count($pending) . PHP_EOL . PHP_EOL);

    return $pending;
}

function classBasename(string $class): string
{
    $class = trim($class);
    if ($class === '') {
        return '';
    }

    $parts = explode('\\', $class);
    return end($parts) ?: '';
}
