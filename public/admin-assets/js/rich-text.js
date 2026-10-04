function initRichTextField(field) {
    field.querySelectorAll('[data-locale-pane]').forEach(function (pane) {
        var locale = pane.dataset.localePane;
        var editorEl = pane.querySelector('.rich-text-editor');

        // Guard against double-init — e.g. if something calls this twice on
        // the same field.
        if (!editorEl || editorEl.classList.contains('ql-container')) {
            return;
        }

        var hiddenInput = field.querySelector('.rich-text-hidden-input[data-locale="' + locale + '"]');

        var quill = new Quill(editorEl, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{header: [2, 3, false]}],
                        [{list: 'ordered'}, {list: 'bullet'}],
                        ['link', 'blockquote', 'image'],
                        ['table'],
                        ['clean'],
                    ],
                    handlers: {
                        image: function () {
                            richTextImageHandler(this.quill);
                        },
                        table: function () {
                            richTextInsertTable(this.quill);
                        },
                    },
                },
            },
        });

        quill.root.innerHTML = hiddenInput.value || '';

        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
    });
}

/**
 * Quill has no native file-upload handler for its image button — the
 * default behavior embeds a giant base64 data URI straight into the
 * content, which bloats the DB column fast. This uploads to the same
 * /admin/rich-text/images endpoint any other admin upload goes through and
 * inserts a real URL instead.
 */
function richTextImageHandler(quill) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = function () {
        var file = input.files[0];
        if (!file) {
            return;
        }

        var formData = new FormData();
        formData.append('image', file);

        var token = document.querySelector('meta[name="csrf-token"]');

        fetch('/admin/rich-text/images', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': token ? token.content : ''},
            body: formData,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Upload failed');
                }
                return response.json();
            })
            .then(function (data) {
                var range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', data.url, 'user');
                quill.setSelection(range.index + 1);
            })
            .catch(function () {
                window.alert('Image upload failed. Please try again.');
            });
    };
}

/**
 * Quill (core) has no native table editor — this inserts a plain, already-
 * structured HTML table at the cursor, which Quill is happy to keep as-is
 * in its HTML output (and the admin can still type around it). It's a
 * deliberately simple fallback, not a full interactive table module —
 * none of the actively-maintained ones ship a CDN-ready build compatible
 * with Quill 2 (see docs/admin-ui.md).
 */
function richTextInsertTable(quill) {
    var rows = window.prompt('Number of rows?', '2');
    var cols = window.prompt('Number of columns?', '2');
    rows = Math.max(1, Math.min(20, parseInt(rows, 10) || 2));
    cols = Math.max(1, Math.min(10, parseInt(cols, 10) || 2));

    var html = '<table style="width:100%;border-collapse:collapse;" border="1" cellpadding="6">';
    for (var r = 0; r < rows; r++) {
        html += '<tr>';
        for (var c = 0; c < cols; c++) {
            html += '<td>&nbsp;</td>';
        }
        html += '</tr>';
    }
    html += '</table><p><br></p>';

    var range = quill.getSelection(true);
    quill.clipboard.dangerouslyPasteHTML(range.index, html, 'user');
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof Quill === 'undefined') {
        return;
    }

    document.querySelectorAll('.rich-text-field').forEach(initRichTextField);
});

window.initRichTextField = initRichTextField;
