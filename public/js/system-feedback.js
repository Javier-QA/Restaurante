(function () {
    'use strict';
    function boot() {
        const probe = document.createElement('span');
        probe.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none;color:var(--primary)';
        document.body.appendChild(probe);
        function updatePaletteContrast() {
            const rgb = getComputedStyle(probe).color.match(/[\d.]+/g);
            if (!rgb || rgb.length < 3) return;
            const channels = rgb.slice(0, 3).map(Number).map(v => {
                v /= 255;
                return v <= .04045 ? v / 12.92 : Math.pow((v + .055) / 1.055, 2.4);
            });
            const luminance = channels[0] * .2126 + channels[1] * .7152 + channels[2] * .0722;
            const lightText = luminance < .179;
            document.body.style.setProperty('--modal-header-ink', lightText ? '#ffffff' : '#071320');
            document.body.style.setProperty('--modal-close-filter', lightText ? 'invert(1) grayscale(1) brightness(2)' : 'none');
        }
        updatePaletteContrast();
        // Watch the palette and light/dark mode without reacting to our own style changes.
        const observer = new MutationObserver(updatePaletteContrast);
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-color-mode', 'class'] });

        const approved = new WeakSet();
        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            const method = form.querySelector('input[name="_method"]');
            if (!method || method.value.toUpperCase() !== 'DELETE' || form.hasAttribute('onsubmit')) return;
            if (approved.has(form)) { approved.delete(form); return; }
            if (!window.SystemNotify) return;
            event.preventDefault();
            const submitter = event.submitter;
            SystemNotify.confirm({
                type: 'danger', title: 'Confirmar eliminación',
                text: form.dataset.confirmMessage || '¿Estás seguro de eliminar este registro?',
                confirmText: 'Eliminar', icon: 'bi-trash3',
                onConfirm: function () {
                    approved.add(form);
                    form.requestSubmit(submitter || undefined);
                }
            });
        }, true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
