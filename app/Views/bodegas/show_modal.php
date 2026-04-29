<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="mb-1">Detalles de Bodega</h4>
        <p class="text-muted mb-0">Información completa de la bodega seleccionada.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-warehouse"></i> Información Principal</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Nombre Bodega:</label>
                <h5><?= esc($bodega['nombre_bodega']) ?></h5>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Ubicación:</label>
                <h6 class="text-secondary"><i class="fas fa-map-marker-alt"></i> <?= esc($bodega['ubicacion']) ?></h6>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Estado:</label>
                <div>
                    <span class="badge bg-<?= $bodega['status'] === 'activo' ? 'success' : 'secondary' ?>" style="font-size: 0.95rem; padding: 0.5rem 0.75rem;">
                        <?= ucfirst(esc($bodega['status'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-history"></i> Información de Auditoría</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Creado en:</label>
                    <p class="fw-bold"><?= date('d/m/Y H:i:s', strtotime($bodega['created_at'])) ?></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Usuario que creó:</label>
                    <p class="fw-bold"><?= esc($bodega['usuario_creo'] ?? 'Sistema') ?></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Última actualización:</label>
                    <p class="fw-bold"><?= !empty($bodega['updated_at']) ? date('d/m/Y H:i:s', strtotime($bodega['updated_at'])) : 'N/A' ?></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Usuario que actualizó:</label>
                    <p class="fw-bold"><?= esc($bodega['usuario_actualizo'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6 mb-0">
                    <label class="form-label text-muted">Eliminado en:</label>
                    <p class="fw-bold"><?= !empty($bodega['deleted_at']) ? date('d/m/Y H:i:s', strtotime($bodega['deleted_at'])) : 'Activo' ?></p>
                </div>
                <div class="col-md-6 mb-0">
                    <label class="form-label text-muted">Usuario que eliminó:</label>
                    <p class="fw-bold"><?= esc($bodega['usuario_elimino'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
