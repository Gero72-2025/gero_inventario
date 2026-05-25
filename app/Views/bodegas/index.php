<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Bodegas</h1>
        <a href="<?= base_url('bodegas/nuevo') ?>" class="btn btn-primary">Nueva bodega</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form id="searchForm" method="get" action="<?= base_url('bodegas') ?>" class="row g-2 align-items-center">
                <input type="hidden" name="deletedIds" id="deletedIdsField" value="<?= esc($deletedIds ?? '') ?>">
                <div class="col-md-8">
                    <input type="text" name="q" id="searchInput" class="form-control" placeholder="Filtrar por nombre o ubicación" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a id="clearSearchLink" href="<?= base_url('bodegas') ?>" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="bodegasTable">
                <thead class="table-light">
                <tr>
                    <th>Nombre Bodega</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($bodegas)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No hay bodegas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bodegas as $bodega): ?>
                        <tr data-bodega-id="<?= esc($bodega['id']) ?>">
                            <td><strong><?= esc($bodega['nombre_bodega']) ?></strong></td>
                            <td><?= esc($bodega['ubicacion']) ?></td>
                            <td>
                                <span class="badge bg-<?= $bodega['status'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($bodega['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($bodega['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('bodegas/ver/' . $bodega['id']) ?>" class="btn btn-sm btn-outline-info btn-view-bodega">Ver</a>
                                <button type="button" class="btn btn-sm btn-outline-warning btn-edit-bodega" data-url="<?= base_url('bodegas/editar/' . $bodega['id']) ?>">Editar</button>
                                <form action="<?= base_url('bodegas/eliminar/' . $bodega['id']) ?>" method="POST" class="d-inline-block ms-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-gero-confirm
                                            data-title="Eliminar bodega"
                                            data-message="¿Deseas eliminar esta bodega?"
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
        <nav aria-label="Paginación bodegas" class="d-inline-block">
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
const DELETED_BODEGAS_KEY = 'deletedBodegas';
const searchInput = document.getElementById('searchInput');
const deletedIdsField = document.getElementById('deletedIdsField');
const searchForm = document.getElementById('searchForm');
const clearSearchLink = document.getElementById('clearSearchLink');
const pageInfoLabel = document.getElementById('paginationPageInfo');
let currentPage = parseInt('<?= esc((string) $currentPage) ?>', 10) || 1;
const totalPages = parseInt('<?= esc((string) $totalPages) ?>', 10) || 1;

function getDeletedBodegaIds() {
    const raw = localStorage.getItem(DELETED_BODEGAS_KEY);
    return raw ? JSON.parse(raw) : [];
}

function saveDeletedBodegaId(id) {
    const deleted = getDeletedBodegaIds();
    if (!deleted.includes(id)) {
        deleted.push(id);
        localStorage.setItem(DELETED_BODEGAS_KEY, JSON.stringify(deleted));
    }
}

function updateDeletedIdsField() {
    deletedIdsField.value = getDeletedBodegaIds().join(',');
}

function addDeletedIdsToUrl(url) {
    const deletedIds = getDeletedBodegaIds().join(',');
    const parsed = new URL(url, window.location.origin);

    if (deletedIds !== '') {
        parsed.searchParams.set('deletedIds', deletedIds);
    } else {
        parsed.searchParams.delete('deletedIds');
    }

    return parsed.toString();
}

function navigateToPage(page, url) {
    const targetPage = Math.max(1, Math.min(totalPages, page));

    if (targetPage === currentPage || targetPage < 1 || targetPage > totalPages) {
        return;
    }

    currentPage = targetPage;

    if (pageInfoLabel) {
        pageInfoLabel.textContent = `Página ${currentPage} de ${totalPages}`;
    }

    document.querySelectorAll('.pagination-button').forEach(button => {
        const buttonPage = parseInt(button.dataset.page, 10);
        const parent = button.parentElement;

        if (Number.isNaN(buttonPage)) {
            return;
        }

        if (buttonPage === currentPage) {
            parent.classList.add('active');
            button.setAttribute('aria-current', 'page');
        } else {
            parent.classList.remove('active');
            button.removeAttribute('aria-current');
        }
    });

    window.location.assign(addDeletedIdsToUrl(url));
}

function updatePaginationLinks() {
    document.querySelectorAll('.pagination-button').forEach(button => {
        const url = button.dataset.url;
        const page = parseInt(button.dataset.page, 10);

        if (!url || url === '#' || Number.isNaN(page)) {
            return;
        }

        button.addEventListener('click', function() {
            if (button.parentElement.classList.contains('disabled')) {
                return;
            }

            const nextPage = Math.max(1, Math.min(totalPages, page));
            navigateToPage(nextPage, url);
        });
    });
}

function updateClearLink() {
    clearSearchLink.setAttribute('href', addDeletedIdsToUrl('<?= esc(base_url('bodegas')) ?>'));
}

function extractModalContent(html) {
    if (!html || typeof html !== 'string') {
        return html;
    }

    if (html.trim().startsWith('<!DOCTYPE') || html.match(/<html[\s>]/i)) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const container = doc.querySelector('.container-fluid') || doc.querySelector('.app-content') || doc.querySelector('main') || doc.body;
        return container ? container.innerHTML : html;
    }

    return html;
}

function showBodegaModal(url) {
    return fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        },
        credentials: 'same-origin',
    }).then(response => {
        if (!response.ok) {
            throw new Error('No se pudo cargar el detalle de la bodega.');
        }
        return response.text();
    }).then(html => {
        const content = extractModalContent(html);

        if (typeof window.GeroModal === 'object' && typeof window.GeroModal.show === 'function') {
            window.GeroModal.show({
                title: 'Detalle de Bodega',
                html: content,
                type: 'info',
                showCancel: true,
                cancelLabel: 'Cerrar',
                showConfirm: false,
            });
            return;
        }

        const modalEl = document.getElementById('geroModal');
        if (!modalEl) {
            throw new Error('No se encontró el modal del sistema.');
        }

        const contentEl = modalEl.querySelector('.modal-body');
        if (contentEl) {
            contentEl.innerHTML = content;
        }
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
}

function showEditModal(url) {
    return fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        },
        credentials: 'same-origin',
    }).then(response => {
        if (!response.ok) {
            throw new Error('No se pudo cargar el formulario de edición.');
        }
        return response.text();
    }).then(html => {
        const content = extractModalContent(html);

        if (typeof window.GeroModal !== 'object' || typeof window.GeroModal.show !== 'function') {
            throw new Error('No se encontró el sistema de modal.');
        }

        window.GeroModal.show({
            title: 'Editar Bodega',
            html: content,
            type: 'warning',
            showCancel: true,
            cancelLabel: 'Cerrar',
            showConfirm: true,
            confirmLabel: 'Guardar',
            onConfirm: function () {
                const modalEl = document.getElementById('geroModal');
                const form = modalEl ? modalEl.querySelector('form') : null;
                if (form) {
                    form.submit();
                }
            },
        });
    });
}

function syncDeletedIdsFromUrl() {
    const params = new URL(window.location.href).searchParams;
    const deletedIds = params.get('deletedIds') || '';

    if (deletedIds !== '') {
        const items = deletedIds.split(',').map(id => id.trim()).filter(id => id !== '');
        if (items.length > 0) {
            localStorage.setItem(DELETED_BODEGAS_KEY, JSON.stringify(items));
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    syncDeletedIdsFromUrl();
    updateDeletedIdsField();
    updatePaginationLinks();
    updateClearLink();

    searchForm.addEventListener('submit', function() {
        updateDeletedIdsField();
    });

    document.querySelectorAll('.btn-view-bodega').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            showBodegaModal(this.href).catch(error => {
                console.error(error);
                window.location.assign(this.href);
            });
        });
    });

    document.querySelectorAll('.btn-edit-bodega').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            showEditModal(this.dataset.url).catch(error => {
                console.error(error);
                window.location.assign(this.dataset.url);
            });
        });
    });
});
</script>
<?php $this->endSection(); ?>
