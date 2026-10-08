// Toko Sembako A1 - shared UI behaviour: sidebar toggle and confirm dialogs.
(function () {
    'use strict';

    var toggle = document.querySelector('[data-menu-toggle]');
    var sidebar = document.querySelector('[data-sidebar]');
    var overlay = document.querySelector('[data-sidebar-overlay]');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
    }

    if (toggle && sidebar && overlay) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', closeSidebar);
    }

    // Confirmation dialog on any form with data-confirm.
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
})();
