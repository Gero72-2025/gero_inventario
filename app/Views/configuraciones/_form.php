<?php
$errors = session('errors') ?? [];
$lockLlave = $lockLlave ?? false;
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
    <label for="llave" class="form-label">Llave</label>
    <input
        type="text"
        id="llave"
        name="llave"
        class="form-control"
        maxlength="100"
        <?= $lockLlave ? 'readonly' : 'required' ?>
        value="<?= esc(old('llave', $configuracion['llave'] ?? '')) ?>"
    >
    <?php if ($lockLlave): ?>
        <small class="text-muted">La llave no se puede modificar una vez creada.</small>
    <?php endif; ?>
</div>

<div class="row g-3">
    <?php for ($i = 1; $i <= 10; $i++): ?>
        <div class="col-12 col-lg-6">
            <label for="valor_<?= $i ?>" class="form-label">Valor <?= $i ?></label>
            <textarea
                id="valor_<?= $i ?>"
                name="valor_<?= $i ?>"
                class="form-control"
                rows="4"
            ><?= esc(old('valor_' . $i, $configuracion['valor_' . $i] ?? '')) ?></textarea>
        </div>
    <?php endfor; ?>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('configuraciones') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
