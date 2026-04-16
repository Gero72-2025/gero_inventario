<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Editar configuracion</h1>
            <form action="<?= base_url('configuraciones/actualizar/' . $configuracion['id']) ?>" method="post">
                <?= csrf_field() ?>
                <?php
                $submitLabel = 'Actualizar';
                echo view('configuraciones/_form', [
                    'submitLabel'   => $submitLabel,
                    'configuracion' => $configuracion,
                    'lockLlave'     => true,
                ]);
                ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
