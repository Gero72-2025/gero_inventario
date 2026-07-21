<?php
$errors = session('errors') ?? [];
?>

<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-6">
        <label for="id_solicitud" class="form-label">Solicitud</label>
        <select id="id_solicitud" name="id_solicitud" class="form-select" required>
            <option value="">Seleccione una solicitud</option>
            <?php foreach ($solicitudes as $solicitud): ?>
                <option value="<?= esc($solicitud['id']) ?>"
                    <?= (old('id_solicitud', $factura['id_solicitud'] ?? '') == $solicitud['id']) ? 'selected' : '' ?>>
                    <?= esc($solicitud['folio_fisico']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="id_proveedor" class="form-label">Proveedor</label>
        <select id="id_proveedor" name="id_proveedor" class="form-select" required>
            <option value="">Seleccione un proveedor</option>
            <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?= esc($proveedor['id']) ?>"
                    <?= (old('id_proveedor', $factura['id_proveedor'] ?? '') == $proveedor['id']) ? 'selected' : '' ?>>
                    <?= esc($proveedor['nombre_comercial']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="serie_factura" class="form-label">Serie de factura</label>
        <input type="text" id="serie_factura" name="serie_factura" class="form-control" maxlength="100" required
               value="<?= esc(old('serie_factura', $factura['serie_factura'] ?? '')) ?>">
    </div>

    <div class="col-md-6">
        <label for="numero_factura" class="form-label">Número de factura</label>
        <input type="text" id="numero_factura" name="numero_factura" class="form-control" maxlength="100" required
               value="<?= esc(old('numero_factura', $factura['numero_factura'] ?? '')) ?>">
    </div>

    <div class="col-md-6">
        <label for="monto_real_pagado" class="form-label">Monto real pagado</label>
        <input type="number" id="monto_real_pagado" name="monto_real_pagado" class="form-control" step="0.01" min="0"
               required value="<?= esc(old('monto_real_pagado', $factura['monto_real_pagado'] ?? '0.00')) ?>">
    </div>

    <div class="col-md-6">
        <label for="monto_vuelto_devuelto" class="form-label">Monto vuelto / devuelto</label>
        <input type="number" id="monto_vuelto_devuelto" name="monto_vuelto_devuelto" class="form-control" step="0.01" min="0"
               value="<?= esc(old('monto_vuelto_devuelto', $factura['monto_vuelto_devuelto'] ?? '0.00')) ?>">
    </div>

    <div class="col-md-6">
        <label for="fecha_pago" class="form-label">Fecha de pago</label>
        <input type="date" id="fecha_pago" name="fecha_pago" class="form-control" required
               value="<?= esc(old('fecha_pago', $factura['fecha_pago'] ?? '')) ?>">
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Estado</label>
        <select id="status" name="status" class="form-select" required>
            <option value="pagado" <?= (old('status', $factura['status'] ?? 'pagado') === 'pagado') ? 'selected' : '' ?>>Pagado</option>
            <option value="anulado" <?= (old('status', $factura['status'] ?? '') === 'anulado') ? 'selected' : '' ?>>Anulado</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('facturas-liquidacion') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>
