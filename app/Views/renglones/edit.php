<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="mb-3">
        <a href="<?= base_url('renglones') ?>" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><?= $heading ?? 'Editar Renglón' ?></h4>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if ($errors = session('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('renglones/actualizar/' . $renglon['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="codigo_renglon" class="form-label">Código Renglón <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control <?= session('errors.codigo_renglon') ? 'is-invalid' : '' ?>" 
                           id="codigo_renglon" 
                           name="codigo_renglon" 
                           value="<?= old('codigo_renglon', $renglon['codigo_renglon']) ?>" 
                           maxlength="100" 
                           required>
                    <?php if ($error = session('errors.codigo_renglon')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= session('errors.descripcion') ? 'is-invalid' : '' ?>" 
                              id="descripcion" 
                              name="descripcion" 
                              rows="4" 
                              required><?= old('descripcion', $renglon['descripcion']) ?></textarea>
                    <?php if ($error = session('errors.descripcion')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="">Seleccionar estado...</option>
                        <option value="activo" <?= old('status', $renglon['status']) === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= old('status', $renglon['status']) === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                    <?php if ($error = session('errors.status')): ?>
                        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning">Actualizar Renglón</button>
                    <a href="<?= base_url('renglones') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
