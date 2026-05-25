<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="mb-3">
        <a href="<?= base_url('presupuestos-renglones') ?>" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= $heading ?? 'Crear Presupuesto Renglón' ?></h4>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if ($errors = session('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('presupuestos-renglones/guardar') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="id_presupuesto_division" class="form-label">Presupuesto División <span class="text-danger">*</span></label>
                    <select class="form-select <?= session('errors.id_presupuesto_division') ? 'is-invalid' : '' ?>"
                            id="id_presupuesto_division"
                            name="id_presupuesto_division"
                            required
                            onchange="actualizarDisponible()">
                        <option value="">Seleccionar presupuesto...</option>
                        <?php foreach ($presupuestos as $presupuesto): ?>
                            <option value="<?= esc($presupuesto['id']) ?>" data-monto="<?= esc($presupuesto['saldo_actual']) ?>" <?= old('id_presupuesto_division') == $presupuesto['id'] ? 'selected' : '' ?>>
                                Año: <?= esc($presupuesto['anio']) ?> - <?= esc($presupuesto['nombre_division']) ?> (Disponible: <?= number_format($presupuesto['saldo_actual'], 2, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($error = session('errors.id_presupuesto_division')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted d-block mt-1">Disponible: <strong id="disponible">0.00</strong></small>
                </div>

                <div class="mb-3">
                    <label for="id_renglon" class="form-label">Renglón <span class="text-danger">*</span></label>
                    <select class="form-select <?= session('errors.id_renglon') ? 'is-invalid' : '' ?>"
                            id="id_renglon"
                            name="id_renglon"
                            required>
                        <option value="">Seleccionar renglón...</option>
                        <?php foreach ($renglones as $renglon): ?>
                            <option value="<?= esc($renglon['id']) ?>" <?= old('id_renglon') == $renglon['id'] ? 'selected' : '' ?>>
                                <?= esc($renglon['codigo_renglon']) ?> - <?= esc($renglon['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($error = session('errors.id_renglon')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="monto_asignado" class="form-label">Monto Asignado <span class="text-danger">*</span></label>
                    <input type="number"
                           class="form-control <?= session('errors.monto_asignado') ? 'is-invalid' : '' ?>"
                           id="monto_asignado"
                           name="monto_asignado"
                           value="<?= old('monto_asignado') ?>"
                           step="0.01"
                           min="0"
                           required>
                    <?php if ($error = session('errors.monto_asignado')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>"
                            id="status"
                            name="status"
                            required>
                        <option value="">Seleccionar estado...</option>
                        <option value="activo" <?= old('status') === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= old('status') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                    <?php if ($error = session('errors.status')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Crear Presupuesto Renglón</button>
                    <a href="<?= base_url('presupuestos-renglones') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function actualizarDisponible() {
    const select = document.getElementById('id_presupuesto_division');
    const option = select.options[select.selectedIndex];
    const disponible = option.getAttribute('data-monto') || '0';
    document.getElementById('disponible').textContent = parseFloat(disponible).toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarDisponible();
});
</script>
<?php $this->endSection(); ?>
