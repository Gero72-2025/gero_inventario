<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Editar Insumo</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('cat_insumos/actualizar/' . $insumo['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="id_renglon" class="form-label">Renglón</label>
                    <select name="id_renglon" id="id_renglon" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($renglones as $r): ?>
                            <option value="<?= esc($r['id']) ?>" <?= set_select('id_renglon', $r['id'], $insumo['id_renglon'] == $r['id']) ?>><?= esc($r['codigo_renglon']) ?> - <?= esc($r['descripcion']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="codigo_pacc" class="form-label">Código PACC</label>
                    <input type="text" name="codigo_pacc" id="codigo_pacc" class="form-control" value="<?= set_value('codigo_pacc', $insumo['codigo_pacc']) ?>" required maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="nombre_insumo" class="form-label">Nombre del Insumo</label>
                    <input type="text" name="nombre_insumo" id="nombre_insumo" class="form-control" value="<?= set_value('nombre_insumo', $insumo['nombre_insumo']) ?>" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="precio_unitario_pacc" class="form-label">Precio Unitario PACC (SIN IVA)</label>
                    <input type="number" step="0.01" name="precio_unitario_pacc" id="precio_unitario_pacc" class="form-control" value="<?= set_value('precio_unitario_pacc', $insumo['precio_unitario_pacc']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="presentacion" class="form-label">Presentación</label>
                    <input type="text" name="presentacion" id="presentacion" class="form-control" value="<?= set_value('presentacion', $insumo['presentacion']) ?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                    <input type="text" name="unidad_medida" id="unidad_medida" class="form-control" value="<?= set_value('unidad_medida', $insumo['unidad_medida']) ?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="caracteristicas" class="form-label">Características</label>
                    <textarea name="caracteristicas" id="caracteristicas" class="form-control" rows="4"><?= set_value('caracteristicas', $insumo['caracteristicas']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="tipo_insumo" class="form-label">Tipo de Insumo</label>
                    <select name="tipo_insumo" id="tipo_insumo" class="form-select" required>
                        <option value="activo" <?= set_select('tipo_insumo', 'activo', $insumo['tipo_insumo'] === 'activo') ?>>Activo</option>
                        <option value="material" <?= set_select('tipo_insumo', 'material', $insumo['tipo_insumo'] === 'material') ?>>Material</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="cuenta_sap" class="form-label">Cuenta SAP</label>
                    <input type="text" name="cuenta_sap" id="cuenta_sap" class="form-control" value="<?= set_value('cuenta_sap', $insumo['cuenta_sap']) ?>" maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="activo" <?= set_select('status', 'activo', $insumo['status'] === 'activo') ?>>Activo</option>
                        <option value="anulado" <?= set_select('status', 'anulado', $insumo['status'] === 'anulado') ?>>Anulado</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="<?= base_url('cat_insumos') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
