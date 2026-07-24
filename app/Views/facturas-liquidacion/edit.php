<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Editar factura de liquidación</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('facturas-liquidacion/actualizar/' . $factura['id']) ?>" method="post">
                <?= csrf_field() ?>
                <?= view('facturas-liquidacion/_form', [
                    'submitLabel' => 'Actualizar',
                    'factura' => $factura,
                    'solicitudes' => $solicitudes,
                    'proveedores' => $proveedores,
                ]) ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
