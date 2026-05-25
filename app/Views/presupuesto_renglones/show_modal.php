<?php if (!$this->request->isAJAX()): ?>
    <?php $this->extend('layouts/app'); ?>
    <?php $this->section('content'); ?>
<?php endif; ?>

<div class="modal-header">
    <h5 class="modal-title">Detalles de Presupuesto Renglón</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="text-muted">ID</h6>
            <p><?= esc($presupuesto['id']) ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted">Estado</h6>
            <p>
                <span class="badge bg-<?= $presupuesto['status'] === 'activo' ? 'success' : 'secondary' ?>">
                    <?= ucfirst($presupuesto['status']) ?>
                </span>
            </p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="text-muted">Monto Asignado</h6>
            <p><?= number_format($presupuesto['monto_asignado'], 2, ',', '.') ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted">Saldo Actual</h6>
            <p><?= number_format($presupuesto['saldo_actual'], 2, ',', '.') ?></p>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted">Creado</h6>
            <p><?= date('d/m/Y H:i', strtotime($presupuesto['created_at'])) ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted">Actualizado</h6>
            <p><?= $presupuesto['updated_at'] ? date('d/m/Y H:i', strtotime($presupuesto['updated_at'])) : '-' ?></p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="<?= base_url('presupuestos-renglones/editar/' . $presupuesto['id']) ?>" class="btn btn-warning">Editar</a>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>

<?php if (!$this->request->isAJAX()): ?>
    <?php $this->endSection(); ?>
<?php endif; ?>
