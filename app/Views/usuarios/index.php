<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Usuarios</h1>
        <a href="<?= base_url('usuarios/nuevo') ?>" class="btn btn-primary">Nuevo usuario</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('usuarios') ?>" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" placeholder="Filtrar por alias o email" value="<?= esc($searchTerm ?? '') ?>">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
                <div class="col-md-auto">
                    <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Limpiar</a>
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
                    <th>Alias</th>
                    <th>Email</th>
                    <th>Tipo de rol</th>
                    <th>Conectado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= esc($usuario['id']) ?></td>
                            <td><?= esc($usuario['alias']) ?></td>
                            <td><?= esc($usuario['email']) ?></td>
                            <td><?= esc($usuario['tipo_rol'] ?? 'Sin rol') ?></td>
                            <td>
                                <?php if ((int) ($usuario['esta_conectado'] ?? 0) === 1): ?>
                                    <span class="badge text-bg-success">Si</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('usuarios/editar/' . $usuario['id']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form action="<?= base_url('usuarios/eliminar/' . $usuario['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_usuario" value="<?= esc(session('user_id') ?? '') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseas eliminar este usuario?')">Eliminar</button>
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
    $canNavigate = $totalPages > 2;
    ?>
    <div class="mt-3 text-center">
        <small class="text-muted d-block mb-2">Pagina <?= esc((string) $currentPage) ?> de <?= esc((string) $totalPages) ?></small>
        <nav aria-label="Paginacion usuarios" class="d-inline-block">
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
