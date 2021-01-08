export function setDataTable(elementID, pageLength = 25, filterColumns = []) {
    return $(`#${elementID}`).DataTable({
        'responsive': true,
        "pageLength": 25,
        "lengthMenu": [10, 20, 25, 50, 75, 100],
        "order": [[0, 'desc']],
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'csv',
                text: 'Export CSV',
                exportOptions: {
                    columns: [$(":not(.donotexport)")]
                },
            },
            {
                extend: 'excel',
                text: 'Export Excel',
                exportOptions: {
                    columns: [$(":not(.donotexport)")]
                },
            }
        ],
        initComplete: function () {
            this.api().columns(filterColumns).every(function () {
                var column = this;
                var select = $('<select><option value="">_All</option><option value="NULL">_Empty</option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        if (val === 'NULL') {
                            column
                                .search('^$', true, false)
                                .draw();
                        } else {
                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        }
                    });

                column.data().unique().sort().each(function (d, j) {
                    if (!d) {
                        return;
                    }
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        }
    });
}

export function setDataTableResponsive(dataTable, parentDivRef, parentDivObject) {
    parentDivObject.parentDivDimensions = {
        width: parentDivRef.current.offsetWidth,
        height: parentDivRef.current.offsetHeight
    };

    const callback = () => {
        if (typeof parentDivRef.current === 'undefined' || !parentDivRef.current) {
            return document.removeEventListener('mousemove', callback);
        }

        if (parentDivObject.parentDivDimensions.width !== parentDivRef.current.offsetWidth || parentDivObject.parentDivDimensions.height !== parentDivRef.current.offsetHeight) {
            dataTable.responsive.recalc();

            parentDivObject.parentDivDimensions = {
                width: parentDivRef.current.offsetWidth,
                height: parentDivRef.current.offsetHeight
            };
        }
    };

    // ok, i can't handle it
    // dataTables does not react on resizing of parent div, it only reacts when window (i mean browser window) changes
    // so i have to force redrawing somehow
    // probably this is the ugliest way to do it, but damn it works
    document.addEventListener('mousemove', callback);
}