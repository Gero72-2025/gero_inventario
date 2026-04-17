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

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function migrationStatusData(array $files, array $installed): array
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

    $rows = [];
    $pending = [];

    foreach ($files as $file) {
        $isInstalled = isset($installedByVersion[$file['version']]);
        if (! $isInstalled && $file['class'] !== '') {
            $isInstalled = isset($installedByClassBase[$file['class']]);
        }

        $rows[] = [
            'version' => $file['version'],
            'class'   => $file['class'] ?: $file['name'],
            'status'  => $isInstalled ? 'INSTALADA' : 'PENDIENTE',
        ];

        if (! $isInstalled) {
            $pending[] = $file;
        }
    }

    $fileVersions = array_column($files, 'version');
    $orphans = [];
    foreach ($installed as $row) {
        if (! in_array($row['version'], $fileVersions, true)) {
            $orphans[] = $row;
        }
    }

    return [
        'rows'    => $rows,
        'pending' => $pending,
        'orphans' => $orphans,
    ];
}

$envPath = envFilePath();
$defaults = readEnvDatabaseDefaults($envPath);

if (isCli()) {
    echo PHP_EOL;
    echo "=== Asistente de Actualizacion ===" . PHP_EOL;

    $dbConfig = [
        'hostname' => prompt('Servidor MySQL', $defaults['hostname']),
        'port'     => prompt('Puerto MySQL', $defaults['port']),
        'username' => prompt('Usuario MySQL', $defaults['username']),
        'password' => prompt('Contrasena MySQL (deja vacio para ninguna)', $defaults['password']),
        'database' => prompt('Nombre de base de datos', $defaults['database']),
    ];

    try {
        updateEnvDatabaseSettings($envPath, $dbConfig);
        echo "Archivo .env actualizado correctamente." . PHP_EOL;

        $migrationsPath = projectRoot() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        $files = migrationFiles($migrationsPath);
        if ($files === []) {
            echo "No se encontraron archivos de migracion en app/Database/Migrations." . PHP_EOL;
            exit(1);
        }

        $installed = installedMigrations($dbConfig);
        $pending = printMigrationStatus($files, $installed);

        if ($pending === []) {
            $syncResult = syncAdministradorRolePermisos($dbConfig);
            echo "Permisos del rol Administrador sincronizados. Total: {$syncResult['total']}, insertados: {$syncResult['insertados']}, restaurados: {$syncResult['restaurados']}, activos: {$syncResult['ya_activos']}" . PHP_EOL;
            echo "No hay migraciones pendientes. Sistema actualizado." . PHP_EOL;
            exit(0);
        }

        if (! promptYesNo('Deseas aplicar ahora las migraciones pendientes?', true)) {
            echo "Actualizacion detenida por usuario." . PHP_EOL;
            exit(0);
        }

        echo PHP_EOL . "Ejecutando migraciones pendientes..." . PHP_EOL;
        $exitCode = runSparkMigrate(projectRoot());
        if ($exitCode !== 0) {
            echo "Fallo la ejecucion de migraciones. Codigo: {$exitCode}" . PHP_EOL;
            exit($exitCode);
        }

        $syncResult = syncAdministradorRolePermisos($dbConfig);
        echo "Permisos del rol Administrador sincronizados. Total: {$syncResult['total']}, insertados: {$syncResult['insertados']}, restaurados: {$syncResult['restaurados']}, activos: {$syncResult['ya_activos']}" . PHP_EOL;

        echo PHP_EOL . "Actualizacion completada correctamente." . PHP_EOL;
        exit(0);
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

$status = '';
$statusType = 'info';
$migrateOutput = '';
$statusData = [
    'rows' => [],
    'pending' => [],
    'orphans' => [],
];
$dbConfig = dbConfigFromInput($_POST, $defaults);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        updateEnvDatabaseSettings($envPath, $dbConfig);

        $migrationsPath = projectRoot() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';
        $files = migrationFiles($migrationsPath);
        if ($files === []) {
            throw new RuntimeException('No se encontraron archivos de migracion en app/Database/Migrations.');
        }

        $installed = installedMigrations($dbConfig);
        $statusData = migrationStatusData($files, $installed);

        if (isset($_POST['apply_now']) && $_POST['apply_now'] === '1') {
            $result = runSparkMigrateWithOutput(projectRoot());
            $migrateOutput = $result['output'];

            if ($result['exitCode'] !== 0) {
                $statusType = 'danger';
                $status = 'Fallo la ejecucion de migraciones. Codigo: ' . $result['exitCode'];
            } else {
                $syncResult = syncAdministradorRolePermisos($dbConfig);
                $migrateOutput .= PHP_EOL . 'Permisos rol Administrador sincronizados: total=' . $syncResult['total'] . ', insertados=' . $syncResult['insertados'] . ', restaurados=' . $syncResult['restaurados'] . ', activos=' . $syncResult['ya_activos'];

                $installed = installedMigrations($dbConfig);
                $statusData = migrationStatusData($files, $installed);
                $statusType = 'success';
                $status = 'Actualizacion completada correctamente.';
            }
        } elseif ($statusData['pending'] === []) {
            $syncResult = syncAdministradorRolePermisos($dbConfig);
            $migrateOutput .= PHP_EOL . 'Permisos rol Administrador sincronizados: total=' . $syncResult['total'] . ', insertados=' . $syncResult['insertados'] . ', restaurados=' . $syncResult['restaurados'] . ', activos=' . $syncResult['ya_activos'];
            $statusType = 'success';
            $status = 'No hay migraciones pendientes. Sistema actualizado.';
        } else {
            $statusType = 'warning';
            $status = 'Hay migraciones pendientes. Revisa el estado y aplica cuando estes listo.';
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
    <title>Asistente de Actualizacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Asistente de Actualizacion</h1>
            <p class="text-muted">Detecta migraciones instaladas y pendientes, luego aplica actualizaciones.</p>

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
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Revisar estado</button>
                    <button type="submit" name="apply_now" value="1" class="btn btn-primary">Aplicar pendientes</button>
                </div>
            </form>

            <?php if ($statusData['rows'] !== []): ?>
                <hr>
                <h2 class="h6">Estado de migraciones</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                        <tr>
                            <th>Version</th>
                            <th>Clase</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($statusData['rows'] as $row): ?>
                            <tr>
                                <td><?= h($row['version']) ?></td>
                                <td><?= h($row['class']) ?></td>
                                <td>
                                    <span class="badge text-bg-<?= $row['status'] === 'PENDIENTE' ? 'warning' : 'success' ?>">
                                        <?= h($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($statusData['orphans'] !== []): ?>
                    <div class="alert alert-secondary">
                        <strong>Migraciones instaladas sin archivo local:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($statusData['orphans'] as $orphan): ?>
                                <li><?= h((string) $orphan['version']) ?> | <?= h((string) $orphan['class']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($migrateOutput !== ''): ?>
                <hr>
                <h2 class="h6">Salida de migraciones</h2>
                <pre class="bg-dark text-light p-3 rounded small"><?= h($migrateOutput) ?></pre>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
