<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= esc($heading) ?></h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('solicitudes-encabezado') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php
                $submitLabel = 'Crear';
                $solicitud = null;
                include '_form.php';
                ?>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
