document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // --- Dropzone upload --------------------------------------------------
    var dropzoneEl = document.getElementById('gallery-dropzone');
    if (dropzoneEl && typeof Dropzone !== 'undefined') {
        Dropzone.autoDiscover = false;

        var grid = document.getElementById('photo-grid');

        new Dropzone('#gallery-dropzone', {
            url: dropzoneEl.dataset.uploadUrl,
            paramName: 'file',
            maxFilesize: 8,
            acceptedFiles: 'image/*',
            headers: {'X-CSRF-TOKEN': csrfToken},
            dictDefaultMessage: 'Drop photos here or click to upload',
            success: function (file, response) {
                if (!response || !response.success || !grid) {
                    return;
                }

                var card = buildPhotoCard(response.data.id, response.data.url);
                grid.appendChild(card);
                bindPhotoCard(card);
            },
        });
    }

    function buildPhotoCard(id, url) {
        var col = document.createElement('div');
        col.className = 'col-md-3 photo-grid-item';
        col.dataset.id = id;
        col.innerHTML =
            '<div class="card">' +
            '<img src="' + url + '" class="card-img-top" style="height:140px;object-fit:cover;cursor:grab;" alt="">' +
            '<div class="card-body p-2">' +
            '<input type="text" class="form-control form-control-sm mb-1 photo-caption-input" data-media-id="' + id + '" placeholder="Caption">' +
            '<input type="text" class="form-control form-control-sm mb-2 photo-alt-input" data-media-id="' + id + '" placeholder="Alt text">' +
            '<button type="button" class="btn btn-sm btn-soft-danger w-100 photo-delete-btn" data-media-id="' + id + '">Delete</button>' +
            '</div></div>';

        return col;
    }

    // --- Inline caption / alt text, saved on blur ---------------------------
    function saveCaption(mediaId, caption, altText) {
        fetch('/admin/galleries/media/' + mediaId + '/caption', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({caption: caption, alt_text: altText}),
        });
    }

    function bindPhotoCard(card) {
        var captionInput = card.querySelector('.photo-caption-input');
        var altInput = card.querySelector('.photo-alt-input');
        var deleteBtn = card.querySelector('.photo-delete-btn');

        [captionInput, altInput].forEach(function (input) {
            input.addEventListener('blur', function () {
                saveCaption(input.dataset.mediaId, captionInput.value, altInput.value);
            });
        });

        deleteBtn.addEventListener('click', function () {
            var mediaId = deleteBtn.dataset.mediaId;

            var afterConfirm = function () {
                fetch('/admin/galleries/media/' + mediaId, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrfToken},
                }).then(function (response) {
                    if (response.ok) {
                        card.remove();
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete this photo?',
                    text: "This can't be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    confirmButtonColor: '#e3342f',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        afterConfirm();
                    }
                });
            } else if (confirm('Delete this photo?')) {
                afterConfirm();
            }
        });
    }

    document.querySelectorAll('.photo-grid-item').forEach(bindPhotoCard);

    // --- Drag-reorder via SortableJS ---------------------------------------
    var grid = document.getElementById('photo-grid');
    if (grid && typeof Sortable !== 'undefined') {
        new Sortable(grid, {
            animation: 150,
            onEnd: function () {
                var items = Array.from(grid.querySelectorAll('.photo-grid-item')).map(function (el, index) {
                    return {id: el.dataset.id, order: index + 1};
                });

                fetch(grid.dataset.reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({items: items}),
                });
            },
        });
    }
});
