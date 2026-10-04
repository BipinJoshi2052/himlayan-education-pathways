document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.querySelector('.sidebar');
    if (!sidebar) {
        return;
    }

    var mobile = window.matchMedia('(max-width: 1199.98px)');

    // The theme collapses the sidebar to icons on small screens, which hides
    // its labels and leaves it stuck off-screen. On phones keep it full size.
    function keepFullSize() {
        if (mobile.matches && sidebar.classList.contains('sidebar-mini')) {
            sidebar.classList.remove('sidebar-mini', 'on-resize');
        }
    }
    new MutationObserver(keepFullSize).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    keepFullSize();

    var backdrop = document.createElement('div');
    backdrop.className = 'admin-sidebar-backdrop';
    document.body.appendChild(backdrop);

    function setOpen(open) {
        document.body.classList.toggle('admin-sidebar-open', open);
    }

    backdrop.addEventListener('click', function () { setOpen(false); });

    // Capture phase + stopImmediatePropagation so the theme's own collapse
    // handler doesn't also run on phones.
    document.querySelectorAll('.sidebar-toggle, .admin-drawer-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            if (!mobile.matches) {
                return;
            }
            event.stopImmediatePropagation();
            setOpen(!document.body.classList.contains('admin-sidebar-open'));
        }, true);
    });

    document.querySelectorAll('.sidebar a').forEach(function (link) {
        link.addEventListener('click', function () { if (mobile.matches) { setOpen(false); } });
    });
});
