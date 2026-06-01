<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Solicitudes Encabezado</h1>
        <a href="<?= base_url('solicitudes-encabezado/nuevo') ?>" class="btn btn-primary">Nueva solicitud</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('solicitudes-encabezado') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Filtrar por folio, división, etapa o usuario" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a href="<?= base_url('solicitudes-encabezado') ?>" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Folio Físico</th>
                        <th>División</th>
                        <th>Etapa</th>
                        <th>Monto Estimado</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr><td colspan="9" class="text-center py-4">No hay registros.</td></tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?= esc($solicitud['id']) ?></td>
                                <td><?= esc($solicitud['folio_fisico']) ?></td>
                                <td><?= esc($solicitud['nombre_division'] ?? 'Sin división') ?></td>
                                <td><?= esc($solicitud['nombre_etapa'] ?? 'Sin etapa') ?></td>
                                <td><?= esc(number_format($solicitud['monto_estimado_total'], 2, '.', ',')) ?></td>
                                <td><?= esc($solicitud['nombre_usuario'] ?? 'Sin usuario') ?></td>
                                <td>
                                    <span class="badge bg-<?= $solicitud['status'] === 'activo' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($solicitud['status']) ?>
                                    </span>
                                </td>
                                <td><?= esc($solicitud['updated_at'] ?? '') ?></td>
                                <td class="text-end">
                                    <a href="<?= base_url('solicitudes-encabezado/ver/' . $solicitud['id']) ?>" class="btn btn-sm btn-outline-info">Ver</a>
                                    <a href="<?= base_url('solicitudes-encabezado/editar/' . $solicitud['id']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form action="<?= base_url('solicitudes-encabezado/eliminar/' . $solicitud['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                data-gero-confirm
                                                data-title="Eliminar solicitud"
                                                data-message="¿Deseas eliminar la solicitud con folio '<?= esc($solicitud['folio_fisico']) ?>'? Esta acción no se puede deshacer."
                                                data-type="danger"
                                                data-confirm-label="Eliminar">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $pagerDetails = $pager->getDetails();
    $totalPages = (int) ($pagerDetails['pageCount'] ?? 1);
    $currentPage = (int) ($pagerDetails['currentPage'] ?? 1);
    $canNavigate = $totalPages > 1;
    ?>
    <div class="mt-3 text-center">
        <small class="text-muted d-block mb-2">
            Página <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?>
        </small>
        <nav aria-label="Paginación" class="d-inline-block">
            <ul class="pagination mb-0">
                <li class="page-item <?= ($canNavigate && !empty($pagerDetails['hasPrevious'])) ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= ($canNavigate && !empty($pagerDetails['hasPrevious'])) ? esc((string) ($pagerDetails['previous'] ?? '#')) : '#' ?>">Anterior</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $canNavigate ? esc($pager->getPageURI($i)) : '#' ?>">
                            <?= esc((string) $i) ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($canNavigate && !empty($pagerDetails['hasNext'])) ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= ($canNavigate && !empty($pagerDetails['hasNext'])) ? esc((string) ($pagerDetails['next'] ?? '#')) : '#' ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?= $this->endSection() ?>
