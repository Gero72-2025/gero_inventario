<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="mb-3">
        <a href="<?= base_url('presupuestos-renglones') ?>" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><?= $heading ?? 'Detalles de Presupuesto Renglón' ?></h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">ID</h6>
                    <p class="lead"><?= esc($presupuesto['id']) ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Estado</h6>
                    <p class="lead">
                        <span class="badge bg-<?= $presupuesto['status'] === 'activo' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($presupuesto['status']) ?>
                        </span>
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Presupuesto División ID</h6>
                    <p class="lead"><?= esc($presupuesto['id_presupuesto_division']) ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Renglón ID</h6>
                    <p class="lead"><?= esc($presupuesto['id_renglon']) ?></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Monto Asignado</h6>
                    <p class="lead"><?= number_format($presupuesto['monto_asignado'], 2, ',', '.') ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Saldo Actual</h6>
                    <p class="lead"><?= number_format($presupuesto['saldo_actual'], 2, ',', '.') ?></p>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Creado por</h6>
                    <p class="lead">ID Usuario: <?= esc($presupuesto['id_usuario_creo']) ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Fecha Creación</h6>
                    <p class="lead"><?= date('d/m/Y H:i', strtotime($presupuesto['created_at'])) ?></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Actualizado por</h6>
                    <p class="lead">ID Usuario: <?= esc($presupuesto['id_usuario_actualizo'] ?? '-') ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Fecha Actualización</h6>
                    <p class="lead"><?= $presupuesto['updated_at'] ? date('d/m/Y H:i', strtotime($presupuesto['updated_at'])) : '-' ?></p>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?= base_url('presupuestos-renglones/editar/' . $presupuesto['id']) ?>" class="btn btn-warning">Editar</a>
                <a href="<?= base_url('presupuestos-renglones') ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
