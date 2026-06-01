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
    <label for="codigo_empleado" class="form-label">Código de empleado</label>
    <input
        type="text"
        id="codigo_empleado"
        name="codigo_empleado"
        class="form-control"
        maxlength="50"
        required
        value="<?= esc(old('codigo_empleado', $empleado['codigo_empleado'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="nombre_completo" class="form-label">Nombre completo</label>
    <input
        type="text"
        id="nombre_completo"
        name="nombre_completo"
        class="form-control"
        maxlength="150"
        required
        value="<?= esc(old('nombre_completo', $empleado['nombre_completo'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="tipo_contrato" class="form-label">Tipo de contrato</label>
    <select id="tipo_contrato" name="tipo_contrato" class="form-select" required>
        <option value="">-- Seleccionar --</option>
        <option value="011" <?= (old('tipo_contrato', $empleado['tipo_contrato'] ?? '') === '011') ? 'selected' : '' ?>>
            Tipo 011
        </option>
        <option value="012" <?= (old('tipo_contrato', $empleado['tipo_contrato'] ?? '') === '012') ? 'selected' : '' ?>>
            Tipo 012
        </option>
        <option value="contratista" <?= (old('tipo_contrato', $empleado['tipo_contrato'] ?? '') === 'contratista') ? 'selected' : '' ?>>
            Contratista
        </option>
    </select>
</div>

<div class="mb-3">
    <label for="id_division" class="form-label">División</label>
    <select id="id_division" name="id_division" class="form-select" required>
        <option value="">-- Seleccionar división --</option>
        <?php foreach ($divisiones as $division): ?>
            <option value="<?= esc($division['id']) ?>" <?= (old('id_division', $empleado['id_division'] ?? '') == $division['id']) ? 'selected' : '' ?>>
                <?= esc($division['nombre_division']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $empleado['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $empleado['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('empleados') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
