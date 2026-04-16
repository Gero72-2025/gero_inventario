<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Restablecer contrasena</h1>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>

                    <?php $errors = session('errors') ?? []; ?>
                    <?php if (! empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('reset-password') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token) ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva contrasena</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">Ver</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmar contrasena</label>
                            <div class="input-group">
                                <input type="password" id="password_confirm" name="password_confirm" class="form-control" minlength="8" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirm">Ver</button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function bindToggle(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);
    if (!input || !button) {
        return;
    }

    button.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Ocultar' : 'Ver';
    });
}

bindToggle('password', 'togglePassword');
bindToggle('password_confirm', 'togglePasswordConfirm');
</script>
<?= $this->endSection() ?>
