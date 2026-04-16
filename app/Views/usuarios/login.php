<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 text-center mb-3">Iniciar sesion</h1>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                    <?php endif; ?>

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

                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario o email</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" value="<?= esc(old('usuario', '')) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contrasena</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">Ver</button>
                            </div>
                        </div>
                        <div class="mb-3 text-end">
                            <a href="<?= base_url('recuperar-password') ?>" class="small">Olvide mi contrasena</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const passwordInput = document.getElementById('password');
const togglePasswordBtn = document.getElementById('togglePassword');

if (passwordInput && togglePasswordBtn) {
    togglePasswordBtn.addEventListener('click', function () {
        const show = passwordInput.type === 'password';
        passwordInput.type = show ? 'text' : 'password';
        togglePasswordBtn.textContent = show ? 'Ocultar' : 'Ver';
    });
}
</script>
<?= $this->endSection() ?>
