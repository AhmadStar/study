$(document).ready(function () {
    var table = $('#example').DataTable({
        "ajax": {
            "url": '/admin/family-filter/list',
            "type": 'GET',
            "data": function (d) {
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
            { "data": "head_name" },
            {
                "data": null,
                "className": 'dt-center',
                "orderable": false,
                "render": function (data, type, row) {
                    var editButton = '<button class="btn btn-success edit-btn"';

                    if (data !== null && typeof row.id !== 'undefined') {
                        editButton += ' data-id="' + row.id + '"';
                    }

                    editButton += '>Edit</button>';

                    return editButton;
                }
            },
        ],
        "columnDefs": [
            {
                "targets": 1,
                "className": "text-left",
                "width": "100px"
            },
            {
                targets: 1,
                width: 1
            }
        ],
        scrollCollapse: true,
        scrollX: true,
        "deferRender": true, // Add this line to defer rendering of rows not in the viewport
        "buttons": [
            {
                extend: 'excelHtml5',
                text: 'Export to Excel',
                customizeData: function (data) {
                    // Iterate through each row in the exported data
                    for (var i = 0; i < data.body.length; i++) {
                        // Check if the image column is null
                        if (data.body[i][2] === null) {
                            // Set a default image URL
                            data.body[i][2] = '<img src="https://app.magiprix.com/storage/news/1-150x150.jpg" alt="Default Image" style="max-width: 100px;">';
                        } else {
                            // Concatenate the base URL with the image field and create an image tag
                            data.body[i][2] = '<img src="https://app.magiprix.com/storage' + data.body[i][2] + '" alt="Product Image" style="max-width: 100px;">';
                        }
                    }
                },
            }
        ],
        "paging": true, // Enable pagination
        "pageLength": 10, // Set the number of records per page
        "serverSide": true, // Enable server-side processing
        "processing": true, // Show processing indicator
        "searching": true, // Enable searching
        "info": true, // Display information about the table
        "lengthMenu": [5, 10, 25, 50, 100, 200, 500, 1000], // Define the page length menu
        "dom": 'lrtip', // Customize the DataTables controls
        "language": {
            "processing": '<div class="custom-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
        },
        "initComplete": function (settings, json) {
            // Set the initial info message
            $('#example_info').html('Showing 1 to ' + settings._iDisplayLength + ' of ' + json.total + ' entries');
        },

        "drawCallback": function (settings, json) {
            // Update the info message on each draw
            $('#example_info').html('Showing ' + (settings._iDisplayStart + 1) + ' to ' + (settings._iDisplayStart + settings._iDisplayLength) + ' of ' + settings.fnRecordsTotal() + ' entries');

        },
    });
});
