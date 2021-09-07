/*
 *  Document   : be_tables_datatables.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in DataTables Page
 */

// DataTables, for more examples you can check out https://www.datatables.net/
class pageTablesDatatables {
    /*
     * Init DataTables functionality
     *
     */
    static initDataTables() {
        // Override a few default classes
        jQuery.extend(jQuery.fn.dataTable.ext.classes, {
            sWrapper: "dataTables_wrapper dt-bootstrap4",
            sFilterInput:  "form-control form-control-sm",
            sLengthSelect: "form-control form-control-sm"
        });

        // Override a few defaults
        jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Cari..",
                emptyTable: "Tidak ada Data tersedia",
                info: "Menampilkan <strong>_START_</strong> sampai <strong>_END_</strong> dari <strong>_TOTAL_</strong> data",
                infoEmpty: "",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>'
                }
            }
        });

        // Init full DataTable
        jQuery('.js-dataTable-full').dataTable({
            pageLength: 10,
            lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            autoWidth: false,
            info: true,
            order: [],
            "columnDefs": [{ targets: 'disable-sorting', orderable: false }]
        });

        // Init full extra DataTable
        jQuery('.js-dataTable-full-pagination').dataTable({
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
            autoWidth: false
        });

        // Init simple DataTable
        jQuery('.js-dataTable-simple').dataTable({
            pageLength: 10,
            lengthMenu: false,
            searching: false,
            autoWidth: false,
            order: [ [ $('th.defaultSort').length > 0 ? $('th.defaultSort').index() : 0,  'desc' ] ],
            dom: "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>"
        });
        
        // Init simple DataTable
        jQuery('.mytask-card-dataTable').dataTable({
            pageLength: 12,
            lengthMenu: false,
            searching: true,
            autoWidth: false,
            "bLengthChange" : false,
            order: [],
            // dom: "<'row'<'col-sm-12'tr>>" +
                // "<'row'<'col-sm-6'i><'col-sm-6'p>>"
        });

        // Init DataTable with Buttons
        jQuery('.js-dataTable-buttons').dataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
            autoWidth: false,
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-alt-primary' },
                { extend: 'csv', className: 'btn btn-sm btn-alt-primary' },
                { extend: 'print', className: 'btn btn-sm btn-alt-primary' }
            ],
            dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    }

    /*
     * Init functionality
     *
     */
    static init() {
        this.initDataTables();
    }
}

// Initialize when page loads
jQuery(() => { pageTablesDatatables.init(); });
