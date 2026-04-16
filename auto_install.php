<?php

declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'installer_common.php';

function dbConfigFromInput(array $input, array $defaults): array
{
    return [
        'hostname' => trim((string) ($input['hostname'] ?? $defaults['hostname'])),
        'port'     => trim((string) ($input['port'] ?? $defaults['port'])),
        'username' => trim((string) ($input['username'] ?? $defaults['username'])),
        'password' => (string) ($input['password'] ?? $defaults['password']),
        'database' => trim((string) ($input['database'] ?? $defaults['database'])),
    ];
}

function adminConfigFromInput(array $input): array
{
    return [
        'create_admin' => isset($input['create_admin']) && (string) $input['create_admin'] === '1',
        'alias'        => trim((string) ($input['admin_alias'] ?? 'Administrador')),
        'email'        => trim((string) ($input['admin_email'] ?? 'admin@local.dev')),
        'password'     => (string) ($input['admin_password'] ?? ''),
        'password_confirm' => (string) ($input['admin_password_confirm'] ?? ''),
    ];
}

function validateAdminConfig(array $adminConfig): void
{
    if (! ($adminConfig['create_admin'] ?? false)) {
        return;
    }

    $password = (string) ($adminConfig['password'] ?? '');
    $passwordConfirm = (string) ($adminConfig['password_confirm'] ?? '');

    if ($password === '') {
        throw new RuntimeException('La contrasena del administrador es obligatoria.');
    }

    if ($password !== $passwordConfirm) {
        throw new RuntimeException('La confirmacion de contrasena del administrador no coincide.');
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function appBaseUrlFromEnv(string $envPath): string
{
    $default = '/';
    if (! is_file($envPath)) {
        return $default;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return $default;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || ! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        if (trim($key) !== 'app.baseURL') {
            continue;
        }

        $clean = trim(trim($value), " \t\n\r\0\x0B'\"");
        return $clean !== '' ? $clean : $default;
    }

    return $default;
}

$envPath = envFilePath();
$defaults = readEnvDatabaseDefaults($envPath);

if (isCli()) {
    echo PHP_EOL;
    echo "=== Asistente de Instalacion (Proyecto Nuevo) ===" . PHP_EOL;

    cliSetup:
    $dbConfig = [
        'hostname' => prompt('Servidor MySQL', $defaults['hostname']),
        'port'     => prompt('Puerto MySQL', $defaults['port']),
        'username' => prompt('Usuario MySQL', $defaults['username']),
        'password' => prompt('Contrasena MySQL (deja vacio para ninguna)', $defaults['password']),
        'database' => prompt('Nombre de base de datos', $defaults['database']),
    ];

    $adminConfig = [
        'create_admin' => promptYesNo('Deseas crear un usuario administrador?', false),
        'alias'        => 'Administrador',
        'email'        => 'admin@local.dev',
        'password'     => '',
        'password_confirm' => '',
    ];

    if ($adminConfig['create_admin']) {
        $adminConfig['alias'] = prompt('Alias del administrador', 'Administrador');
        $adminConfig['email'] = prompt('Email del administrador', 'admin@local.dev');
        $adminConfig['password'] = prompt('Contrasena del administrador (minimo 8 caracteres)', '');
        $adminConfig['password_confirm'] = prompt('Confirmar contrasena del administrador', '');

        if ($adminConfig['password'] !== $adminConfig['password_confirm']) {
            echo 'Las contrasenas no coinciden. Vuelve a intentarlo.' . PHP_EOL;
            goto cliSetup;
        }
    }

    echo PHP_EOL;
    echo "Resumen de configuracion:" . PHP_EOL;
    echo "- Host: {$dbConfig['hostname']}" . PHP_EOL;
    echo "- Puerto: {$dbConfig['port']}" . PHP_EOL;
    echo "- Usuario: {$dbConfig['username']}" . PHP_EOL;
    echo "- Base de datos: {$dbConfig['database']}" . PHP_EOL;
    echo "- Crear admin: " . ($adminConfig['create_admin'] ? 'Si' : 'No') . PHP_EOL;
    if ($adminConfig['create_admin']) {
        echo "- Email admin: {$adminConfig['email']}" . PHP_EOL;
    }

    if (! promptYesNo('Deseas continuar con esta configuracion?', true)) {
        if (promptYesNo('Deseas volver a capturar los datos?', true)) {
            goto cliSetup;
        }

        echo "Instalacion cancelada por usuario." . PHP_EOL;
        exit(0);
    }

    try {
        validateAdminConfig($adminConfig);

        updateEnvDatabaseSettings($envPath, $dbConfig);
        echo "Archivo .env actualizado correctamente." . PHP_EOL;

        ensureDatabaseExists($dbConfig);
        echo "Base de datos verificada/creada correctamente." . PHP_EOL;

        echo PHP_EOL . "Ejecutando migraciones..." . PHP_EOL;
        $exitCode = runSparkMigrate(projectRoot());
        if ($exitCode !== 0) {
            echo "Fallo la ejecucion de migraciones. Codigo: {$exitCode}" . PHP_EOL;
            exit($exitCode);
        }

        $syncResult = syncAdministradorRolePermisos($dbConfig);
        echo "Permisos del rol Administrador sincronizados. Total: {$syncResult['total']}, insertados: {$syncResult['insertados']}, restaurados: {$syncResult['restaurados']}, activos: {$syncResult['ya_activos']}" . PHP_EOL;

        if ($adminConfig['create_admin']) {
            createAdminUser($dbConfig, [
                'alias'    => $adminConfig['alias'],
                'email'    => $adminConfig['email'],
                'password' => $adminConfig['password'],
            ]);
            echo "Usuario administrador creado correctamente." . PHP_EOL;
        }

        echo PHP_EOL . "Instalacion completada correctamente." . PHP_EOL;
        exit(0);
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

$status = '';
$statusType = 'info';
$migrateOutput = '';
$dbConfig = dbConfigFromInput($_POST, $defaults);
$adminConfig = adminConfigFromInput($_POST);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        validateAdminConfig($adminConfig);

        updateEnvDatabaseSettings($envPath, $dbConfig);
        ensureDatabaseExists($dbConfig);
        $result = runSparkMigrateWithOutput(projectRoot());

        $migrateOutput = $result['output'];
        if ($result['exitCode'] !== 0) {
            $statusType = 'danger';
            $status = 'Fallo la ejecucion de migraciones. Codigo: ' . $result['exitCode'];
        } else {
            $syncResult = syncAdministradorRolePermisos($dbConfig);
            $migrateOutput .= PHP_EOL . 'Permisos rol Administrador sincronizados: total=' . $syncResult['total'] . ', insertados=' . $syncResult['insertados'] . ', restaurados=' . $syncResult['restaurados'] . ', activos=' . $syncResult['ya_activos'];

            if ($adminConfig['create_admin']) {
                createAdminUser($dbConfig, [
                    'alias'    => $adminConfig['alias'],
                    'email'    => $adminConfig['email'],
                    'password' => $adminConfig['password'],
                ]);
                $migrateOutput .= PHP_EOL . 'Usuario administrador creado: ' . $adminConfig['email'];
            }

            $baseUrl = rtrim(appBaseUrlFromEnv($envPath), '/');
            $loginUrl = ($baseUrl !== '' ? $baseUrl : '') . '/login';

            if (! headers_sent()) {
                header('Location: ' . $loginUrl, true, 302);
                exit;
            }

            $statusType = 'success';
            $status = 'Instalacion completada correctamente. Redirigiendo a login...';
            $migrateOutput .= PHP_EOL . 'Redirect: ' . $loginUrl;
        }
    } catch (Throwable $e) {
        $statusType = 'danger';
        $status = 'Error: ' . $e->getMessage();
    }
}

?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistente de Instalacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Asistente de Instalacion</h1>
            <p class="text-muted">Configura base de datos, crea la BD si no existe y ejecuta migraciones.</p>

            <?php if ($status !== ''): ?>
                <div class="alert alert-<?= h($statusType) ?>"><?= h($status) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Servidor MySQL</label>
                        <input type="text" name="hostname" class="form-control" value="<?= h($dbConfig['hostname']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Puerto</label>
                        <input type="text" name="port" class="form-control" value="<?= h($dbConfig['port']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-control" value="<?= h($dbConfig['username']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contrasena</label>
                        <input type="password" name="password" class="form-control" value="<?= h($dbConfig['password']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Base de datos</label>
                        <input type="text" name="database" class="form-control" value="<?= h($dbConfig['database']) ?>" required>
                    </div>
                </div>
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="create_admin" name="create_admin" value="1" <?= $adminConfig['create_admin'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="create_admin">Deseo crear un usuario administrador al finalizar la instalacion</label>
                </div>
                <div id="admin_fields" class="row g-3 mt-1" style="display: <?= $adminConfig['create_admin'] ? 'flex' : 'none' ?>;">
                    <div class="col-md-4">
                        <label class="form-label">Alias administrador</label>
                        <input type="text" name="admin_alias" class="form-control" maxlength="50" value="<?= h($adminConfig['alias']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email administrador</label>
                        <input type="email" name="admin_email" class="form-control" maxlength="100" value="<?= h($adminConfig['email']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contrasena administrador</label>
                        <div class="input-group">
                            <input type="password" id="admin_password" name="admin_password" class="form-control" minlength="8">
                            <button type="button" class="btn btn-outline-secondary" id="toggleAdminPassword" aria-label="Mostrar u ocultar contrasena de administrador">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Confirmar contrasena administrador</label>
                        <div class="input-group">
                            <input type="password" id="admin_password_confirm" name="admin_password_confirm" class="form-control" minlength="8">
                            <button type="button" class="btn btn-outline-secondary" id="toggleAdminPasswordConfirm" aria-label="Mostrar u ocultar confirmacion de contrasena">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Ejecutar instalacion</button>
                </div>
            </form>

            <?php if ($migrateOutput !== ''): ?>
                <hr>
                <h2 class="h6">Salida de migraciones</h2>
                <pre class="bg-dark text-light p-3 rounded small"><?= h($migrateOutput) ?></pre>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
const createAdminCheckbox = document.getElementById('create_admin');
const adminFields = document.getElementById('admin_fields');
const adminPasswordInput = document.getElementById('admin_password');
const adminPasswordConfirmInput = document.getElementById('admin_password_confirm');
const toggleAdminPassword = document.getElementById('toggleAdminPassword');
const toggleAdminPasswordConfirm = document.getElementById('toggleAdminPasswordConfirm');

if (createAdminCheckbox && adminFields) {
    createAdminCheckbox.addEventListener('change', function () {
        adminFields.style.display = this.checked ? 'flex' : 'none';
    });
}

function bindPasswordToggle(input, button) {
    if (!input || !button) {
        return;
    }

    button.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
}

bindPasswordToggle(adminPasswordInput, toggleAdminPassword);
bindPasswordToggle(adminPasswordConfirmInput, toggleAdminPasswordConfirm);
</script>
</body>
</html>
