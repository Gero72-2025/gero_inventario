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
    <label for="nit_proveedor" class="form-label">NIT del Proveedor <span class="text-danger">*</span></label>
    <input
        type="text"
        id="nit_proveedor"
        name="nit_proveedor"
        class="form-control"
        maxlength="50"
        required
        value="<?= esc(old('nit_proveedor', $proveedor['nit_proveedor'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="nombre_legal" class="form-label">Nombre Legal <span class="text-danger">*</span></label>
    <input
        type="text"
        id="nombre_legal"
        name="nombre_legal"
        class="form-control"
        maxlength="255"
        required
        value="<?= esc(old('nombre_legal', $proveedor['nombre_legal'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="nombre_comercial" class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
    <input
        type="text"
        id="nombre_comercial"
        name="nombre_comercial"
        class="form-control"
        maxlength="255"
        required
        value="<?= esc(old('nombre_comercial', $proveedor['nombre_comercial'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="direccion_fiscal" class="form-label">Dirección Fiscal <span class="text-danger">*</span></label>
    <textarea
        id="direccion_fiscal"
        name="direccion_fiscal"
        class="form-control"
        rows="3"
        required
    ><?= esc(old('direccion_fiscal', $proveedor['direccion_fiscal'] ?? '')) ?></textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="telefono_contacto" class="form-label">Teléfono de Contacto</label>
        <input
            type="text"
            id="telefono_contacto"
            name="telefono_contacto"
            class="form-control"
            maxlength="20"
            value="<?= esc(old('telefono_contacto', $proveedor['telefono_contacto'] ?? '')) ?>"
        >
    </div>

    <div class="col-md-6 mb-3">
        <label for="email_contacto" class="form-label">Email de Contacto</label>
        <input
            type="email"
            id="email_contacto"
            name="email_contacto"
            class="form-control"
            maxlength="100"
            value="<?= esc(old('email_contacto', $proveedor['email_contacto'] ?? '')) ?>"
        >
    </div>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $proveedor['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $proveedor['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('proveedores') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
