<?php
/**
 * Created by PhpStorm.
 * User: Ahamd Alnajm
 * Date: 28/11/2023
 * Time: 11:01 AM
 */
?>
@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')


    <div id="loadingOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgb(255 255 255 / 19%); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            {{-- <i class="fas fa-spinner fa-spin"></i> Loading... --}}
        </div>
    </div>

    <table id="example" class="display nowrap" width="100%">
        <thead>
            <tr>

            <th>id</th>
            <th>head_name</th>
            <th>actions</th>
            </tr>

        </thead>
        <tbody>
        </tbody>
    </table>
@stop


<style>

    #example td:nth-child(3) {
        max-width: 100px;
        word-wrap: break-word;
        white-space: normal;
    }

    .init-qty-cell,
    .history {
        /* cursor: pointer; */
    }

    div.container {
        min-width: 980px;
        margin: 0 auto;
    }

    /* Style for checkbox buttons */
    input[type="checkbox"] {
        display: none;
    }

    /* Custom styling for the checkbox buttons */
    input[type="checkbox"]+label {
        display: inline-block;
        margin-right: 5px;
        /* Adjust the spacing between buttons */
        padding: 8px 16px;
        font-size: 14px;
        text-align: center;
        /* cursor: pointer; */
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #f4f4f4;
        color: #333;
    }

    /* Style when the checkbox is checked */
    input[type="checkbox"]:checked+label {
        background-color: #198754;
        color: #fff;
        border: 1px solid #198754;
    }

    input[type="text"],
    select {
        height: 34px;
        box-sizing: border-box;
        background-color: #f4f4f4;
        border: 1px solid #ccc;

    }

    .dataTables_processing {
        background-color: #ffcc00;
        /* Set your desired background color */
        color: #333;
        /* Set your desired text color */
        font-size: 16px;
        /* Set your desired font size */
        padding: 10px;
        /* Adjust padding as needed */
        border-radius: 4px;
        /* Add border-radius for rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Add a subtle box shadow */
    }

    div.dataTables_processing>div:last-child>div {
        position: absolute;
        top: 0;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #198754 !important;
        background: rgb(var(--dt-row-selected));
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    div.dataTables_processing {
        top: 5% !important;
    }

    .table-header-cell {
        width: 200px !important;
        max-width: 200px !important;
        word-wrap: break-word;
        /* Allow long words to be broken and wrap onto the next line */
    }

    /* Adjust the styles for the loader inside the button */
    .button-container .loader {
        vertical-align: middle;
        margin-left: 5px;
    }

    /* You can also customize the loader appearance */
    .button-container .loader::after {
        display: inline-block;
        font-size: 13px;
    }

    .button-container .loader1 {
        vertical-align: middle;
        margin-left: 5px;
    }

    /* You can also customize the loader appearance */
    .button-container .loader1::after {
        content: 'Loading...';
        display: inline-block;
        font-size: 13px;
    }

    .c-item {
        flex: 1 1 0%;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .button-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn,
    .c-item-text {
        margin-right: 10px;
        /* Adjust margin as needed */
    }

    input[type="text"] {
        width: auto;
        /* Auto width, not taking up remaining space */
    }

    .modal-body input[type="text"] {
        width: 100% !important;
    }

    .checkbox-form-group {
        display: inline-block;
        margin-right: 10px;
        /* Add some margin between form groups */
    }

    .modal-dialog {
        max-width: 800px !important;
        /* Set the maximum width of the modal */
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .star-rating input[type="radio"] {
        display: none;
    }

    .star-rating label {
        font-size: 1.6em;
        color: grey;
        cursor: pointer;
    }

    .star-rating input[type="radio"]:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label,
    .star-rating input[type="radio"]:checked~label {
        color: #FFD700;
    }

    .select-checkbox input[type=checkbox] {
        display: block !important;
    }

    .status-verified {
        background-color: green;
        color: white;
        padding: 2px 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    .status-active {
        background-color: blue;
        color: white;
        padding: 2px 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    .status-discontinued {
        background-color: red;
        color: white;
        padding: 2px 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    .status-imported_local {
        background-color: orange;
        color: white;
        padding: 2px 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    .datepadding{
        padding: .3em 1.3em .3em .3em;
        background: #f4f4f4;
        border: 1px solid #ccc;
    }

    .danger {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 3px;
    }

    .btn-gold {
    background-color: gold; /* Background color for the button */
    color: black; /* Text color */
    border-color: gold; /* Border color (optional) */
    /* Additional styles as needed */
    }

    .btn-gold .fas.fa-star {
        color: gold; /* Color for the star icon */
    }

    @media (max-width: 768px) {
        .mobile-checkbox {
            flex-wrap: wrap;
        }

        .c-item {
            flex: 1 1 0%;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .c-item-text {
            flex: 1 1 calc(50% - 10px);
            margin-right: 5;
        }

        .select-image {}

        .btn {
            font-size: 12px !important;
            padding: 5px 5px !important;
        }

        select {
            font-size: 12px !important;
        }

        .table-header-cell{
            font-size: 12px;
        }

        #example td, #example th, #example th a, #example a, a{
            font-size: 12px;
        }

        /* Custom styling for the checkbox buttons */
        input[type="checkbox"]+label {
            display: inline-block;
            margin-right: 5px;
            /* Adjust the spacing between buttons */
            padding: 5px 5px;
            font-size: 12px;
            text-align: center;
            /* cursor: pointer; */
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f4f4f4;
            color: #333;
        }

        .datepadding{
            font-size: 12px;
        }

    }
</style>


<script>
    document.querySelectorAll('.date-filter').forEach(function(dateFilterDiv) {
        dateFilterDiv.addEventListener('click', function(event) {
            console.log('test clcik');
            const inputElement = dateFilterDiv.querySelector('input[type="date"]');
            inputElement.click(); // Trigger the click event on the input element
        });
    });
</script>

