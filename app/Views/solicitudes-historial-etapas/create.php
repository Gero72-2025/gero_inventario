<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Crear historial de etapa</h1>
        <a href="<?= base_url('solicitudes-historial-etapas') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('solicitudes-historial-etapas') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Solicitud</label>
                        <select name="id_solicitud" class="form-select" required>
                            <option value="">Seleccione una solicitud</option>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <option value="<?= esc($solicitud['id']) ?>" <?= old('id_solicitud') == $solicitud['id'] ? 'selected' : '' ?>><?= esc($solicitud['folio_fisico'] ?? $solicitud['id']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Etapa anterior</label>
                        <select name="id_etapa_anterior" class="form-select" required>
                            <option value="">Seleccione una etapa</option>
                            <?php foreach ($etapas as $etapa): ?>
                                <option value="<?= esc($etapa['id']) ?>" <?= old('id_etapa_anterior') == $etapa['id'] ? 'selected' : '' ?>><?= esc($etapa['nombre_etapa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Etapa nueva</label>
                        <select name="id_etapa_nueva" class="form-select" required>
                            <option value="">Seleccione una etapa</option>
                            <?php foreach ($etapas as $etapa): ?>
                                <option value="<?= esc($etapa['id']) ?>" <?= old('id_etapa_nueva') == $etapa['id'] ? 'selected' : '' ?>><?= esc($etapa['nombre_etapa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Comentario de transición</label>
                        <textarea name="comentario_transicion" class="form-control" rows="4" placeholder="Describa el motivo o detalle de la transición"><?= old('comentario_transicion') ?></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="<?= base_url('solicitudes-historial-etapas') ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
