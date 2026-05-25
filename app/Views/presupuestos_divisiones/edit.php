<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Editar presupuesto por división</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('presupuestos-divisiones/actualizar/' . $presupuestoDivision['id']) ?>" method="post">
                <?= csrf_field() ?>
                <?= view('presupuestos_divisiones/_form', ['submitLabel' => 'Actualizar', 'presupuestoDivision' => $presupuestoDivision, 'ejerciciosFiscales' => $ejerciciosFiscales, 'divisiones' => $divisiones]) ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
