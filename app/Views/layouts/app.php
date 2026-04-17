<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'Portal Finanzas') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
<?php $isLoggedIn = (bool) (session('is_logged_in') ?? false); ?>
<?php
$uri = service('uri');
$segment1 = strtolower((string) $uri->getSegment(1));

$modulosNavegables = [];
if ($isLoggedIn) {
    $userId = (int) (session('user_id') ?? 0);
    if ($userId > 0) {
        try {
            $rows = db_connect()->table('usuarios u')
                ->select('p.controlador, p.accion')
                ->join('rol_permisos rp', 'rp.id_rol = u.id_rol AND rp.deleted_at IS NULL', 'inner')
                ->join('permisos p', 'p.id = rp.id_permiso AND p.deleted_at IS NULL', 'inner')
                ->where('u.id', $userId)
                ->where('u.deleted_at', null)
                ->orderBy('p.controlador', 'ASC')
                ->orderBy('p.accion', 'ASC')
                ->get()
                ->getResultArray();

            $accionesPorControlador = [];
            foreach ($rows as $row) {
                $controlador = trim((string) ($row['controlador'] ?? ''));
                $accion = strtolower(trim((string) ($row['accion'] ?? '')));
                if ($controlador === '' || $accion === '') {
                    continue;
                }

                if (! isset($accionesPorControlador[$controlador])) {
                    $accionesPorControlador[$controlador] = [];
                }

                $accionesPorControlador[$controlador][$accion] = true;
            }

            $iconosPorControlador = [
                'Usuarios' => 'bi-people-fill',
                'Configuraciones' => 'bi-gear-fill',
                'Roles' => 'bi-person-badge-fill',
                'Permisos' => 'bi-shield-lock-fill',
            ];

            foreach ($accionesPorControlador as $controlador => $acciones) {
                if (! isset($acciones['index'])) {
                    continue;
                }

                $slug = strtolower($controlador);
                $modulosNavegables[] = [
                    'slug'  => $slug,
                    'url'   => base_url($slug),
                    'label' => $controlador,
                    'icono' => $iconosPorControlador[$controlador] ?? 'bi-grid-1x2-fill',
                ];
            }

            usort($modulosNavegables, static function (array $a, array $b): int {
                return strcmp((string) $a['label'], (string) $b['label']);
            });
        } catch (Throwable $e) {
            $modulosNavegables = [];
        }
    }
}
?>

<?php if ($isLoggedIn): ?>
    <div class="app-shell" id="appShell">
        <aside class="app-sidebar">
            <div class="brand">GERO - INDE</div>
            <nav class="nav flex-column">
                <?php if (empty($modulosNavegables)): ?>
                    <div class="text-white-50 small px-2 py-2">
                        Sin modulos asignados para este rol.
                    </div>
                <?php else: ?>
                    <?php foreach ($modulosNavegables as $modulo): ?>
                        <a href="<?= esc((string) $modulo['url']) ?>" class="nav-link <?= $segment1 === $modulo['slug'] ? 'active' : '' ?>">
                            <i class="bi <?= esc((string) $modulo['icono']) ?>"></i>
                            <span><?= esc((string) $modulo['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </aside>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <main class="app-main">
            <header class="app-header">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Abrir u ocultar menu lateral">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="fw-semibold"><?= esc($pageTitle ?? 'Portal Finanzas') ?></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">Cerrar sesion</a>
                </div>
            </header>

            <section class="app-content">
                <?= $this->renderSection('content') ?>
            </section>

            <footer class="app-footer">
                Creado por AGPT - GERO - INDE &copy; a&ntilde;o <?= date('Y') ?>
            </footer>
        </main>
    </div>
<?php else: ?>
    <div class="guest-content">
        <?= $this->renderSection('content') ?>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Modal Global GERO -->
<div class="modal fade" id="geroModal" tabindex="-1" aria-labelledby="geroModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="geroModalHeader">
                <h5 class="modal-title fw-semibold" id="geroModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="geroModalBody"></div>
            <div class="modal-footer" id="geroModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn" id="geroModalConfirm">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal Global GERO -->

<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
