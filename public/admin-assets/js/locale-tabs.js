document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.locale-switch-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var locale = btn.dataset.locale;

            document.querySelectorAll('.locale-switch-btn').forEach(function (b) {
                b.classList.toggle('active', b === btn);
            });

            document.querySelectorAll('[data-locale-pane]').forEach(function (pane) {
                pane.style.display = pane.dataset.localePane === locale ? '' : 'none';
            });
        });
    });
});
