<?php /** @var array $solicitudes_detalle */ ?>
<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <h3>Detalle de Solicitudes</h3>

    <div class="mb-3 d-flex justify-content-between">
        <form method="get" action="<?= base_url('solicitudes-detalle') ?>" class="d-flex">
            <input type="text" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Buscar..." />
            <button class="btn btn-sm btn-outline-primary ms-2">Buscar</button>
        </form>

        <a href="<?= base_url('solicitudes-detalle/nuevo') ?>" class="btn btn-sm btn-primary">Nuevo</a>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitud</th>
                    <th>Insumo</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($solicitudes_detalle) && is_array($solicitudes_detalle)): ?>
                    <?php foreach ($solicitudes_detalle as $row): ?>
                        <tr>
                            <td><?= esc($row['id']) ?></td>
                            <td><?= esc($row['id_solicitud']) ?></td>
                            <td><?= esc($row['insumo_nombre'] ?? '') ?></td>
                            <td><?= esc($row['cantidad']) ?></td>
                                <td><?= number_format((float) $row['precio_unitario_solicitado'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= ($row['status'] ?? '') === 'activo' ? 'success' : 'secondary' ?>"><?= ucfirst($row['status'] ?? '') ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('solicitudes-detalle/editar/' . $row['id']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form method="post" action="<?= base_url('solicitudes-detalle/eliminar/' . $row['id']) ?>" style="display:inline-block" class="ms-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-gero-confirm data-title="Eliminar registro" data-message="¿Deseas eliminar este registro? Esta acción no se puede deshacer." data-type="danger" data-confirm-label="Eliminar">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-3">No se encontraron registros</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        $pagerDetails = $pager->getDetails();
        $totalPages = (int) ($pagerDetails['pageCount'] ?? 1);
        $currentPage = (int) ($pagerDetails['currentPage'] ?? 1);
        $hasPrevious = ! empty($pagerDetails['previous']);
        $hasNext = ! empty($pagerDetails['next']);
        ?>

        <div class="mt-3 text-center">
            <small class="text-muted d-block mb-2">Página <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
            <nav aria-label="Paginación solicitudes detalle" class="d-inline-block">
                <ul class="pagination mb-0">
                    <li class="page-item <?= $hasPrevious ? '' : 'disabled' ?>">
                        <button type="button" class="page-link pagination-button" data-page="<?= max(1, $currentPage - 1) ?>" data-url="<?= $hasPrevious ? esc($pagerDetails['previous']) : '#' ?>">Anterior</button>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <button type="button" class="page-link pagination-button" data-page="<?= $i ?>" data-url="<?= esc($pager->getPageURI($i)) ?>" <?= $i === $currentPage ? 'aria-current="page"' : '' ?>><?= esc((string) $i) ?></button>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $hasNext ? '' : 'disabled' ?>">
                        <button type="button" class="page-link pagination-button" data-page="<?= min($totalPages, $currentPage + 1) ?>" data-url="<?= $hasNext ? esc($pagerDetails['next']) : '#' ?>">Siguiente</button>
                    </li>
                </ul>
            </nav>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paginationButtons = document.querySelectorAll('.pagination-button');
        paginationButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        });
    });
    </script>

<?= $this->endSection() ?>
