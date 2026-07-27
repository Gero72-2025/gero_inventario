<?php
/**
 * Variables disponibles:
 *  - $insumos (array)
 *  - $solicitudes (array)
 *  - $record (array) opcional cuando se edita
 */
$rec = $record ?? null;
?>

<div class="row g-2">
    <div class="col-md-4">
        <label class="form-label">Solicitud</label>
        <select name="id_solicitud" class="form-select form-select-sm" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($solicitudes as $s): ?>
                <option value="<?= esc($s['id']) ?>" <?= isset($rec) && $rec['id_solicitud']==$s['id'] ? 'selected' : '' ?>><?= esc($s['id']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Insumo</label>
        <select name="id_insumo" class="form-select form-select-sm" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($insumos as $i): ?>
                <option value="<?= esc($i['id']) ?>" <?= isset($rec) && $rec['id_insumo']==$i['id'] ? 'selected' : '' ?>><?= esc($i['nombre_insumo']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" class="form-control form-control-sm" required value="<?= esc(old('cantidad', $rec['cantidad'] ?? '')) ?>" />
    </div>

    <div class="col-md-2">
        <label class="form-label">Precio Unitario</label>
        <input type="text" name="precio_unitario_solicitado" class="form-control form-control-sm" required value="<?= esc(old('precio_unitario_solicitado', $rec['precio_unitario_solicitado'] ?? '')) ?>" />
    </div>

    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select form-select-sm">
            <option value="activo" <?= isset($rec) && $rec['status']=='activo' ? 'selected' : '' ?>>Activo</option>
            <option value="anulado" <?= isset($rec) && $rec['status']=='anulado' ? 'selected' : '' ?>>Anulado</option>
        </select>
    </div>
</div>
