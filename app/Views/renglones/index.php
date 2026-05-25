<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Renglones</h1>
        <a href="<?= base_url('renglones/nuevo') ?>" class="btn btn-primary">Nuevo Renglón</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form id="searchForm" method="get" action="<?= base_url('renglones') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" id="searchInput" class="form-control" placeholder="Filtrar por código o descripción" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a id="clearSearchLink" href="<?= base_url('renglones') ?>" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="renglonesTable">
                <thead class="table-light">
                <tr>
                    <th>Código Renglón</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($renglones)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No hay renglones registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($renglones as $renglon): ?>
                        <tr data-renglon-id="<?= esc($renglon['id']) ?>">
                            <td><strong><?= esc($renglon['codigo_renglon']) ?></strong></td>
                            <td><?= esc(substr($renglon['descripcion'], 0, 50)) ?><?= strlen($renglon['descripcion']) > 50 ? '...' : '' ?></td>
                            <td>
                                <span class="badge bg-<?= $renglon['status'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($renglon['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($renglon['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('renglones/ver/' . $renglon['id']) ?>" class="btn btn-sm btn-outline-info">Ver</a>
                                <a href="<?= base_url('renglones/editar/' . $renglon['id']) ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                <form action="<?= base_url('renglones/eliminar/' . $renglon['id']) ?>" method="POST" class="d-inline-block ms-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-gero-confirm
                                            data-title="Eliminar renglón"
                                            data-message="¿Deseas eliminar este renglón?"
                                            data-type="danger"
                                            data-confirm-label="Eliminar">
                                        Eliminar
                                    </button>
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
    $hasPrevious = ! empty($pagerDetails['previous']);
    $hasNext = ! empty($pagerDetails['next']);
    ?>
    <div class="mt-3 text-center">
        <small id="paginationPageInfo" class="text-muted d-block mb-2">Página <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
        <nav aria-label="Paginación renglones" class="d-inline-block">
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
<?php $this->endSection(); ?>
