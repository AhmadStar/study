$(document).ready(function () {

    // Initialize Select2 for the supplier filter
    $('#supplierFilter').select2({
        allowClear: true, // Allow clearing the selection
        width: '100%', // Set a specific width
        placeholder: "Select Supplier",
    });

    // Initialize Select2 for the category filter
    $('#categoryFilter').select2({
        allowClear: true,
        placeholder: "Select Category",
    });

    // Initialize Select2 for the category filter
    $('#storeFilter').select2({
        allowClear: true,
        placeholder: "Select Store",
    });

    // Initialize Select2 for the user filter
    $('#userFilter').select2({
        allowClear: true,
        placeholder: "Select User",
    });

    var table = $('#example').DataTable({
        "ajax": {
            "url": '/admin/family-filter/list',
            "type": 'GET',
            "data": function (d) {
                d.suppliers = $('#supplierFilter').val();
                d.categories = $('#categoryFilter').val();
                d.stores = $('#storeFilter').val();
                d.users = $('#userFilter').val();
                d.showPrice = $('#showPrice').prop('checked');
                d.showQuantity = $('#showQuantity').prop('checked');
                d.showHistory = $('#showHistory').prop('checked');
                d.keyword = $('#keyword').val();
                d.showActive = $('#showActive').prop('checked') ? 1 : 0;
                d.showOnline = $('#showOnline').prop('checked') ? 1 : 0;
                d.emptyName = $('#emptyName').prop('checked') ? 1 : 0;
                d.wrongPrice = $('#wrongPrice').prop('checked') ? 1 : 0;
                d.wrongBarcode = $('#wrongBarcode').prop('checked') ? 1 : 0;
                d.discontinued = $('#discontinued').prop('checked') ? 1 : 0;
                d.verified = $('#verified').prop('checked') ? 1 : 0;
                d.showImages = $('#showImages').val();
                d.showQuantities = $('#showQuantities').val();
                d.importedLocale = $('#importedLocale').val();
                d.showLastUpdated = $('#showLastUpdated').val();
                d.lastUpdatedType = $('#lastUpdatedType').val();
                // Get start and end date values
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();

                if (typeof d.start !== 'undefined' && typeof d.length !== 'undefined') {
                    d.current_page = d.start / d.length + 1;
                } else {
                    d.current_page = 0;
                }
                if (d.suppliers.length > 0 || d.categories.length > 0 || d.stores.length > 0) {
                    d.first_loop = false;
                } else {
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
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] // Specify which columns to include in the export
                }
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

            // // Calculate the total sum for all rows
            // var totalSum = table.column('.sum-column').data().reduce(function (acc, curr) {

            //     // Convert quantities and selling price to numeric types
            //     var quantities = parseInt(curr.quantities);
            //     var sellingPrice = parseFloat(curr.selling_price);

            //     // Check if quantities and selling price are valid numeric values
            //     if (!isNaN(quantities) && !isNaN(sellingPrice)) {
            //         // Calculate the sum for this row
            //         var sum = quantities * sellingPrice;
            //         return acc + sum;
            //     } else {
            //         return acc; // Exclude invalid values from the sum
            //     }
            // }, 0);

            // console.log("Total Sum:", totalSum); // Log the total sum

            // // Set total sum above the "Sum" column
            // $('#total-sum').text(totalSum);

            // Call the calculateTotalSum function initially and whenever the table content changes
            calculateTotalSum();

        },

        "drawCallback": function (settings, json) {
            // Update the info message on each draw
            $('#example_info').html('Showing ' + (settings._iDisplayStart + 1) + ' to ' + (settings._iDisplayStart + settings._iDisplayLength) + ' of ' + settings.fnRecordsTotal() + ' entries');

            calculateTotalSum();
        },
    });

    // Get the headers row of the table
    var headersRow = $('#example thead tr:last');

    // Insert each checkbox before its respective th element
    $('#columnToggles label').each(function(index) {
        var columnIndex = $(this).find('.column-toggle').data('column-index');
        var thElement = headersRow.find('th:eq(' + columnIndex + ')');
        $(this).insertBefore(thElement);
    });

    // Attach click event handler to each column toggle
    $('#columnToggles').on('change', '.column-toggle', function() {
        var columnIndex = $(this).data('column-index');
        var isChecked = $(this).prop('checked');

        // Toggle visibility of the corresponding column
        table.column(columnIndex).visible(isChecked);
    });

    // Handle the click event for the Edit button
    $('#example tbody').on('click', 'button.edit-btn', function () {
        var data = table.row($(this).parents('tr')).data();
        window.open('/admin/ecommerce/products/edit/' + data.id, '_blank');
    });

    // Handle DataTable processing event
    table.on('processing.dt', function (e, settings, processing) {
        if (processing) {
            $('#loadingOverlay').show();
            disableFilters(true);
        } else {
            $('#loadingOverlay').hide();
            disableFilters(false);
        }
    });

    $('#importedLocale').val('1').trigger('change');

    const $importedLocale = $('#importedLocale').closest('.c-item');
    $importedLocale.addClass('hidden');

    const $discontinued = $('#discontinued').closest('.c-item');
    $discontinued.addClass('hidden');

    const $verified = $('#verified').closest('.c-item');
    $verified.addClass('hidden');

    // Function to disable or enable filter elements
    function disableFilters(disabled) {
        $('#supplierFilter, #categoryFilter, #storeFilter, #userFilter, #showPrice, #showQuantity, #showHistory, #showActive, #showOnline, #showImages, #showQuantities, #showLastUpdated, #lastUpdatedType, #startDate, #endDate, #notes1, #keyword').prop('disabled', disabled);
    }

    document.addEventListener('mousedown', function(event) {
        if (event.target && event.target.matches('a.toggle-vis')) {
            event.preventDefault();
            event.stopImmediatePropagation();

            let columnIdx = event.target.getAttribute('data-column');
            let column = table.column(columnIdx);

            // Toggle the visibility
            column.visible(!column.visible());

            // Check if the column is now hidden
            if (!column.visible()) {
                // Find the corresponding item related to the hidden column
                let relatedItem = document.querySelector('.hide-links a[data-column="' + columnIdx + '"]');
                if (relatedItem) {
                    // Show the related item
                    relatedItem.style.display = 'inline';
                }
            }
        }
    });

    // Listen to DataTable column visibility event to update related item visibility
    table.on('column-visibility.dt', function(e, settings, columnIdx, visibility) {
        let relatedItem = document.querySelector('.hide-links a[data-column="' + columnIdx + '"]');
        if (relatedItem) {
            // Update related item visibility based on column visibility
            relatedItem.style.display = visibility ? 'none' : 'inline';
        }
    });

    // Calculate total sum
    function calculateTotalSum() {
        var totalSum = 0;
        var invalidProducts = []; // Array to store barcodes of products with invalid sums

        // Iterate through all rows in the underlying dataset
        table.rows().every(function() {
            var data = this.data();

            // Check if quantities and selling_price are valid numbers
            if (!isNaN(data.quantities) && !isNaN(data.selling_price) && data.selling_price !== null) {
                var productSum = parseFloat(data.quantities) * parseFloat(data.selling_price);
                totalSum += productSum;
            } else {
                // invalidProducts.push(data.barcode); // Store the barcode of the product with an invalid sum
            }
        });

        // Update the total sum
        if (invalidProducts.length === 0) {
            $('#total-sum').text(totalSum.toFixed(2)); // Round to 2 decimal places
        } else {
            $('#total-sum').text("N/A"); // Display "N/A" if total sum is not available
            console.log("Invalid sums found for products with barcodes:", invalidProducts);
        }
    }

    // Add a click event handler for the Reset Filters button
    $('#resetFiltersButton').on('click', function () {

        // Show the spinner and hide the reset text
        $('#resetFiltersButton .reset-text').hide();
        $('#resetFiltersButton .spinner').removeClass('d-none');

        // Reset Select2 filters
        $('#supplierFilter, #categoryFilter, #storeFilter, #userFilter').val(null).trigger('change');

        // Reset checkboxes
        $('#showPrice, #showQuantity, #showHistory, #showActive, #showOnline, #emptyName, #wrongPrice, #wrongBarcode, #discontinued, #verified').prop('checked', false);

        // Reset text inputs
        $('#notes1, #keyword').val('');

        // Reset select dropdown
        $('#showImages').val('1').trigger('change');

        $('#showQuantities').val('all').trigger('change');

        $('#importedLocale').val('1').trigger('change');

        $('#showLastUpdated').val('all').trigger('change');
        $('#lastUpdatedType').val('json').trigger('change');


        // Reset both startDate and endDate to empty
        $('#startDate, #endDate').val('');

        // Set the default value for endDate to the current date
        document.getElementById('endDate').valueAsDate = new Date();

        // Trigger the DataTable to reload with the new filter values
        table.ajax.reload(function () {
            // Hide the spinner and show the export text after the reload is complete
            setTimeout(function () {
                // Hide the spinner and show the reset text after the reset is complete
                $('#resetFiltersButton .reset-text').show();
                $('#resetFiltersButton .spinner').addClass('d-none');
            }, 500);
        });
    });

    // Set the desired width for the init_qty column
    var initQtyColumnIndex = table.column('init_qty:name').index();
    $(table.column(initQtyColumnIndex).nodes()).css('width', '20px');

    // Set the desired width for the init_qty column using CSS
    $('#example .init-qty-cell').css('width', '20px');

    $('#keyword').on('input', function () {
        var typingTimer;
        var doneTypingInterval = 1000; // Adjust the delay in milliseconds (1 second in this example)
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            table.ajax.reload();
        }, doneTypingInterval);
    });

    $('#supplierFilter').on('change', function () {

        table.ajax.reload();
    });

    // Add an event listener for changes in the category filter
    $('#categoryFilter').on('change', function () {
        table.ajax.reload();
    });

    // Add an event listener for changes in the store filter
    $('#storeFilter').on('change', function () {
        var selectedStores = $(this).val();

        const selectedOptions = $(this).val();

        console.log('selectedOptions' + selectedOptions);

        if (selectedOptions.length === 1 && selectedOptions[0] === '38') {
            $importedLocale.removeClass('hidden');
            $discontinued.removeClass('hidden');
            $verified.removeClass('verified');
        } else {
            $importedLocale.addClass('hidden');
            $discontinued.addClass('hidden');
            $verified.addClass('verified');
        }

        // Check if only one store is selected
        if (selectedStores && selectedStores.length === 1) {
            // Show the "store_qty" column
            table.column(3).visible(true);
        } else {
            // Hide the "store_qty" column
            table.column(3).visible(false);
        }

        table.ajax.reload();
    });

    $('#userFilter').on('change', function () {
        table.ajax.reload();
    });

    $('#showOnline').on('change', function () {
        table.ajax.reload();
    });

    $('#showActive').on('change', function () {
        table.ajax.reload();
    });

    $('#emptyName').on('change', function () {
        table.ajax.reload();
    });

    $('#wrongPrice').on('change', function () {
        table.ajax.reload();
    });

    $('#wrongBarcode').on('change', function () {
        table.ajax.reload();
    });

    $('#discontinued').on('change', function () {
        table.ajax.reload();
    });

    $('#verified').on('change', function () {
        table.ajax.reload();
    });

    // show product depends on images
    $('#showImages').on('change', function () {
        var selectedValue = $(this).val();
        var label;

        switch (selectedValue) {
            case '1':
                label = 'With Images';
                break;
            case '0':
                label = 'Without Images';
                break;
            case 'all':
                label = 'All';
                break;
            default:
                label = 'With Images';
                break;
        }

        $('#showImagesLabel').text(label);
        table.ajax.reload(); // Assuming you have a DataTable named 'table'
    });

    // show quantities
    $('#showQuantities').on('change', function () {
        var selectedValue = $(this).val();
        var label;

        switch (selectedValue) {
            case '1':
                label = 'Positive QTY';
                break;
            case '0':
                label = 'Zero QTY';
                break;
            case 'all':
                label = 'All QTY';
                break;
            case '-1':
                label = 'Negative QTY';
                break;
            default:
                label = 'All QTY';
                break;
        }

        table.ajax.reload();
    });


    $('#importedLocale').on('change', function () {
        table.ajax.reload();
    });


    // last updated
    $('#showLastUpdated').on('change', function () {
        var selectedValue = $(this).val();
        var label;

        switch (selectedValue) {
            case 'today':
                label = 'Today';
                break;
            case 'last2days':
                label = 'Last 2 Days';
                break;
            case 'lastWeek':
                label = 'Last Week';
                break;
            case 'thisMonth':
                label = 'This Month';
                break;
            case 'lastMonth':
                label = 'Last Month';
                break;
            case 'allUpdated':
                label = 'All Updated';
                break;
            default:
                label = 'All Data';
                break;
        }

        table.ajax.reload();
    });

    // last updated type
    $('#lastUpdatedType').on('change', function () {
        var selectedValue = $(this).val();
        var label;

        switch (selectedValue) {
            case 'inventory':
                label = 'Inventory';
                break;
            case 'json':
                label = 'Json';
                break;
            default:
                label = 'Inventory';
                break;
        }

        table.ajax.reload();
    });

    // Handle checkbox changes
    $('#showPrice, #showQuantity').on('change', function () {
        updateColumnHeaders();
    });

    // Initial update
    updateColumnHeaders();

    // Function to update column headers based on checkbox states
    function updateColumnHeaders() {
        var showPrice = $('#showPrice').prop('checked');
        var showQuantity = $('#showQuantity').prop('checked');

        // Select the column you want to update
        var column = $('#example thead th.price-quantity');
        // Update the column header text based on checkbox states
        if (showPrice && showQuantity) {
            var column = table.column('.price-quantity');
            var headerCell = column.header();

            headerCell.innerHTML = 'Price | Quantity';
        } else if (showPrice) {
            var column = table.column('.price-quantity');
            var headerCell = column.header();
            headerCell.innerHTML = 'Price';
        } else if (showQuantity) {
            var column = table.column('.price-quantity');
            var headerCell = column.header();
            headerCell.innerHTML = 'Quantity';
        } else {
            // Hide the column if neither checkbox is checked
            column.text('Qty');
            table.column('.price-quantity').visible(false);
            return; // Exit the function to prevent further execution
        }

        // Show the column if it was previously hidden
        table.column('.price-quantity').visible(true);
    }

    // Handle checkbox changes
    $('#showHistory').on('change', function () {
        updateColumnHistory();
    });

    // Initial update
    updateColumnHistory();

    // Function to update column headers based on checkbox states
    function updateColumnHistory() {
        var showHistory = $('#showHistory').prop('checked');

        // Select the DataTable instance
        var table = $('#example').DataTable();

        // Get the column index for the "History" column
        var columnIdx = table.column('.history').index();

        // Update the column visibility based on checkbox states
        table.column(columnIdx).visible(showHistory);
    }

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


    // Handle row click qtyandprice to expand
    $('#example').on('click', '.init-qty-cell', function () {
        var row = table.row(this);
        var data = row.data();
        var buttonContainer = $(this).find('.button-container');
        var btnTextContainer = buttonContainer.find('.btn-text');
        var loaderContainer = buttonContainer.find('.loader');

        // Check if the row is already expanded
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        } else {
            // Show the loader and hide the button text
            loaderContainer.show();
            btnTextContainer.hide();

            // Disable the button
            buttonContainer.find('.show-qty-btn').prop('disabled', true);

            // Open this row
            formatChildRow(data, buttonContainer).then(function (content) {
                row.child(content).show();

                // Hide the loader and show the button text
                loaderContainer.hide();
                btnTextContainer.show();

                // Enable the button
                buttonContainer.find('.show-qty-btn').prop('disabled', false);
            });
        }
    });

    function formatChildRow(data, buttonContainer) {
        var deferred = $.Deferred();

        // Make an AJAX request to fetch detailed information
        $.ajax({
            url: '/admin/get-product-details/' + data.id,
            type: 'GET',
            success: function (response) {
                // Process the detailed information and format the content
                // This is just an example; adjust based on your actual data structure
                var content = '<table>';
                content += '<thead><tr>';
                content += '<th class="table-header-cell">Store Name</th>';
                content += '<th class="table-header-cell">Quantity</th>';
                content += '<th class="table-header-cell">Price</th>';
                content += '</tr></thead>';
                content += '<tbody>';

                $.each(response.product, function (_, product) {
                    content += '<tr>';
                    content += '<td class="table-header-cell">' + product.name + '</td>';
                    content += '<td class="table-header-cell">' + product.init_qty + '</td>';
                    content += '<td class="table-header-cell">' + product.selling_price + '</td>';
                    content += '</tr>';
                });

                content += '</tbody>';
                content += '</table>';

                deferred.resolve(content);
            },
            error: function (error) {
                console.error('Error fetching detailed information:', error);
                var errorContent = 'Error fetching detailed information.';
                deferred.reject(errorContent);
            }
        });

        return deferred.promise();
    }

    // Handle row click History to expand
    $('#example').on('click', '.history-cell', function () {
        var row = table.row(this);
        var data = row.data();
        var buttonContainer = $(this).find('.button-container');
        var btnTextContainer = buttonContainer.find('.btn-text');
        var loaderContainer = buttonContainer.find('.loader');

        // Check if the row is already expanded
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        } else {
            // Show the loader and hide the button text
            loaderContainer.show();
            btnTextContainer.hide();

            // Disable the button
            buttonContainer.find('.show-history-btn').prop('disabled', true);

            // Open this row
            formatChildRowHistory(data, buttonContainer).then(function (content) {
                row.child(content).show();

                // Hide the loader and show the button text
                loaderContainer.hide();
                btnTextContainer.show();

                // Enable the button
                buttonContainer.find('.show-history-btn').prop('disabled', false);
            });
        }
    });

    function formatChildRowHistory(data, buttonContainer) {
        var deferred = $.Deferred();

        // Make an AJAX request to fetch detailed information
        $.ajax({
            url: '/admin/get-product-history/' + data.id,
            type: 'GET',
            success: function (response) {
                if (response.product && response.product.length > 0) {
                    var content = '<table>';
                    content += '<thead><tr>';
                    content += '<th class="table-header-cell">Store Number</th>';
                    content += '<th class="table-header-cell">Init QTY</th>';
                    content += '<th class="table-header-cell">Store QTY</th>';
                    content += '<th class="table-header-cell">Warehouse QTY</th>';
                    content += '<th class="table-header-cell">User</th>';
                    content += '<th class="table-header-cell">Last Time Confirm</th>';
                    content += '<th class="table-header-cell">Last Time Variation</th>';
                    content += '<th class="table-header-cell">Last Variation</th>';
                    content += '</tr></thead>';
                    content += '<tbody>';

                    $.each(response.product, function (_, product) {
                        content += '<tr>';
                        content += '<td class="table-header-cell">' + product.store_number + '</td>';
                        content += '<td class="table-header-cell">' + product.init_qty + '</td>';
                        content += '<td class="table-header-cell">' + product.qty + '</td>';
                        content += '<td class="table-header-cell">' + product.warehouse_qty + '</td>';
                        content += '<td class="table-header-cell">' + product.user_id + '</td>';
                        content += '<td class="table-header-cell">' + product.last_time_confirm + '</td>';
                        content += '<td class="table-header-cell">' + product.last_time_variation + '</td>';
                        content += '<td class="table-header-cell">' + product.last_variation + '</td>';
                        content += '</tr>';
                    });

                    content += '</tbody>';
                    content += '</table>';

                    deferred.resolve(content);
                } else {
                    // Display a message when there is no history for the product
                    deferred.resolve('<p style="text-align:center;margin-top: 1rem;">No history available for this product.</p>');
                }
            },
            error: function (error) {
                console.error('Error fetching detailed information:', error);
                var errorContent = 'Error fetching detailed information.';
                deferred.reject(errorContent);
            }
        });

        return deferred.promise();
    }

    $('#exportButtonBackend').on('click', function () {
        var button = $(this);
        var buttonText = button.find('.export-text');
        var loader = button.find('.loader');

        // Show loader and hide text
        buttonText.addClass('d-none');
        loader.removeClass('d-none');

        // Get the selected values from your filters
        var suppliers = $('#supplierFilter').val();
        var categories = $('#categoryFilter').val();
        var stores = $('#storeFilter').val();
        var users = $('#userFilter').val();

        // Convert selected option values to arrays of IDs and names
        var selectedSupplierIds = suppliers;
        var selectedSupplierNames = suppliers.map(function (value) {
            return $('#supplierFilter option[value="' + value + '"]').text();
        });

        var selectedCategoryIds = categories;
        var selectedCategoryNames = categories.map(function (value) {
            return $('#categoryFilter option[value="' + value + '"]').text();
        });

        var selectedStoreIds = stores;
        var selectedStoreNames = stores.map(function (value) {
            return $('#storeFilter option[value="' + value + '"]').text();
        });

        var selectedUserIds = users;
        var selectedUserNames = users.map(function (value) {
            return $('#userFilter option[value="' + value + '"]').text();
        });

        var notes1 = $('#notes1').val();
        var notes2 = $('#notes2').val();
        var keyword = $('#keyword').val();

        var showPrice = $('#showPrice').prop('checked');
        var showQuantity = $('#showQuantity').prop('checked');
        var showHistory = $('#showHistory').prop('checked');
        var showActive = $('#showActive').prop('checked') ? 1 : 0;
        var showOnline = $('#showOnline').prop('checked') ? 1 : 0;
        var emptyName = $('#emptyName').prop('checked') ? 1 : 0;
        var wrongPrice = $('#wrongPrice').prop('checked') ? 1 : 0;
        var wrongBarcode = $('#wrongBarcode').prop('checked') ? 1 : 0;
        var discontinued = $('#discontinued').prop('checked') ? 1 : 0;
        var verified = $('#verified').prop('checked') ? 1 : 0;
        var showImages = $('#showImages').val();
        var showQuantities = $('#showQuantities').val();
        var importedLocale = $('#importedLocale').val();
        var showLastUpdated = $('#showLastUpdated').val();
        var lastUpdatedType = $('#lastUpdatedType').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        // Make an AJAX request to fetch detailed information
        $.ajax({
            url: '/admin/export-data-filter/',
            type: 'GET',
            data: {
                suppliers: selectedSupplierIds,
                categories: selectedCategoryIds,
                stores: selectedStoreIds,
                users: selectedUserIds,
                suppliersName: selectedSupplierNames,
                categoriesName: selectedCategoryNames,
                storesName: selectedStoreNames,
                usersName: selectedUserNames,
                notes1: notes1,
                notes2: notes2,
                showPrice: showPrice,
                showQuantity: showQuantity,
                showHistory: showHistory,
                keyword: keyword,
                showActive: showActive,
                showOnline: showOnline,
                emptyName: emptyName,
                wrongPrice: wrongPrice,
                wrongBarcode: wrongBarcode,
                discontinued: discontinued,
                verified: verified,
                showImages: showImages,
                showQuantities: showQuantities,
                importedLocale: importedLocale,
                showLastUpdated: showLastUpdated,
                lastUpdatedType: lastUpdatedType,
                startDate: startDate,
                endDate: endDate,
                length: $('#example').DataTable().page.len(),  // Get the page length from DataTable
                current_page: $('#example').DataTable().page.info().page + 1,  // Get the current page from DataTable
                order: getOrderInformation(),
                columns: getColumnInformation()
            },
            xhrFields: {
                responseType: 'blob' // Set the response type to blob
            },
            success: function (response, status, xhr) {
                // Create a blob from the response data
                var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

                // Create a link element and trigger a click to download the file
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);

                // Use the dynamic file name extracted from the content-disposition header
                var contentDisposition = xhr.getResponseHeader('content-disposition');
                var match = contentDisposition.match(/filename\*?=['"]?(?:UTF-\d['"]*)?([^;\r\n"']*)['"]?/i);

                if (match && match[1]) {
                    var fileName = decodeURIComponent(match[1]);
                } else {
                    // Fallback to the original approach if no match is found
                    var fileName = xhr.getResponseHeader('content-disposition').split('filename=')[1];
                }

                // Rest of your code remains the same
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Hide loader and show text
                buttonText.removeClass('d-none');
                loader.addClass('d-none');
            },
            error: function (error) {
                // Handle error

                // Hide loader and show text
                buttonText.removeClass('d-none');
                loader.addClass('d-none');

                console.error('Error fetching detailed information:', error);
            }
        });
    });

    function getOrderInformation() {
        var order = [];
        var columns = $('#example').DataTable().columns().indexes();

        columns.each(function (index) {
            var orderObj = {};
            var columnOrder = $('#example').DataTable().order()[index];

            if (columnOrder && columnOrder.length > 1) {
                orderObj['column'] = index;
                orderObj['dir'] = columnOrder[1];
                order.push(orderObj);
            }
        });

        return order;
    }

    function getColumnInformation() {
        var columns = $('#example').DataTable().columns().indexes();
        var columnData = [];

        columns.each(function (index) {
            var column = $('#example').DataTable().column(index);
            columnData.push({
                data: column.dataSrc(),
                name: column.header().innerHTML,
                visible: column.visible(),
                // searchable: column.searchable  // Use the searchable property directly
            });
        });

        return columnData;
    }
});
