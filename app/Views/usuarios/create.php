<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Nuevo usuario</h1>

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

            <form action="<?= base_url('usuarios') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="alias" class="form-label">Alias</label>
                        <input type="text" id="alias" name="alias" class="form-control" maxlength="50" required value="<?= esc(old('alias', '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email', '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contrasena</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Mostrar u ocultar contrasena">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="id_rol" class="form-label">Rol</label>
                        <select id="id_rol" name="id_rol" class="form-select" required>
                            <option value="">Selecciona un rol</option>
                            <?php foreach (($roles ?? []) as $rol): ?>
                                <option value="<?= esc($rol['id']) ?>" <?= old('id_rol') == $rol['id'] ? 'selected' : '' ?>>
                                    <?= esc($rol['nombre_rol']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
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
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        togglePasswordBtn.innerHTML = isPassword
            ? '<i class="bi bi-eye-slash"></i>'
            : '<i class="bi bi-eye"></i>';
    });
}
</script>
<?= $this->endSection() ?>
