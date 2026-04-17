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
    <label for="nombre_rol" class="form-label">Nombre de rol</label>
    <input
        type="text"
        id="nombre_rol"
        name="nombre_rol"
        class="form-control"
        maxlength="50"
        required
        value="<?= esc(old('nombre_rol', $rol['nombre_rol'] ?? '')) ?>"
    >
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label">Descripcion</label>
    <textarea id="descripcion" name="descripcion" class="form-control" rows="4"><?= esc(old('descripcion', $rol['descripcion'] ?? '')) ?></textarea>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('roles') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
