<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 mb-3">Nuevo ejercicio fiscal</h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('ejercicios-fiscales/guardar') ?>" method="post">
                <?= csrf_field() ?>
                <?= view('ejercicios_fiscales/_form', ['submitLabel' => 'Guardar']) ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
