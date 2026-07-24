<?php
$errors = session('errors') ?? [];
$contrato = $contrato ?? [];
$submitLabel = $submitLabel ?? 'Guardar';
?>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="mb-3">
    <label for="numero_contrato" class="form-label">Número de contrato</label>
    <input
        type="text"
        id="numero_contrato"
        name="numero_contrato"
        class="form-control"
        maxlength="100"
        required
        value="<?= esc(old('numero_contrato', $contrato['numero_contrato'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="id_empleado" class="form-label">Empleado</label>
    <select id="id_empleado" name="id_empleado" class="form-select" required>
        <option value="">-- Seleccionar empleado --</option>
        <?php foreach ($empleados as $empleado): ?>
            <option value="<?= esc($empleado['id']) ?>" <?= (old('id_empleado', $contrato['id_empleado'] ?? '') == $empleado['id']) ? 'selected' : '' ?>>
                <?= esc($empleado['nombre_completo']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
        <input
            type="date"
            id="fecha_inicio"
            name="fecha_inicio"
            class="form-control"
            required
            value="<?= esc(old('fecha_inicio', $contrato['fecha_inicio'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="fecha_fin" class="form-label">Fecha de fin</label>
        <input
            type="date"
            id="fecha_fin"
            name="fecha_fin"
            class="form-control"
            required
            value="<?= esc(old('fecha_fin', $contrato['fecha_fin'] ?? '')) ?>"
        >
    </div>
</div>

<div class="mb-3">
    <label for="monto_contrato" class="form-label">Monto del contrato</label>
    <input
        type="number"
        id="monto_contrato"
        name="monto_contrato"
        class="form-control"
        step="0.01"
        min="0"
        required
        value="<?= esc(old('monto_contrato', $contrato['monto_contrato'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="estado_contrato" class="form-label">Estado del contrato</label>
    <select id="estado_contrato" name="estado_contrato" class="form-select" required>
        <option value="">-- Seleccionar estado --</option>
        <option value="vigente" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'vigente') ? 'selected' : '' ?>>
            Vigente
        </option>
        <option value="vencido" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'vencido') ? 'selected' : '' ?>>
            Vencido
        </option>
        <option value="rescindido" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'rescindido') ? 'selected' : '' ?>>
            Rescindido
        </option>
    </select>
</div>

<div class="mb-3">
    <label for="pdf_contrato" class="form-label">
        Archivo PDF del contrato
        <?php if (! empty($submitLabel) && strpos($submitLabel, 'Actualizar') !== false): ?>
            <small class="text-muted">(opcional, deja en blanco para mantener el actual)</small>
        <?php else: ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
    <input
        type="file"
        id="pdf_contrato"
        name="pdf_contrato"
        class="form-control"
        accept=".pdf"
        <?= (! empty($submitLabel) && strpos($submitLabel, 'Actualizar') !== false) ? '' : 'required' ?>
    >
    <small class="form-text text-muted">Máximo 5 MB. Solo archivos PDF.</small>
    <?php if (! empty($contrato['pdf_contrato_path'])): ?>
        <div class="mt-2">
            <small class="text-success">
                <i class="fas fa-check-circle"></i> Archivo actual:
                <a href="<?= base_url('contratos-empleados/descargar-pdf/' . $contrato['id']) ?>" target="_blank">
                    Descargar
                </a>
            </small>
        </div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $contrato['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $contrato['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('contratos-empleados') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
