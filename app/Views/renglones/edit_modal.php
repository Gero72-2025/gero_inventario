<?php
/**
 * Modal para editar renglón (AJAX)
 * Se carga dentro del GeroModal
 */
?>
<form id="editRenglonesForm" action="<?= base_url('renglones/actualizar/' . $renglon['id']) ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="codigo_renglon" class="form-label">Código Renglón <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control" 
               id="codigo_renglon" 
               name="codigo_renglon" 
               value="<?= esc($renglon['codigo_renglon']) ?>" 
               maxlength="100" 
               required>
        <small class="form-text text-muted">Campo requerido</small>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
        <textarea class="form-control" 
                  id="descripcion" 
                  name="descripcion" 
                  rows="4" 
                  required><?= esc($renglon['descripcion']) ?></textarea>
        <small class="form-text text-muted">Campo requerido</small>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
        <select class="form-select" id="status" name="status" required>
            <option value="">Seleccionar estado...</option>
            <option value="activo" <?= $renglon['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $renglon['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <small class="form-text text-muted">Campo requerido</small>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>
