document.addEventListener('DOMContentLoaded', function () {

    if (typeof $ === 'undefined' || !$.fn.DataTable) {
        console.warn('DataTables no está cargado');
        return;
    }

    $('.datatable-export').each(function () {

        if ($.fn.DataTable.isDataTable(this)) {
            return;
        }

        const title = $(this).data('page-title') || 'Reporte';

        $(this).DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm ms-2',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: title
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-secondary btn-sm ms-2'
                }
            ]
        });
    });

    setTimeout(() => {
        document.querySelectorAll('.alert-success').forEach(el => el.remove());
    }, 4000);
});
