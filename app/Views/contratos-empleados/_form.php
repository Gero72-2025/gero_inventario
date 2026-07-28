<?php
$errors = session('errors') ?? [];
$contrato = $contrato ?? [];
$submitLabel = $submitLabel ?? 'Guardar';
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

<div class="mb-3">
    <label for="numero_contrato" class="form-label">Número de contrato</label>
    <input
        type="text"
        id="numero_contrato"
        name="numero_contrato"
        class="form-control"
        maxlength="100"
        required
        value="<?= esc(old('numero_contrato', $contrato['numero_contrato'] ?? '')) ?>"
    >
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="expediente" class="form-label">Expediente</label>
        <input
            type="text"
            id="expediente"
            name="expediente"
            class="form-control"
            maxlength="100"
            value="<?= esc(old('expediente', $contrato['expediente'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="codigo_contrato" class="form-label">Código de contrato</label>
        <input
            type="text"
            id="codigo_contrato"
            name="codigo_contrato"
            class="form-control"
            maxlength="100"
            required
            value="<?= esc(old('codigo_contrato', $contrato['codigo_contrato'] ?? '')) ?>"
        >
    </div>
</div>

<div class="mb-3">
    <label for="id_empleado" class="form-label">Empleado</label>
    <select id="id_empleado" name="id_empleado" class="form-select" required>
        <option value="">-- Seleccionar empleado --</option>
        <?php foreach ($empleados as $empleado): ?>
            <option value="<?= esc($empleado['id']) ?>" <?= (old('id_empleado', $contrato['id_empleado'] ?? '') == $empleado['id']) ? 'selected' : '' ?>>
                <?= esc($empleado['nombre_completo']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="fecha_aceptacion_contrato" class="form-label">Fecha de aceptación</label>
        <input
            type="date"
            id="fecha_aceptacion_contrato"
            name="fecha_aceptacion_contrato"
            class="form-control"
            required
            value="<?= esc(old('fecha_aceptacion_contrato', $contrato['fecha_aceptacion_contrato'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="renglon" class="form-label">Renglón</label>
        <select id="renglon" name="renglon" class="form-select" required>
            <option value="">-- Seleccionar renglón --</option>
            <?php foreach ($renglones as $renglonItem): ?>
                <option value="<?= esc($renglonItem['id']) ?>" data-codigo="<?= esc($renglonItem['codigo_renglon']) ?>" <?= (old('renglon', $contrato['renglon'] ?? '') == $renglonItem['id']) ? 'selected' : '' ?>>
                    <?= esc($renglonItem['codigo_renglon']) ?> - <?= esc($renglonItem['descripcion']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="codigo_renglon" class="form-label">Código de renglón</label>
    <input
        type="text"
        id="codigo_renglon"
        name="codigo_renglon"
        class="form-control"
        maxlength="100"
        value="<?= esc(old('codigo_renglon', $contrato['codigo_renglon'] ?? '')) ?>"
    >
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
        <input
            type="date"
            id="fecha_inicio"
            name="fecha_inicio"
            class="form-control"
            required
            value="<?= esc(old('fecha_inicio', $contrato['fecha_inicio'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="fecha_fin" class="form-label">Fecha de fin</label>
        <input
            type="date"
            id="fecha_fin"
            name="fecha_fin"
            class="form-control"
            required
            value="<?= esc(old('fecha_fin', $contrato['fecha_fin'] ?? '')) ?>"
        >
    </div>
</div>

<div class="mb-3">
    <label for="monto_contrato" class="form-label">Monto del contrato</label>
    <input
        type="number"
        id="monto_contrato"
        name="monto_contrato"
        class="form-control"
        step="0.01"
        min="0"
        required
        value="<?= esc(old('monto_contrato', $contrato['monto_contrato'] ?? '')) ?>"
    >
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="monto_texto" class="form-label">Monto en letras</label>
        <input
            type="text"
            id="monto_texto"
            name="monto_texto"
            class="form-control"
            maxlength="255"
            value="<?= esc(old('monto_texto', $contrato['monto_texto'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="cantidad_pagos" class="form-label">Cantidad de pagos</label>
        <input
            type="number"
            id="cantidad_pagos"
            name="cantidad_pagos"
            class="form-control"
            min="1"
            value="<?= esc(old('cantidad_pagos', $contrato['cantidad_pagos'] ?? '')) ?>"
            required
        >
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="puente_financiamiento" class="form-label">Puente de financiamiento</label>
        <input
            type="text"
            id="puente_financiamiento"
            name="puente_financiamiento"
            class="form-control"
            maxlength="255"
            value="<?= esc(old('puente_financiamiento', $contrato['puente_financiamiento'] ?? '')) ?>"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="honorarios" class="form-label">Honorarios</label>
        <input
            type="text"
            id="honorarios"
            name="honorarios_display"
            class="form-control"
            readonly
            value="<?php
                $monto = (float) str_replace(',', '.', old('monto_contrato', $contrato['monto_contrato'] ?? '0'));
                $pagos = (int) old('cantidad_pagos', $contrato['cantidad_pagos'] ?? 0);
                echo esc(($pagos > 0) ? number_format($monto / $pagos, 2, '.', ',') : '0.00');
            ?>"
        >
    </div>
</div>

<div class="mb-3">
    <label for="estado_contrato" class="form-label">Estado del contrato</label>
    <select id="estado_contrato" name="estado_contrato" class="form-select" required>
        <option value="">-- Seleccionar estado --</option>
        <option value="vigente" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'vigente') ? 'selected' : '' ?>>
            Vigente
        </option>
        <option value="vencido" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'vencido') ? 'selected' : '' ?>>
            Vencido
        </option>
        <option value="rescindido" <?= (old('estado_contrato', $contrato['estado_contrato'] ?? '') === 'rescindido') ? 'selected' : '' ?>>
            Rescindido
        </option>
    </select>
</div>

<div class="mb-3">
    <label for="pdf_contrato" class="form-label">
        Archivo PDF del contrato
        <?php if (! empty($submitLabel) && strpos($submitLabel, 'Actualizar') !== false): ?>
            <small class="text-muted">(opcional, deja en blanco para mantener el actual)</small>
        <?php else: ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
    <input
        type="file"
        id="pdf_contrato"
        name="pdf_contrato"
        class="form-control"
        accept=".pdf"
        <?= (! empty($submitLabel) && strpos($submitLabel, 'Actualizar') !== false) ? '' : 'required' ?>
    >
    <small class="form-text text-muted">Máximo 5 MB. Solo archivos PDF.</small>
    <?php if (! empty($contrato['pdf_contrato_path'])): ?>
        <div class="mt-2">
            <small class="text-success">
                <i class="fas fa-check-circle"></i> Archivo actual:
                <a href="<?= base_url('contratos-empleados/descargar-pdf/' . $contrato['id']) ?>" target="_blank">
                    Descargar
                </a>
            </small>
        </div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
        <option value="activo" <?= (old('status', $contrato['status'] ?? 'activo') === 'activo') ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="inactivo" <?= (old('status', $contrato['status'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
    <a href="<?= base_url('contratos-empleados') ?>" class="btn btn-outline-secondary">Cancelar</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const montoInput = document.getElementById('monto_contrato');
        const pagosInput = document.getElementById('cantidad_pagos');
        const honorariosInput = document.getElementById('honorarios');
        const renglonSelect = document.getElementById('renglon');
        const codigoRenglonInput = document.getElementById('codigo_renglon');

        function updateHonorarios() {
            const monto = parseFloat(montoInput.value.replace(',', '.')) || 0;
            const pagos = parseInt(pagosInput.value, 10) || 0;
            honorariosInput.value = pagos > 0 ? (monto / pagos).toFixed(2) : '0.00';
        }

        function updateCodigoRenglon() {
            const selectedOption = renglonSelect.options[renglonSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.codigo) {
                codigoRenglonInput.value = selectedOption.dataset.codigo;
            }
        }

        if (montoInput && pagosInput && honorariosInput) {
            montoInput.addEventListener('input', updateHonorarios);
            pagosInput.addEventListener('input', updateHonorarios);
        }

        if (renglonSelect && codigoRenglonInput) {
            renglonSelect.addEventListener('change', updateCodigoRenglon);
        }
    });
</script>
