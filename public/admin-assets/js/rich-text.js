/**
 * Quill's default color format is class-based (e.g. class="ql-color-red"),
 * which only looks right where Quill's own CSS is loaded — admin only, not
 * the public site that actually renders this content. Registering the
 * *style* attributor instead makes it an inline style
 * (style="color:#...") baked into the saved HTML, so it renders correctly
 * anywhere, admin or public, with no extra CSS needed on the public side.
 * Runs once, before any editor is created.
 */
if (typeof Quill !== 'undefined') {
    Quill.register(Quill.import('attributors/style/color'), true);
}

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
                        ['customColor'],
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
                        customColor: function () {
                            richTextCustomColor(this.quill, this.container.querySelector('.ql-customColor'));
                        },
                    },
                },
            },
        });

        quill.root.innerHTML = hiddenInput.value || '';

        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML;
        });

        // Created now, not on first click: a brand-new element has no on-screen
        // position yet in the same tick it's inserted, so opening the native
        // color picker immediately after creating it anchored to a screen
        // corner instead of the button. Creating it up front gives the
        // browser time to lay it out before anyone ever clicks Color.
        var colorButton = editorEl.parentElement.querySelector('.ql-toolbar .ql-customColor');
        if (colorButton) {
            richTextEnsureColorInput(colorButton);
        }
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

/**
 * The hidden <input type="color"> used by the Color toolbar button — a
 * *sibling* of the button, not a child (nesting an <input> inside a <button>
 * is invalid HTML, and browsers silently refuse to open the native picker
 * from one). Created once per button and reused after that.
 */
function richTextEnsureColorInput(buttonEl) {
    var input = buttonEl.nextElementSibling;

    if (!input || !input.classList.contains('rich-text-color-input')) {
        input = document.createElement('input');
        input.type = 'color';
        input.className = 'rich-text-color-input';
        input.style.width = '1px';
        input.style.height = '1px';
        input.style.opacity = '0';
        input.style.border = '0';
        input.style.padding = '0';
        buttonEl.insertAdjacentElement('afterend', input);
    }

    return input;
}

/**
 * Opens the browser's own color picker (a full palette) and applies the
 * chosen color to the current selection — or, with nothing selected, to
 * whatever is typed next. Browsers anchor the native picker popup near the
 * input's own on-screen position, so the input is created up front (see
 * initRichTextField) rather than here on first use: a brand-new element
 * has no on-screen position yet in the same tick it's inserted, so opening
 * the picker immediately after creating it landed in a screen corner on
 * the very first click.
 */
function richTextCustomColor(quill, buttonEl) {
    var range = quill.getSelection(true);
    var input = richTextEnsureColorInput(buttonEl);

    input.value = '#000000';
    input.onchange = function () {
        if (range && range.length > 0) {
            quill.formatText(range.index, range.length, 'color', input.value, 'user');
        } else {
            quill.setSelection(range);
            quill.format('color', input.value, 'user');
        }
    };
    input.click();
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof Quill === 'undefined') {
        return;
    }

    document.querySelectorAll('.rich-text-field').forEach(initRichTextField);
});

window.initRichTextField = initRichTextField;
