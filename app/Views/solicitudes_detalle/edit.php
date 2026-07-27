<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <h3>Editar detalle de solicitud</h3>

    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach(session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('solicitudes-detalle/actualizar/' . $record['id']) ?>">
        <?= $this->include('solicitudes_detalle/_form') ?>

        <div class="mt-3">
            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
            <a href="<?= base_url('solicitudes-detalle') ?>" class="btn btn-sm btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
