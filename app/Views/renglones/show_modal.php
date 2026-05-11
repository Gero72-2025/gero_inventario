<?php
/**
 * Modal para mostrar detalles del renglón (AJAX)
 * Se carga dentro del GeroModal
 */
?>
<div class="container-fluid">
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

    <div class="row text-sm">
        <div class="col-md-6">
            <small class="text-muted d-block">Creado por: <?= esc($renglon['usuario_creo'] ?? 'N/A') ?></small>
            <small class="text-muted d-block">Fecha: <?= date('d/m/Y H:i', strtotime($renglon['created_at'])) ?></small>
        </div>
        <div class="col-md-6">
            <small class="text-muted d-block">Actualizado por: <?= esc($renglon['usuario_actualizo'] ?? 'N/A') ?></small>
            <small class="text-muted d-block">Fecha: <?= $renglon['updated_at'] ? date('d/m/Y H:i', strtotime($renglon['updated_at'])) : 'N/A' ?></small>
        </div>
    </div>
</div>
