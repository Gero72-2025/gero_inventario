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
            <option value="<?= esc($division['id']) ?>" <?= (old('id_division', $empleado['id_division'] ?? '') == $division['id']) ? 'selected' : '' ?> >
                <?= esc($division['nombre_division']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nit" class="form-label">NIT</label>
        <input type="text" id="nit" name="nit" class="form-control" maxlength="50"
            value="<?= esc(old('nit', $empleado['nit'] ?? '')) ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label for="dpi" class="form-label">DPI</label>
        <input type="text" id="dpi" name="dpi" class="form-control" maxlength="50"
            value="<?= esc(old('dpi', $empleado['dpi'] ?? '')) ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="numero_telefonico" class="form-label">Número telefónico</label>
        <input type="text" id="numero_telefonico" name="numero_telefonico" class="form-control" maxlength="25"
            value="<?= esc(old('numero_telefonico', $empleado['numero_telefonico'] ?? '')) ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label for="correo_electronico" class="form-label">Correo electrónico</label>
        <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" maxlength="150"
            value="<?= esc(old('correo_electronico', $empleado['correo_electronico'] ?? '')) ?>">
    </div>
</div>

<div class="row align-items-center mb-3">
    <div class="col-auto">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="es_jefe" name="es_jefe" value="1"
                <?= (old('es_jefe', isset($empleado['es_jefe']) ? $empleado['es_jefe'] : '') == 1) ? 'checked' : '' ?> >
            <label class="form-check-label" for="es_jefe">Usuario jefe</label>
        </div>
    </div>

    <div class="col-md-6">
        <label for="id_usuario_asignado" class="form-label">Asignar usuario</label>
        <select id="id_usuario_asignado" name="id_usuario_asignado" class="form-select">
            <option value="">-- Ninguno --</option>
            <?php if (! empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <?php $label = esc($usuario['alias'] ?? $usuario['email'] ?? 'Usuario') ?>
                    <option value="<?= esc($usuario['id']) ?>" <?= (old('id_usuario_asignado', $empleado['id_usuario_asignado'] ?? '') == $usuario['id']) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
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
