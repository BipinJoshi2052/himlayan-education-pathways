/*
 * "×" on an existing uploaded image (see admin/partials/existing-image).
 * Clicking it hides the image and sets its hidden flag to 1, so the save
 * removes the image. Delegated, so rows added later by the repeater work too.
 */
(function () {
    document.addEventListener('click', function (event) {
        var button = event.target.closest('.existing-image-remove');

        if (!button) {
            return;
        }

        var wrapper = button.closest('.existing-image');

        if (!wrapper) {
            return;
        }

        var flag = wrapper.querySelector('.existing-image-flag');

        if (flag) {
            flag.value = '1';
        }

        wrapper.style.display = 'none';
    });
})();
