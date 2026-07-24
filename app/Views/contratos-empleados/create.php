<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="mb-3">
        <h1 class="h3 mb-0">Crear nuevo contrato</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('contratos-empleados/guardar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?= $this->include('contratos-empleados/_form', ['contrato' => [], 'submitLabel' => 'Crear contrato']) ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
