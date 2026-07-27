<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Detalle Insumo</h1>

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Código PACC</dt>
                <dd class="col-sm-9"><?= esc($insumo['codigo_pacc']) ?></dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9"><?= esc($insumo['nombre_insumo']) ?></dd>

                <dt class="col-sm-3">Renglón</dt>
                <dd class="col-sm-9"><?= esc($insumo['renglon_codigo']) ?> - <?= esc($insumo['renglon_descripcion']) ?></dd>

                <dt class="col-sm-3">Precio Sugerido</dt>
                <dd class="col-sm-9"><?= number_format((float)$insumo['precio_sugerido'], 2) ?></dd>

                <dt class="col-sm-3">Tipo</dt>
                <dd class="col-sm-9"><?= esc($insumo['tipo_insumo']) ?></dd>

                <dt class="col-sm-3">Cuenta SAP</dt>
                <dd class="col-sm-9"><?= esc($insumo['cuenta_sap']) ?></dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9"><?= esc($insumo['status']) ?></dd>

                <dt class="col-sm-3">Creado por</dt>
                <dd class="col-sm-9"><?= esc($insumo['usuario_creo'] ?? '') ?> - <?= esc($insumo['created_at']) ?></dd>

                <dt class="col-sm-3">Última actualización</dt>
                <dd class="col-sm-9"><?= esc($insumo['usuario_actualizo'] ?? '') ?> - <?= esc($insumo['updated_at']) ?></dd>

                <dt class="col-sm-3">Eliminado por</dt>
                <dd class="col-sm-9"><?= esc($insumo['usuario_elimino'] ?? '') ?> - <?= esc($insumo['deleted_at']) ?></dd>
            </dl>

            <div class="mt-3">
                <a href="<?= base_url('cat_insumos') ?>" class="btn btn-secondary">Volver</a>
                <a href="<?= base_url('cat_insumos/editar/' . $insumo['id']) ?>" class="btn btn-warning">Editar</a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
