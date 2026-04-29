(function () {
    const appShell = document.getElementById('appShell');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (!appShell) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 992px)');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (desktopQuery.matches) {
                appShell.classList.toggle('sidebar-collapsed');
                return;
            }

            appShell.classList.toggle('sidebar-open');
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function () {
            appShell.classList.remove('sidebar-open');
        });
    }

    window.addEventListener('resize', function () {
        if (desktopQuery.matches) {
            appShell.classList.remove('sidebar-open');
        }
    });
})();

/* ============================================================
 * GeroModal — Sistema de Modales Global
 * ============================================================
 * Tipos disponibles: 'confirm' | 'danger' | 'warning' | 'info' | 'success'
 *
 * Uso programático:
 *   GeroModal.show({
 *     title: 'Titulo',
 *     message: 'Mensaje descriptivo.',
 *     type: 'danger',                  // opcional, default: 'confirm'
 *     confirmLabel: 'Eliminar',        // opcional, default: 'Confirmar'
 *     cancelLabel: 'Cancelar',         // opcional, default: 'Cancelar'
 *     showCancel: true,                // opcional, default: true
 *     onConfirm: function() { ... },   // callback al confirmar
 *   });
 *
 * Uso declarativo (HTML):
 *   Agrega data-gero-confirm al <button type="submit"> de un <form>:
 *   <button type="submit"
 *           data-gero-confirm
 *           data-title="Eliminar registro"
 *           data-message="¿Deseas eliminar este registro?"
 *           data-type="danger"
 *           data-confirm-label="Eliminar">
 *     Eliminar
 *   </button>
 * ============================================================ */
window.GeroModal = (function () {
    const TYPE_CONFIG = {
        confirm: { headerClass: 'bg-primary text-white',  btnClass: 'btn-primary',  btnCloseClass: 'btn-close-white', icon: 'bi-question-circle-fill' },
        danger:  { headerClass: 'bg-danger text-white',   btnClass: 'btn-danger',   btnCloseClass: 'btn-close-white', icon: 'bi-exclamation-triangle-fill' },
        warning: { headerClass: 'bg-warning text-dark',   btnClass: 'btn-warning',  btnCloseClass: '',                icon: 'bi-exclamation-circle-fill' },
        info:    { headerClass: 'bg-info text-white',     btnClass: 'btn-info',     btnCloseClass: 'btn-close-white', icon: 'bi-info-circle-fill' },
        success: { headerClass: 'bg-success text-white',  btnClass: 'btn-success',  btnCloseClass: 'btn-close-white', icon: 'bi-check-circle-fill' },
    };

    function show(opts) {
        const options = Object.assign({
            title: 'Confirmar',
            message: '',
            type: 'confirm',
            confirmLabel: 'Confirmar',
            cancelLabel: 'Cancelar',
            showCancel: true,
            showConfirm: true,
            onConfirm: null,
        }, opts);

        const cfg = TYPE_CONFIG[options.type] || TYPE_CONFIG.confirm;

        const modalEl    = document.getElementById('geroModal');
        const headerEl   = document.getElementById('geroModalHeader');
        const titleEl    = document.getElementById('geroModalTitle');
        const bodyEl     = document.getElementById('geroModalBody');
        const footerEl   = document.getElementById('geroModalFooter');
        const confirmBtn = document.getElementById('geroModalConfirm');
        const closeBtn   = headerEl ? headerEl.querySelector('.btn-close') : null;

        if (!modalEl || !headerEl || !titleEl || !bodyEl || !confirmBtn) {
            return;
        }

        // Reset header classes
        headerEl.className = 'modal-header ' + cfg.headerClass;
        if (closeBtn) {
            closeBtn.className = 'btn-close ' + cfg.btnCloseClass;
        }

        // Title with icon
        titleEl.innerHTML = '<i class="bi ' + cfg.icon + ' me-2"></i>' + _esc(options.title);

        // Body
        if (options.html) {
            bodyEl.innerHTML = options.html;
        } else {
            bodyEl.textContent = options.message;
        }

        // Confirm button
        confirmBtn.className = 'btn ' + cfg.btnClass;
        confirmBtn.textContent = options.confirmLabel;
        confirmBtn.style.display = options.showConfirm === false ? 'none' : '';

        // Cancel button visibility
        const cancelBtn = footerEl ? footerEl.querySelector('[data-bs-dismiss="modal"]') : null;
        if (cancelBtn) {
            cancelBtn.textContent = options.cancelLabel;
            cancelBtn.style.display = options.showCancel ? '' : 'none';
        }

        // Wire confirm callback (clone to remove old listeners)
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);

        newConfirmBtn.addEventListener('click', function () {
            bsModal.hide();
            if (typeof options.onConfirm === 'function') {
                // Wait for modal hide animation before executing
                modalEl.addEventListener('hidden.bs.modal', function handler() {
                    modalEl.removeEventListener('hidden.bs.modal', handler);
                    options.onConfirm();
                });
            }
        });

        bsModal.show();
    }

    function _esc(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }

    // Auto-wire: intercept forms with [data-gero-confirm] on their submit button
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-gero-confirm]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const form = btn.form || btn.closest('form');

        show({
            title:        btn.dataset.title        || 'Confirmar accion',
            message:      btn.dataset.message      || '¿Deseas continuar con esta accion?',
            type:         btn.dataset.type         || 'confirm',
            confirmLabel: btn.dataset.confirmLabel || 'Confirmar',
            cancelLabel:  btn.dataset.cancelLabel  || 'Cancelar',
            onConfirm: function () {
                if (form) {
                    form.submit();
                }
            },
        });
    });

    return { show };
}());
