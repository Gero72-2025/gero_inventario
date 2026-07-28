<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Contratos de Empleados</h1>
        <a href="<?= base_url('contratos-empleados/nuevo') ?>" class="btn btn-primary">Nuevo contrato</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('contratos-empleados') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Filtrar por número de contrato o empleado" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a href="<?= base_url('contratos-empleados') ?>" class="btn btn-outline-secondary">Limpiar</a>
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
                    <th>Número de contrato</th>
                    <th>Código</th>
                    <th>Empleado</th>
                    <th>Renglón</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Monto</th>
                    <th>Honorarios</th>
                    <th>Estado</th>
                    <th>Estado contrato</th>
                    <th>Actualizado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($contratos)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-4">No hay contratos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contratos as $contrato): ?>
                        <tr>
                            <td><?= esc($contrato['id']) ?></td>
                            <td><?= esc($contrato['numero_contrato']) ?></td>
                            <td><?= esc($contrato['codigo_contrato'] ?? '') ?></td>
                            <td><?= esc($contrato['nombre_completo'] ?? 'N/A') ?></td>
                            <td><?= esc($contrato['codigo_renglon'] ?? 'N/A') ?></td>
                            <td><?= esc($contrato['fecha_inicio']) ?></td>
                            <td><?= esc($contrato['fecha_fin']) ?></td>
                            <td>
                                <?php
                                    $monto = (float) $contrato['monto_contrato'];
                                    echo esc('Q ' . number_format($monto, 2, '.', ','));
                                ?>
                            </td>
                            <td>
                                <?php
                                    $pagos = isset($contrato['cantidad_pagos']) ? (int) $contrato['cantidad_pagos'] : 0;
                                    echo esc('Q ' . number_format(($pagos > 0 ? ((float) $contrato['monto_contrato'] / $pagos) : 0), 2, '.', ','));
                                ?>
                            </td>
                            <td>
                                <?php
                                    $estados = [
                                        'vigente' => 'Vigente',
                                        'vencido' => 'Vencido',
                                        'rescindido' => 'Rescindido'
                                    ];
                                    $badgeClass = match($contrato['estado_contrato']) {
                                        'vigente' => 'bg-success',
                                        'vencido' => 'bg-warning',
                                        'rescindido' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <span class="badge <?= esc($badgeClass) ?>">
                                    <?= esc($estados[$contrato['estado_contrato']] ?? $contrato['estado_contrato']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($contrato['status'] === 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($contrato['updated_at'] ?? '') ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (! empty($contrato['pdf_contrato_path'])): ?>
                                        <a href="<?= base_url('contratos-empleados/descargar-pdf/' . $contrato['id']) ?>" class="btn btn-outline-info" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('contratos-empleados/editar/' . $contrato['id']) ?>" class="btn btn-outline-secondary">Editar</a>
                                    <form action="<?= base_url('contratos-empleados/eliminar/' . $contrato['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                data-gero-confirm
                                                data-title="Eliminar contrato"
                                                data-message="¿Deseas eliminar este contrato? Esta acción no se puede deshacer."
                                                data-type="danger"
                                                data-confirm-label="Eliminar">Eliminar</button>
                                    </form>
                                </div>
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
        <small class="text-muted d-block mb-2">Pagina <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
        <nav aria-label="Paginacion contratos" class="d-inline-block">
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
