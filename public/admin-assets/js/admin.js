$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    $('.theme-toggle-btn').on('click', function () {
        $.post('/api/admin/profile/theme-toggle').done(function (response) {
            var mode = response.data.theme_mode;
            document.documentElement.setAttribute('data-bs-theme', mode);
            $('.theme-toggle-label').text(mode.charAt(0).toUpperCase() + mode.slice(1) + ' mode');
        });
    });
});
