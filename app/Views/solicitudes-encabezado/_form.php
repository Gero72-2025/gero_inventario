<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="mb-3">
    <label for="id_division" class="form-label">División <span class="text-danger">*</span></label>
    <select id="id_division" name="id_division" class="form-select <?= session('errors.id_division') ? 'is-invalid' : '' ?>" required>
        <option value="">Selecciona una división</option>
        <?php foreach (($divisiones ?? []) as $division): ?>
            <option value="<?= esc($division['id']) ?>" <?= old('id_division') == $division['id'] || ($solicitud && $solicitud['id_division'] == $division['id']) ? 'selected' : '' ?>>
                <?= esc($division['nombre_division']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if ($error = session('errors.id_division')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="id_etapa" class="form-label">Etapa <span class="text-danger">*</span></label>
    <select id="id_etapa" name="id_etapa" class="form-select <?= session('errors.id_etapa') ? 'is-invalid' : '' ?>" required>
        <option value="">Selecciona una etapa</option>
        <?php foreach (($etapas ?? []) as $etapa): ?>
            <option value="<?= esc($etapa['id']) ?>" <?= old('id_etapa') == $etapa['id'] || ($solicitud && $solicitud['id_etapa'] == $etapa['id']) ? 'selected' : '' ?>>
                <?= esc($etapa['nombre_etapa']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if ($error = session('errors.id_etapa')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="folio_fisico" class="form-label">Folio Físico <span class="text-danger">*</span></label>
    <input type="text" id="folio_fisico" name="folio_fisico" class="form-control <?= session('errors.folio_fisico') ? 'is-invalid' : '' ?>" value="<?= old('folio_fisico', $solicitud['folio_fisico'] ?? '') ?>" maxlength="255" required>
    <?php if ($error = session('errors.folio_fisico')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="monto_estimado_total" class="form-label">Monto Estimado Total <span class="text-danger">*</span></label>
    <input type="number" id="monto_estimado_total" name="monto_estimado_total" class="form-control <?= session('errors.monto_estimado_total') ? 'is-invalid' : '' ?>" value="<?= old('monto_estimado_total', $solicitud['monto_estimado_total'] ?? '') ?>" step="0.01" min="0" required>
    <?php if ($error = session('errors.monto_estimado_total')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="pdf_firmado" class="form-label">PDF Firmado <?= empty($solicitud) ? '<span class="text-danger">*</span>' : '' ?></label>
    <?php if ($solicitud && $solicitud['pdf_firmado_path']): ?>
        <div class="alert alert-info mb-2">
            <i class="fas fa-file-pdf"></i> Archivo actual:
            <a href="<?= base_url('solicitudes-encabezado/descargar-pdf/' . $solicitud['id']) ?>" class="alert-link">Descargar PDF</a>
            <br><small>Carga un nuevo archivo para reemplazarlo.</small>
        </div>
    <?php endif; ?>
    <input type="file" id="pdf_firmado" name="pdf_firmado" class="form-control <?= session('errors.pdf_firmado') ? 'is-invalid' : '' ?>" accept=".pdf" <?= empty($solicitud) ? 'required' : '' ?>>
    <small class="text-muted">Solo archivos PDF. Máximo 5MB.</small>
    <?php if ($error = session('errors.pdf_firmado')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
    <select id="status" name="status" class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" required>
        <option value="">Selecciona un estado</option>
        <option value="activo" <?= old('status', $solicitud['status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= old('status', $solicitud['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
    </select>
    <?php if ($error = session('errors.status')): ?>
        <div class="invalid-feedback d-block"><?= esc($error) ?></div>
    <?php endif; ?>
</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="<?= base_url('solicitudes-encabezado') ?>" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><?= $submitLabel ?></button>
</div>
