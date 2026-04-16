<?php
$errors = session('errors') ?? [];
?>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-6">
        <label for="alias" class="form-label">Alias</label>
        <input type="text" id="alias" name="alias" class="form-control" maxlength="50" required value="<?= esc(old('alias', $usuario['alias'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" maxlength="100" required value="<?= esc(old('email', $usuario['email'] ?? '')) ?>">
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">Contrasena <?= $requirePassword ? '' : '(opcional)' ?></label>
        <input type="password" id="password" name="password" class="form-control" <?= $requirePassword ? 'required' : '' ?>>
        <?php if (! $requirePassword): ?>
            <small class="text-muted">Solo completa este campo si deseas cambiar la contrasena actual.</small>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label for="token_recuperacion" class="form-label">Token recuperacion</label>
        <input type="text" id="token_recuperacion" name="token_recuperacion" class="form-control" maxlength="255" value="<?= esc(old('token_recuperacion', $usuario['token_recuperacion'] ?? '')) ?>">
    </div>

    <div class="col-md-4">
        <label for="esta_conectado" class="form-label">Esta conectado</label>
        <?php $estaConectado = (string) old('esta_conectado', (string) ($usuario['esta_conectado'] ?? '0')); ?>
        <select id="esta_conectado" name="esta_conectado" class="form-select">
            <option value="0" <?= $estaConectado === '0' ? 'selected' : '' ?>>No</option>
            <option value="1" <?= $estaConectado === '1' ? 'selected' : '' ?>>Si</option>
        </select>
    </div>

    <div class="col-md-4">
        <label for="ultimo_login" class="form-label">Ultimo login</label>
        <?php
        $ultimoLogin = (string) old('ultimo_login', $usuario['ultimo_login'] ?? '');
        $ultimoLogin = $ultimoLogin !== '' ? str_replace(' ', 'T', substr($ultimoLogin, 0, 16)) : '';
        ?>
        <input type="datetime-local" id="ultimo_login" name="ultimo_login" class="form-control" value="<?= esc($ultimoLogin) ?>">
    </div>

    <div class="col-md-4">
        <label for="id_usuario" class="form-label">ID Usuario (auditoria)</label>
        <input type="number" id="id_usuario" name="id_usuario" class="form-control" min="1" step="1" value="<?= esc(old('id_usuario', $usuario['id_usuario'] ?? (session('user_id') ?? ''))) ?>">
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
