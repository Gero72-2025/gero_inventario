<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Empleados</h1>
        <a href="<?= base_url('empleados/nuevo') ?>" class="btn btn-primary">Nuevo empleado</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('empleados') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Filtrar por código, nombre o división" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a href="<?= base_url('empleados') ?>" class="btn btn-outline-secondary">Limpiar</a>
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
                    <th>Código</th>
                    <th>Nombre completo</th>
                    <th>Tipo de contrato</th>
                    <th>División</th>
                    <th>Estado</th>
                    <th>Actualizado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">No hay empleados registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?= esc($empleado['id']) ?></td>
                            <td><?= esc($empleado['codigo_empleado']) ?></td>
                            <td><?= esc($empleado['nombre_completo']) ?></td>
                            <td>
                                <?php
                                    $tipos = [
                                        '011' => 'Tipo 011',
                                        '012' => 'Tipo 012',
                                        'contratista' => 'Contratista'
                                    ];
                                    echo esc($tipos[$empleado['tipo_contrato']] ?? $empleado['tipo_contrato']);
                                ?>
                            </td>
                            <td><?= esc($empleado['nombre_division'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($empleado['status'] === 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($empleado['updated_at'] ?? '') ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('empleados/editar/' . $empleado['id']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form action="<?= base_url('empleados/eliminar/' . $empleado['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            data-gero-confirm
                                            data-title="Eliminar empleado"
                                            data-message="¿Deseas eliminar al empleado '<?= esc($empleado['nombre_completo']) ?>'?"
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
        <small class="text-muted d-block mb-2">Pagina <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
        <nav aria-label="Paginacion empleados" class="d-inline-block">
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
