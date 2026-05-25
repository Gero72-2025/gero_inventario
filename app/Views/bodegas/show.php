<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Encabezado con botones de acción -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><?= $heading ?? 'Detalles de Bodega' ?></h1>
                </div>
                <div class="btn-group" role="group">
                    <a href="<?= base_url('bodegas/edit/' . $bodega['id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="<?= base_url('bodegas') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Tarjeta de Detalles -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-warehouse"></i> Información Principal</h5>
                </div>
                <div class="card-body">
                    <!-- Nombre Bodega -->
                    <div class="mb-4">
                        <label class="form-label text-muted">Nombre Bodega:</label>
                        <h5><?= htmlspecialchars($bodega['nombre_bodega']) ?></h5>
                    </div>

                    <!-- Ubicación -->
                    <div class="mb-4">
                        <label class="form-label text-muted">Ubicación:</label>
                        <h6 class="text-secondary"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($bodega['ubicacion']) ?></h6>
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label class="form-label text-muted">Estado:</label>
                        <div>
                            <span class="badge bg-<?= $bodega['status'] === 'activo' ? 'success' : 'secondary' ?>" style="font-size: 0.95rem; padding: 0.5rem 0.75rem;">
                                <?= ucfirst($bodega['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Auditoría -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Información de Auditoría</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Creación -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Creado en:</label>
                            <p class="fw-bold"><?= date('d/m/Y H:i:s', strtotime($bodega['created_at'])) ?></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Usuario que creó:</label>
                            <p class="fw-bold"><?= htmlspecialchars($bodega['usuario_creo'] ?? 'Sistema') ?></p>
                        </div>

                        <!-- Actualización -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Última actualización:</label>
                            <p class="fw-bold"><?= !empty($bodega['updated_at']) ? date('d/m/Y H:i:s', strtotime($bodega['updated_at'])) : 'N/A' ?></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Usuario que actualizó:</label>
                            <p class="fw-bold"><?= htmlspecialchars($bodega['usuario_actualizo'] ?? 'N/A') ?></p>
                        </div>

                        <!-- Eliminación (si aplica) -->
                        <div class="col-md-6 mb-0">
                            <label class="form-label text-muted">Eliminado en:</label>
                            <p class="fw-bold"><?= !empty($bodega['deleted_at']) ? date('d/m/Y H:i:s', strtotime($bodega['deleted_at'])) : 'Activo' ?></p>
                        </div>

                        <div class="col-md-6 mb-0">
                            <label class="form-label text-muted">Usuario que eliminó:</label>
                            <p class="fw-bold"><?= htmlspecialchars($bodega['usuario_elimino'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Acción Principal -->
            <div class="d-flex gap-2 mt-4">
                <a href="<?= base_url('bodegas/edit/' . $bodega['id']) ?>" class="btn btn-warning flex-grow-1">
                    <i class="fas fa-edit"></i> Editar esta Bodega
                </a>
                <a href="<?= base_url('bodegas') ?>" class="btn btn-outline-secondary flex-grow-1">
                    <i class="fas fa-list"></i> Ver todas las Bodegas
                </a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
