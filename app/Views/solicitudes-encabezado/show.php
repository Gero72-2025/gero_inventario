<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= esc($heading) ?></h1>
        <div class="gap-2 d-flex">
            <a href="<?= base_url('solicitudes-encabezado/editar/' . $solicitud['id']) ?>" class="btn btn-outline-secondary">Editar</a>
            <a href="<?= base_url('solicitudes-encabezado') ?>" class="btn btn-outline-primary">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID</label>
                            <p class="h6"><?= esc($solicitud['id']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Folio Físico</label>
                            <p class="h6"><?= esc($solicitud['folio_fisico']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">División</label>
                            <p class="h6"><?= esc($solicitud['id_division']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Etapa</label>
                            <p class="h6"><?= esc($solicitud['id_etapa']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Monto Estimado Total</label>
                            <p class="h6"><?= esc(number_format($solicitud['monto_estimado_total'], 2, '.', ',')) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Estado</label>
                            <p class="h6">
                                <span class="badge bg-<?= $solicitud['status'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($solicitud['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Creado</label>
                            <p class="h6"><?= esc($solicitud['created_at'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Actualizado</label>
                            <p class="h6"><?= esc($solicitud['updated_at'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <?php if ($solicitud['pdf_firmado_path']): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">PDF Firmado</label>
                            <p class="h6">
                                <a href="<?= base_url('solicitudes-encabezado/descargar-pdf/' . $solicitud['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> Descargar PDF
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
