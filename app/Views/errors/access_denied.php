<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 text-center">
            <div class="mb-3">
                <span class="badge text-bg-danger fs-6">Error 403</span>
            </div>

            <h1 class="h3 mb-2">Acceso denegado</h1>
            <p class="text-muted mb-3">
                No tienes permisos para ejecutar esta accion en el sistema.
            </p>

            <div class="alert alert-warning text-start mx-auto" style="max-width: 680px;">
                <div><strong>Permiso requerido:</strong> <?= esc($nombrePermiso ?? 'No definido') ?></div>
                <div><strong>Usuario:</strong> <?= esc($usuarioAlias ?? 'No disponible') ?></div>
            </div>

            <div class="d-flex justify-content-center gap-2 mt-3">
                <a href="<?= base_url('usuarios') ?>" class="btn btn-primary">Ir al inicio</a>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">Cerrar sesion</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
