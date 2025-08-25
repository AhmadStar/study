$(document).ready(function () {

    // Initialize Select2 for the user filter
    // $('#userFilter').select2({
    //     allowClear: true,
    //     placeholder: "Select User",
    // });

    // $('#storeFilter').select2({
    //     allowClear: true,
    //     placeholder: "Select Store",
    // });

    var table = $('#ch-order').DataTable({
        "ajax": {
            "url": '/ramo/ch-order/list',
            "type": 'GET',
            "data": function (d) {
                // d.users = $('#userFilter').val();
                // if($('select[name="store_id"]').val()!='Select')
                //     d.store_id = $('select[name="store_id"]').val();
                // if($('select[name="status"]').val()!='Select')
                //     d.status = $('select[name="status"]').val();
                // d.keyword = $('#keyword').val();

                // Get start and end date values
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();

                if (typeof d.start !== 'undefined' && typeof d.length !== 'undefined') {
                    d.current_page = d.start / d.length + 1;
                } else {
                    d.current_page = 0;
                }
                d.filters = [];
                return d;
            },
            "dataType": 'json',
            "dataSrc": function (response) {
                return response.data;
            }
        },

        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": 'date', render: function(data) {
                var date = new Date(data);
                return date.toLocaleDateString();
            }},
            { "data": "item_count" },
            {
                "data": "status",
                "render": function (data, type, row) {
                    let className = data.toLowerCase() === 'pending' ? 'badge badge-warning' : 'badge badge-success';
                    return '<span class="' + className + '">' + data + '</span>';
                }
            },

            {
                "data": "operations",
                "orderable": false,
                "render": function (data, type, row, meta) {
                    return '<div class="button-container">' +
                    '<a href="/ramo/ch-order-details/' + row.id + '" class="btn btn-outline-info view-order-btn" data-id="' + row.id + '">View Order</a>' +
                    // '<button class="btn btn-primary edit-order-btn" data-id="' + row.id + '">Edit Order</button>' +
                    // '<button class="btn btn-outline-secondary edit-status-btn" data-id="' + row.id + '" data-status="' + row.status + '">Edit Status</button>' +
                    // '<button class="btn btn-danger delete-order-btn" data-id="' + row.id + '">Delete Order</button>' +
                    '</div>';
                }
            },
        ],
        columnDefs: [
            { "width": "150px", "targets": 1 },
            { "targets": 1, "className": "wrap-text" }
        ],
        scrollCollapse: true,
        scrollX: true,
        "deferRender": true, // Add this line to defer rendering of rows not in the viewport
        "paging": true, // Enable pagination
        "pageLength": 100, // Set the number of records per page
        "serverSide": true, // Enable server-side processing
        "processing": true, // Show processing indicator
        "searching": true, // Enable searching
        "info": true, // Display information about the table
        "lengthMenu": [5, 10, 25, 50, 100, 200, 500, 1000, 10000], // Define the page length menu
        "dom": 'lrtip', // Customize the DataTables controls
        "language": {
            "processing": '<div class="custom-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
        },
        "order": [[0, 'desc']], // Initial sorting order
        "initComplete": function (settings, json) {
            // Set the initial info message
            $('#example_info').html('Showing 1 to ' + settings._iDisplayLength + ' of ' + json.total + ' entries');
        },

        "drawCallback": function (settings, json) {
            // Update the info message on each draw
            $('#example_info').html('Showing ' + (settings._iDisplayStart + 1) + ' to ' + (settings._iDisplayStart + settings._iDisplayLength) + ' of ' + settings.fnRecordsTotal() + ' entries');
        },
    });

    // Handle DataTable processing event
    table.on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $('#loadingOverlay').show();
            // disableFilters(true);
        } else {
            $('#loadingOverlay').hide();
            // disableFilters(false);
        }
    });



    // Event listener for start and end date change
    $('#startDate, #endDate').on('change', function () {
        // Check if both start and end dates are selected
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        if (startDate !== '' && endDate !== '') {
            // Ensure end date is greater than start date
            if (new Date(startDate) > new Date(endDate)) {
                alert('End date should be greater than start date');
                // Optionally reset the date inputs or handle the error in another way
                $('#startDate, #endDate').val('');
            } else {
                bothDatesSelected = true;
                table.ajax.reload();
            }
        } else {
            bothDatesSelected = false;
        }
    });




    function getColumnInformation() {
        var columns = $('#example').DataTable().columns().indexes();
        var columnData = [];

        columns.each(function (index) {
            var column = $('#example').DataTable().column(index);
            columnData.push({
                data: column.dataSrc(),
                name: column.header().innerHTML,
                visible: column.visible(),
            });
        });

        return columnData;
    }





});
