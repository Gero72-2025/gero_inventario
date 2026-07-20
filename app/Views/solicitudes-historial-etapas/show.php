<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Detalle del historial</h1>
        <a href="<?= base_url('solicitudes-historial-etapas') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9"><?= esc($registro['id'] ?? '') ?></dd>

                <dt class="col-sm-3">Solicitud</dt>
                <dd class="col-sm-9"><?= esc($registro['folio_solicitud'] ?? 'Sin solicitud') ?></dd>

                <dt class="col-sm-3">Etapa anterior</dt>
                <dd class="col-sm-9"><?= esc($registro['etapa_anterior'] ?? 'Sin etapa anterior') ?></dd>

                <dt class="col-sm-3">Etapa nueva</dt>
                <dd class="col-sm-9"><?= esc($registro['etapa_nueva'] ?? 'Sin etapa nueva') ?></dd>

                <dt class="col-sm-3">Comentario</dt>
                <dd class="col-sm-9"><?= nl2br(esc($registro['comentario_transicion'] ?? '')) ?></dd>
            </dl>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
