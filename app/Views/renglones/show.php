<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="mb-3">
        <a href="<?= base_url('renglones') ?>" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><?= $heading ?? 'Detalles del Renglón' ?></h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Código Renglón</h6>
                    <p class="fs-5"><strong><?= esc($renglon['codigo_renglon']) ?></strong></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Estado</h6>
                    <p class="fs-5">
                        <span class="badge bg-<?= $renglon['status'] === 'activo' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($renglon['status']) ?>
                        </span>
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="text-muted">Descripción</h6>
                    <p><?= esc($renglon['descripcion']) ?></p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Creado por</h6>
                    <p><?= esc($renglon['usuario_creo'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Fecha de Creación</h6>
                    <p><?= date('d/m/Y H:i', strtotime($renglon['created_at'])) ?></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Última actualización por</h6>
                    <p><?= esc($renglon['usuario_actualizo'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Última actualización</h6>
                    <p><?= $renglon['updated_at'] ? date('d/m/Y H:i', strtotime($renglon['updated_at'])) : 'N/A' ?></p>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?= base_url('renglones/editar/' . $renglon['id']) ?>" class="btn btn-warning">Editar</a>
                <a href="<?= base_url('renglones') ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
