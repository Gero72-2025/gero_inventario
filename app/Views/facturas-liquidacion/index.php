<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Facturas de liquidación</h1>
        <a href="<?= base_url('facturas-liquidacion/nuevo') ?>" class="btn btn-primary">Nueva factura</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('facturas-liquidacion') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Filtrar por factura, solicitud o proveedor" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a href="<?= base_url('facturas-liquidacion') ?>" class="btn btn-outline-secondary">Limpiar</a>
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
                    <th>Solicitud</th>
                    <th>Proveedor</th>
                    <th>Serie</th>
                    <th>Número</th>
                    <th>Pagado</th>
                    <th>Vuelto</th>
                    <th>Fecha pago</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($facturas)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-4">No hay facturas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($facturas as $factura): ?>
                        <tr>
                            <td><?= esc($factura['id']) ?></td>
                            <td><?= esc($factura['folio_solicitud'] ?? 'N/A') ?></td>
                            <td><?= esc($factura['nombre_proveedor'] ?? 'N/A') ?></td>
                            <td><?= esc($factura['serie_factura']) ?></td>
                            <td><?= esc($factura['numero_factura']) ?></td>
                            <td><?= number_format((float) $factura['monto_real_pagado'], 2) ?></td>
                            <td><?= number_format((float) $factura['monto_vuelto_devuelto'], 2) ?></td>
                            <td><?= esc($factura['fecha_pago']) ?></td>
                            <td>
                                <?php if ($factura['status'] === 'pagado'): ?>
                                    <span class="badge bg-success">Pagado</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Anulado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('facturas-liquidacion/editar/' . $factura['id']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form action="<?= base_url('facturas-liquidacion/eliminar/' . $factura['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            data-gero-confirm
                                            data-title="Eliminar factura"
                                            data-message="¿Deseas eliminar esta factura de liquidación?"
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
        <small class="text-muted d-block mb-2">Página <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
        <nav aria-label="Paginación facturas" class="d-inline-block">
            <ul class="pagination mb-0">
                <li class="page-item <?= ($canNavigate && ! empty($pagerDetails['hasPrevious'])) ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= ($canNavigate && ! empty($pagerDetails['hasPrevious'])) ? esc((string) ($pagerDetails['previous'] ?? '#')) : '#' ?>">Anterior</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?> <?= $canNavigate ? '' : 'disabled' ?>">
                        <a class="page-link" href="<?= $canNavigate ? esc($pager->getPageURI($i)) : '#' ?>"><?= esc((string) $i) ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($canNavigate && ! empty($pagerDetails['hasNext'])) ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= ($canNavigate && ! empty($pagerDetails['hasNext'])) ? esc((string) ($pagerDetails['next'] ?? '#')) : '#' ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<?= $this->endSection() ?>
