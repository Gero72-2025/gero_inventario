<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Gestionar Permisos por Rol</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $errors = session('errors') ?? []; ?>
    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('permisos/roles') ?>" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="id_rol" class="form-label">Selecciona un rol</label>
                    <select id="id_rol" name="id_rol" class="form-select" required>
                        <option value="">Selecciona una opcion</option>
                        <?php foreach (($roles ?? []) as $rol): ?>
                            <option value="<?= esc($rol['id']) ?>" <?= (int) ($idRol ?? 0) === (int) $rol['id'] ? 'selected' : '' ?>>
                                <?= esc($rol['nombre_rol']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Cargar permisos</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ((int) ($idRol ?? 0) > 0): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= base_url('permisos/roles/guardar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_rol" value="<?= esc((string) $idRol) ?>">

                    <?php $totalPermisos = count($permisos ?? []); ?>
                    <?php if ($totalPermisos > 0): ?>
                        <div class="form-check mb-3 p-2 border rounded bg-light">
                            <input class="form-check-input" type="checkbox" id="select_all_global">
                            <label class="form-check-label fw-semibold" for="select_all_global">
                                Seleccionar todos los permisos
                            </label>
                            <small class="text-muted d-block">Afecta todas las secciones (controladores).</small>
                        </div>
                    <?php endif; ?>

                    <div class="row g-2">
                        <?php if (empty($permisos)): ?>
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">No hay permisos detectados. Usa primero "Sincronizar Acciones del Sistema".</div>
                            </div>
                        <?php else: ?>
                            <?php foreach (($permisosPorControlador ?? []) as $controlador => $items): ?>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-3">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                            <div>
                                                <h2 class="h6 mb-0"><?= esc($controlador) ?></h2>
                                                <small class="text-muted">Permisos detectados: <?= esc((string) count($items)) ?></small>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input section-toggle"
                                                    type="checkbox"
                                                    id="select_section_<?= esc($controlador) ?>"
                                                    data-section="<?= esc($controlador) ?>"
                                                >
                                                <label class="form-check-label" for="select_section_<?= esc($controlador) ?>">
                                                    Seleccionar seccion
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row g-2">
                                            <?php foreach ($items as $permiso): ?>
                                                <div class="col-md-6">
                                                    <label class="form-check border rounded p-2 w-100">
                                                        <input
                                                            class="form-check-input permiso-item"
                                                            type="checkbox"
                                                            name="id_permisos[]"
                                                            value="<?= esc($permiso['id']) ?>"
                                                            data-section="<?= esc($controlador) ?>"
                                                            <?= in_array((int) $permiso['id'], $asignados ?? [], true) ? 'checked' : '' ?>
                                                        >
                                                        <span class="form-check-label ms-2">
                                                            <strong><?= esc($permiso['nombre_permiso']) ?></strong><br>
                                                            <small class="text-muted"><?= esc($permiso['controlador']) ?> :: <?= esc($permiso['accion']) ?></small>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar permisos del rol</button>
                        <a href="<?= base_url('permisos') ?>" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const globalToggle = document.getElementById('select_all_global');
    const sectionToggles = Array.from(document.querySelectorAll('.section-toggle'));
    const permisoItems = Array.from(document.querySelectorAll('.permiso-item'));

    if (permisoItems.length === 0) {
        return;
    }

    function getItemsBySection(section) {
        return permisoItems.filter((item) => item.dataset.section === section);
    }

    function syncSectionToggle(section) {
        const sectionToggle = sectionToggles.find((toggle) => toggle.dataset.section === section);
        if (!sectionToggle) {
            return;
        }

        const items = getItemsBySection(section);
        const checked = items.filter((item) => item.checked).length;

        sectionToggle.checked = checked > 0 && checked === items.length;
        sectionToggle.indeterminate = checked > 0 && checked < items.length;
    }

    function syncGlobalToggle() {
        if (!globalToggle) {
            return;
        }

        const checked = permisoItems.filter((item) => item.checked).length;
        globalToggle.checked = checked > 0 && checked === permisoItems.length;
        globalToggle.indeterminate = checked > 0 && checked < permisoItems.length;
    }

    if (globalToggle) {
        globalToggle.addEventListener('change', (event) => {
            const checked = !!event.target.checked;
            permisoItems.forEach((item) => {
                item.checked = checked;
            });

            sectionToggles.forEach((toggle) => {
                toggle.checked = checked;
                toggle.indeterminate = false;
            });
        });
    }

    sectionToggles.forEach((toggle) => {
        toggle.addEventListener('change', (event) => {
            const section = event.target.dataset.section;
            const checked = !!event.target.checked;
            const items = getItemsBySection(section);

            items.forEach((item) => {
                item.checked = checked;
            });

            event.target.indeterminate = false;
            syncGlobalToggle();
        });
    });

    permisoItems.forEach((item) => {
        item.addEventListener('change', (event) => {
            const section = event.target.dataset.section;
            syncSectionToggle(section);
            syncGlobalToggle();
        });
    });

    const sections = Array.from(new Set(permisoItems.map((item) => item.dataset.section)));
    sections.forEach((section) => syncSectionToggle(section));
    syncGlobalToggle();
})();
</script>
<?= $this->endSection() ?>
