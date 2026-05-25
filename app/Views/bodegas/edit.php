<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Encabezado -->
            <div class="mb-4">
                <h1><?= $heading ?? 'Editar Bodega' ?></h1>
                <p class="text-muted">Modifica los datos de la bodega.</p>
            </div>

            <!-- Mensajes de Alerta -->
            <?php $errors = session('errors') ?? []; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <strong>Errores de validación:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="<?= base_url('bodegas/actualizar/' . $bodega['id']) ?>" method="POST" id="formBodega">
                        <?= csrf_field() ?>

                        <!-- Nombre Bodega -->
                        <div class="mb-3">
                            <label for="nombre_bodega" class="form-label">
                                Nombre Bodega <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= session()->has('errors') && isset($errors['nombre_bodega']) ? 'is-invalid' : '' ?>" 
                                   id="nombre_bodega" 
                                   name="nombre_bodega" 
                                   placeholder="Ej: Bodega Principal" 
                                   value="<?= old('nombre_bodega', $bodega['nombre_bodega'] ?? '') ?>"
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['nombre_bodega'] ?? 'Campo requerido' ?>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">
                                Ubicación <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= session()->has('errors') && isset($errors['ubicacion']) ? 'is-invalid' : '' ?>" 
                                   id="ubicacion" 
                                   name="ubicacion" 
                                   placeholder="Ej: Piso 2, Edificio A" 
                                   value="<?= old('ubicacion', $bodega['ubicacion'] ?? '') ?>"
                                   required>
                            <div class="invalid-feedback">
                                <?= $errors['ubicacion'] ?? 'Campo requerido' ?>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= session()->has('errors') && isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">-- Seleccionar Estado --</option>
                                <option value="activo" <?= old('status', $bodega['status'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= old('status', $bodega['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                            <div class="invalid-feedback">
                                <?= $errors['status'] ?? 'Campo requerido' ?>
                            </div>
                        </div>

                        <!-- Información de Auditoría (solo lectura) -->
                        <hr>
                        <h6 class="text-muted mb-3"><i class="fas fa-history"></i> Información de Auditoría</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Creado:</label>
                                <p class="form-control-plaintext">
                                    <?= date('d/m/Y H:i', strtotime($bodega['created_at'])) ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Última actualización:</label>
                                <p class="form-control-plaintext">
                                    <?= !empty($bodega['updated_at']) ? date('d/m/Y H:i', strtotime($bodega['updated_at'])) : 'N/A' ?>
                                </p>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="<?= base_url('bodegas') ?>" class="btn btn-outline-secondary flex-grow-1">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
            </div>
        </div>
    </div>
</div>

<script>
// Validación de formulario
document.getElementById('formBodega').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
<?php $this->endSection(); ?>
