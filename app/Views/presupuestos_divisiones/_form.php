<?php
$errors = session('errors') ?? [];
$presupuesto = $presupuestoDivision ?? [];
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
    <label for="id_ejercicio" class="form-label">Ejercicio Fiscal</label>
    <select id="id_ejercicio" name="id_ejercicio" class="form-select" required onchange="actualizarDisponible()">
        <option value="">Seleccione un ejercicio</option>
        <?php foreach ($ejerciciosFiscales as $ejercicio): ?>
            <option value="<?= esc($ejercicio['id']) ?>"
                    data-disponible="<?= esc($ejercicio['disponible']) ?>"
                    <?= (old('id_ejercicio', $presupuesto['id_ejercicio'] ?? '') == $ejercicio['id']) ? 'selected' : '' ?>>
                <?= esc($ejercicio['anio']) ?> - Q <?= number_format($ejercicio['presupuesto_total'], 2, '.', ',') ?> (Disponible: Q <?= number_format($ejercicio['disponible'], 2, '.', ',') ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="id_division" class="form-label">División</label>
    <select id="id_division" name="id_division" class="form-select" required>
        <option value="">Seleccione una división</option>
        <?php foreach ($divisiones as $division): ?>
            <option value="<?= esc($division['id']) ?>" <?= (old('id_division', $presupuesto['id_division'] ?? '') == $division['id']) ? 'selected' : '' ?>>
                <?= esc($division['nombre_division']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="monto_asignado" class="form-label">Monto Asignado</label>
    <div class="input-group">
        <span class="input-group-text">Q</span>
        <input
            type="number"
            id="monto_asignado"
            name="monto_asignado"
            class="form-control"
            step="0.01"
            min="0"
            required
            value="<?= esc(old('monto_asignado', $presupuesto['monto_asignado'] ?? '')) ?>"
        >
    </div>
    <small class="text-muted" id="disponibleInfo">Selecciona un ejercicio fiscal para ver el monto disponible.</small>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $presupuesto['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $presupuesto['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('presupuestos-divisiones') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>

<script>
function actualizarDisponible() {
    const select = document.getElementById('id_ejercicio');
    const option = select.options[select.selectedIndex];
    const disponible = parseFloat(option.dataset.disponible || 0);
    const info = document.getElementById('disponibleInfo');

    if (disponible >= 0) {
        info.textContent = `Presupuesto disponible: Q ${disponible.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        info.className = 'text-muted';
    } else {
        info.textContent = 'Presupuesto agotado para este ejercicio';
        info.className = 'text-danger fw-bold';
    }
}

document.addEventListener('DOMContentLoaded', actualizarDisponible);
</script>

