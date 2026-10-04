document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') {
        return;
    }

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: form.dataset.confirm,
                text: form.dataset.confirmText || "This action can't be undone.",
                icon: form.dataset.confirmIcon || 'warning',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'Yes, delete it',
                confirmButtonColor: '#e3342f',
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Native submit() does not re-dispatch the 'submit' event,
                    // so this doesn't loop back into this same listener.
                    form.submit();
                }
            });
        });
    });
});
