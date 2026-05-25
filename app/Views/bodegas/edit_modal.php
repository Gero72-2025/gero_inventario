<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="mb-1">Editar Bodega</h4>
        <p class="text-muted mb-0">Modifica los datos de la bodega y guarda los cambios.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('bodegas/actualizar/' . $bodega['id']) ?>" method="POST" id="editBodegaForm">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nombre_bodega" class="form-label">Nombre Bodega <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="nombre_bodega"
                           name="nombre_bodega"
                           placeholder="Ej: Bodega Principal"
                           value="<?= esc($bodega['nombre_bodega']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="ubicacion"
                           name="ubicacion"
                           placeholder="Ej: Piso 2, Edificio A"
                           value="<?= esc($bodega['ubicacion']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">-- Seleccionar Estado --</option>
                        <option value="activo" <?= $bodega['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $bodega['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <hr>
                <h6 class="text-muted mb-3"><i class="fas fa-history"></i> Auditoría</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Creado:</label>
                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($bodega['created_at'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Última actualización:</label>
                        <p class="form-control-plaintext"><?= !empty($bodega['updated_at']) ? date('d/m/Y H:i', strtotime($bodega['updated_at'])) : 'N/A' ?></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
