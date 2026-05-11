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
    <label for="nombre_etapa" class="form-label">Nombre de etapa</label>
    <input
        type="text"
        id="nombre_etapa"
        name="nombre_etapa"
        class="form-control"
        maxlength="100"
        required
        value="<?= esc(old('nombre_etapa', $etapa['nombre_etapa'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="color_hex" class="form-label">Color (código hexadecimal)</label>
    <div class="input-group">
        <input
            type="text"
            id="color_hex"
            name="color_hex"
            class="form-control"
            maxlength="7"
            placeholder="#000000"
            required
            value="<?= esc(old('color_hex', $etapa['color_hex'] ?? '')) ?>"
        >
        <input
            type="color"
            id="color_picker"
            class="form-control form-control-color"
            style="max-width: 50px; cursor: pointer;"
            value="<?= esc(old('color_hex', $etapa['color_hex'] ?? '#000000')) ?>"
        >
    </div>
    <small class="text-muted">Usa el selector de color o ingresa el código hexadecimal (ej: #FF5733)</small>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $etapa['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $etapa['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('etapas') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorHexInput = document.getElementById('color_hex');
    const colorPicker = document.getElementById('color_picker');

    // Sincronizar input color con input text
    colorPicker.addEventListener('input', function() {
        colorHexInput.value = this.value;
    });

    // Sincronizar input text con input color
    colorHexInput.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorPicker.value = this.value;
        }
    });
});
</script>
