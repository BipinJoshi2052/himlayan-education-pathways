document.addEventListener('DOMContentLoaded', function () {
    if (typeof Sortable === 'undefined') {
        return;
    }

    document.querySelectorAll('tbody[data-sortable-url]').forEach(function (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () {
                var items = Array.from(tbody.querySelectorAll('tr[data-id]')).map(function (row, index) {
                    return {id: row.dataset.id, order: index + 1};
                });

                fetch(tbody.dataset.sortableUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({items: items}),
                });
            },
        });
    });
});
