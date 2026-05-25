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
    <label for="anio" class="form-label">Año</label>
    <input
        type="number"
        id="anio"
        name="anio"
        class="form-control"
        min="1900"
        max="2999"
        required
        value="<?= esc(old('anio', $ejercicioFiscal['anio'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="presupuesto_total" class="form-label">Presupuesto Total</label>
    <div class="input-group">
        <span class="input-group-text">Q</span>
        <input
            type="number"
            id="presupuesto_total"
            name="presupuesto_total"
            class="form-control"
            step="0.01"
            min="0"
            required
            value="<?= esc(old('presupuesto_total', $ejercicioFiscal['presupuesto_total'] ?? '')) ?>"
        >
    </div>
</div>

<div class="mb-3 form-check">
    <input
        type="checkbox"
        id="estado_abierto"
        name="estado_abierto"
        class="form-check-input"
        value="1"
        <?= (old('estado_abierto', $ejercicioFiscal['estado_abierto'] ?? 1) == 1) ? 'checked' : '' ?>
    >
    <label for="estado_abierto" class="form-check-label">
        Ejercicio abierto
    </label>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $ejercicioFiscal['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $ejercicioFiscal['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('ejercicios-fiscales') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
