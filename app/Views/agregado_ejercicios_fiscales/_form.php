<?php
$errors = session('errors') ?? [];
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
    <label for="id_ejercicio_fiscal" class="form-label">Ejercicio Fiscal *</label>
    <select
        id="id_ejercicio_fiscal"
        name="id_ejercicio_fiscal"
        class="form-select"
        required
    >
        <option value="">-- Seleccionar Ejercicio Fiscal --</option>
        <?php foreach ($ejerciciosFiscales as $ejercicio): ?>
            <option
                value="<?= esc((string) $ejercicio['id']) ?>"
                <?= (old('id_ejercicio_fiscal', $agregado['id_ejercicio_fiscal'] ?? '') == $ejercicio['id']) ? 'selected' : '' ?>
            >
                <?= esc((string) $ejercicio['anio']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="monto" class="form-label">Monto *</label>
    <input
        type="number"
        id="monto"
        name="monto"
        class="form-control"
        step="0.01"
        required
        value="<?= esc(old('monto', $agregado['monto'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="justificacion" class="form-label">Justificación</label>
    <textarea
        id="justificacion"
        name="justificacion"
        class="form-control"
        rows="4"
        maxlength="1000"
    ><?= esc(old('justificacion', $agregado['justificacion'] ?? '')) ?></textarea>
    <small class="text-muted">Máximo 1000 caracteres</small>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $agregado['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $agregado['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('agregado-ejercicios-fiscales') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
