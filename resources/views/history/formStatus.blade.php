@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                {{-- Optional header content --}}
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header">
                {{-- Optional content header --}}
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Insert Daily Report</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @include('partials.alert')
                                        <div class="col-md-12">
                                            <!-- Create this view for store -->
                                            <form action="{{ route('historical') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="id_machine" value="{{ $no_machine }}">
                                                <input type="hidden" name="date" value="{{ $date }}">
                                                <input type="hidden" name="shift" value="{{ $shift }}">
                                                <input type="hidden" name="checksheet_head_id" value="{{ $checksheet_head_id }}">
                                                <input type="hidden" name="report" value="Follow Up Problem">

                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <!-- Card for the first set of fields -->
                                                        <div class="card border border-3 border-mkm rounded">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="shop"><strong style="color: rgba(0, 103, 127, 1)">Shop</strong> </label>
                                                                            <select class="form-control" id="shop" name="shop" required>
                                                                                <option value="Electric">Electric</option>
                                                                                <option value="Mechanic">Mechanic</option>
                                                                                <option value="Power House">Power House</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="problem"><strong style="color: rgba(0, 103, 127, 1)">Problem</strong> </label>
                                                                            <textarea class="form-control" id="problem" name="problem" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="cause"><strong style="color: rgba(0, 103, 127, 1)">Cause</strong></label>
                                                                            <textarea class="form-control" id="cause" name="cause" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="action"><strong style="color: rgba(0, 103, 127, 1)">Action</strong></label>
                                                                            <textarea class="form-control" id="action" name="action" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <!-- Card for the second set of fields -->
                                                        <div class="card border border-3 border-mkm rounded">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="category"><strong style="color: rgba(0, 103, 127, 1)">Category</strong></label>
                                                                            <select name="category" id="category" class="form-control" required>
                                                                                <option value="">- Please Select Role -</option>
                                                                                @foreach ($dropdown as $Problem)
                                                                                    <option value="{{ $Problem->name_value }}">{{ $Problem->name_value }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="start_time"><strong style="color: rgba(0, 103, 127, 1)">Start Time</strong></label>
                                                                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="finish_time"><strong style="color: rgba(0, 103, 127, 1)">Finish Time</strong></label>
                                                                            <input type="time" class="form-control" id="finish_time" name="finish_time" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="balance"><strong style="color: rgba(0, 103, 127, 1)">Balance</strong></label>
                                                                            <input readonly type="number" class="form-control" id="balance" name="balance" required>
                                                                        </div>
                                                                    </div>

                                                                    <script>
                                                                        $(document).ready(function() {
                                                                            function calculateBalance() {
                                                                                var startTime = $('#start_time').val();
                                                                                var finishTime = $('#finish_time').val();

                                                                                if (startTime && finishTime) {
                                                                                    // Convert time strings to Date objects
                                                                                    var start = new Date('1970-01-01T' + startTime + 'Z');
                                                                                    var finish = new Date('1970-01-01T' + finishTime + 'Z');

                                                                                    // Calculate the difference in milliseconds
                                                                                    var difference = finish - start;

                                                                                    if (difference < 0) {
                                                                                        // Handle cases where finish time is before start time
                                                                                        $('#balance').val(0);
                                                                                    } else {
                                                                                        // Convert milliseconds to hours
                                                                                        var hours = difference / (1000 * 60 * 60);
                                                                                        $('#balance').val(hours.toFixed(2)); // Display the balance as a number with two decimal places
                                                                                    }
                                                                                } else {
                                                                                    $('#balance').val('');
                                                                                }
                                                                            }

                                                                            // Attach the calculation function to both time inputs
                                                                            $('#start_time, #finish_time').on('change', calculateBalance);
                                                                        });
                                                                    </script>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <!-- Card for the third set of fields -->
                                                        <div class="card border border-3 border-mkm rounded">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="pic"><strong style="color: rgba(0, 103, 127, 1)">PIC</strong></label>
                                                                            <input type="text" class="form-control" id="pic" name="pic" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="status"><strong style="color: rgba(0, 103, 127, 1)">Status</strong></label>
                                                                            <select class="form-control" id="status" name="status" required>
                                                                                <option value="">Select Status</option>
                                                                                <option value="Close">Close</option>
                                                                                <option value="Open">Open</option>
                                                                                <option value="Delay">Delay</option>
                                                                                <option value="Ongoing">Ongoing</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="img"><strong style="color: rgba(0, 103, 127, 1)">Image</strong></label>
                                                                            <input type="file" class="form-control" id="img" name="img">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="remarks"><strong style="color: rgba(0, 103, 127, 1)">Remarks</strong></label>
                                                                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                              <!-- Checkbox Selection for Parts -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Select Part Type(s)</label>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input part-type" type="checkbox" name="part_type[]" id="sap_part" value="sap">
                                                                    <label class="form-check-label" for="sap_part">SAP Part</label>
                                                                </div>
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input part-type" type="checkbox" name="part_type[]" id="repair_part" value="repair">
                                                                    <label class="form-check-label" for="repair_part">Repair Part</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input part-type" type="checkbox" name="part_type[]" id="other_part" value="other">
                                                                    <label class="form-check-label" for="other_part">Other</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SAP Parts Table -->
                                                <div id="sap-parts-section" style="display:none;">
                                                    <h4>SAP Parts</h4>
                                                    <table class="table table-bordered table-striped" id="sap-parts-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Stock Type</th>
                                                                <th>Spare Part</th>
                                                                <th>SAP Quantity</th>
                                                                <th>Quantity</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="sap-parts-container">
                                                            <tr class="part-row">
                                                                <!-- Stock Type Selection -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control stock_type" name="stock_type[]" disabled>
                                                                            <option value="sap" selected>New (SAP)</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <!-- Spare Part Selection -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control spare_part" name="spare_part_sap[]" >
                                                                            <!-- Parts will be loaded dynamically -->
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <!-- SAP Quantity -->
                                                                <td class="sap_quantity_container">
                                                                    <div class="form-group">
                                                                        <input readonly type="number" class="form-control sap_quantity" name="sap_quantity[]">
                                                                    </div>
                                                                </td>
                                                                <!-- Used Quantity -->
                                                                <td class="qty">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control used_qty" name="used_qty_sap[]">
                                                                    </div>
                                                                </td>
                                                                <!-- Add Part Button -->
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary btn-add-part-sap">+</button>
                                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-part">-</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Repair Parts Table -->
                                                <div id="repair-parts-section" style="display:none;">
                                                    <h4>Repair Parts</h4>
                                                    <table class="table table-bordered table-striped" id="repair-parts-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Stock Type</th>
                                                                <th>Spare Part</th>
                                                                <th>Repair Location</th>
                                                                <th>Quantity</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="repair-parts-container">
                                                            <tr class="part-row">
                                                                <!-- Stock Type Selection -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control stock_type" name="stock_type[]" disabled>
                                                                            <option value="repair" selected>Repair (Extend)</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <!-- Spare Part Selection -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control spare_part" name="spare_part_repair[]" >
                                                                            <!-- Parts will be loaded dynamically -->
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <!-- Repair Location Selection -->
                                                                <td class="repair_location_container">
                                                                    <div class="form-group">
                                                                        <select class="form-control repair_location" name="repair_location[]">
                                                                            <!-- Locations will be loaded dynamically -->
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <!-- Used Quantity -->
                                                                <td class="qty">
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control used_qty" name="used_qty_repair[]">
                                                                    </div>
                                                                </td>
                                                                <!-- Add Part Button -->
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary btn-add-part-repair">+</button>
                                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-part">-</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Other Parts Table -->
                                                <div id="other-parts-section" style="display:none;">
                                                    <h4>Other Parts</h4>
                                                    <table class="table table-bordered table-striped" id="other-parts-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Material Name</th>
                                                                <th>Description</th>
                                                                <th>Bun</th>
                                                                <th>Location</th>
                                                                <th>Cost</th>
                                                                <th>Quantity</th>

                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="other-parts-container">
                                                            <tr class="part-row">
                                                                <!-- Material Name -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control other_part_name" name="other_part_name[]" placeholder="Enter Material Name">
                                                                    </div>
                                                                </td>
                                                                 <!-- Material Description -->
                                                                 <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control other_part_name" name="other_part_name_description[]" placeholder="Enter Material Description">
                                                                    </div>
                                                                </td>
                                                                 <!-- Material Bun -->
                                                                 <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control other_part_name" name="bun[]" placeholder="Enter Material Description">
                                                                    </div>
                                                                </td>
                                                                <!-- Location -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control other_part_location" name="other_part_location[]" placeholder="Enter Location">
                                                                    </div>
                                                                </td>
                                                                <!-- Cost -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control other_part_quantity" name="Cost[]" placeholder="Enter Quantity">
                                                                    </div>
                                                                </td>
                                                                <!-- Quantity -->
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number" class="form-control other_part_quantity" name="other_part_quantity[]" placeholder="Enter Quantity">
                                                                    </div>
                                                                </td>
                                                                <!-- Add/Remove Part Button -->
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary btn-add-part-other">+</button>
                                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-part">-</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>


                                                <hr>
                                                <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        // Handle Part Type Selection using Checkboxes
        $('.part-type').on('change', function() {
            var showSAP = $('#sap_part').is(':checked');
            var showRepair = $('#repair_part').is(':checked');
            var showOther = $('#other_part').is(':checked'); // New "Other" option handling

            if (showSAP) {
                $('#sap-parts-section').show();
            } else {
                $('#sap-parts-section').hide();
            }

            if (showRepair) {
                $('#repair-parts-section').show();
            } else {
                $('#repair-parts-section').hide();
            }

            if (showOther) {
                $('#other-parts-section').show(); // Show "Other" parts section
            } else {
                $('#other-parts-section').hide(); // Hide "Other" parts section
            }
        });

        // Initialize Select2 for the first row in SAP and Repair tables
        initializeSelect2($('#sap-parts-table .part-row:first').find('.spare_part'));
        initializeSelect2($('#repair-parts-table .part-row:first').find('.spare_part'));

        /* ---------- SAP Parts Table ---------- */

        // Add Part Row for SAP Table
        $('#sap-parts-table').on('click', '.btn-add-part-sap', function() {
            addPartRowSap();
        });

        // Function to handle adding a new part row for SAP table
        function addPartRowSap() {
            var newRow = $('#sap-parts-table .part-row:first').clone();

            // Clear all input fields
            newRow.find('input').val('');
            newRow.find('select').val(''); // Clear select fields

            // Set the stock type to "sap" and ensure it is disabled
            newRow.find('.stock_type').val('sap').prop('disabled', true); // Set and disable stock type

            // Remove existing Select2 container and initialize Select2 again
            newRow.find('.select2-container').remove(); // Remove existing Select2 container
            newRow.find('.spare_part').prop('disabled', false).val(null); // Ensure the dropdown is enabled and reset

            // Append the new row to the SAP table container
            $('#sap-parts-container').append(newRow);

            // Initialize Select2 for the new row
            initializeSelect2(newRow.find('.spare_part'));
        }

        // Fetch SAP Quantity dynamically for SAP table
        $('#sap-parts-table').on('change', '.spare_part', function() {
            var row = $(this).closest('.part-row');
            var partId = $(this).val();

            if (partId) {
                // Fetch SAP Quantity via AJAX
                $.ajax({
                    url: '/get-sap-quantity', // Ensure this route and controller method exist
                    method: 'GET',
                    data: { part_id: partId },
                    success: function(response) {
                        if (response.sap_quantity) {
                            row.find('.sap_quantity').val(response.sap_quantity); // Update SAP quantity input
                        } else {
                            row.find('.sap_quantity').val(''); // Clear if no quantity
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching SAP quantity:', xhr.responseText);
                    }
                });
            } else {
                row.find('.sap_quantity').val(''); // Clear SAP quantity if no part is selected
            }
        });

        /* ---------- Repair Parts Table ---------- */

        // Add Part Row for Repair Table
        $('#repair-parts-table').on('click', '.btn-add-part-repair', function() {
            addPartRowRepair();
        });

        // Function to handle adding a new part row for Repair table
        function addPartRowRepair() {
            var newRow = $('#repair-parts-table .part-row:first').clone(); // Clone the first row as a template

            // Clear all input fields
            newRow.find('input, select').val('');

            // Set the default stock type to "repair" and disable it to ensure it can't be changed
            newRow.find('.stock_type').val('repair').prop('disabled', true);

            newRow.find('.select2-container').remove(); // Remove existing Select2 container
            newRow.find('.spare_part').prop('disabled', false).val(null); // Ensure the dropdown is enabled and reset

            // Reset the repair location dropdown
            newRow.find('.repair_location').empty().append('<option value="">Select Location</option>');

            $('#repair-parts-container').append(newRow); // Append the new row to the container
            initializeSelect2(newRow.find('.spare_part')); // Initialize Select2 for the new row
        }

        // Fetch Repair Locations dynamically for Repair Parts table
        $('#repair-parts-table').on('change', '.spare_part', function() {
            var row = $(this).closest('.part-row');
            var repairPartId = $(this).val();

            if (repairPartId) {
                // Fetch Repair Locations via AJAX using the repair_part_id in the URL
                $.ajax({
                    url: '/get-repair-locations-for-part/' + repairPartId,  // Include repair_part_id in the URL
                    method: 'GET',
                    success: function(response) {
                        console.log('AJAX Response:', response); // Log the response to inspect it

                        if (Array.isArray(response) && response.length > 0) {
                            var repairLocationSelect = row.find('.repair_location');
                            repairLocationSelect.empty().append('<option value="">Select Location</option>');
                            response.forEach(function(location) {
                                repairLocationSelect.append('<option value="' + location.id + '" data-repaired-qty="' + location.repaired_qty + '">' + location.sloc + ' - ' + location.repaired_qty + ' Qty</option>');
                            });
                        } else {
                            console.error('No locations found for the selected part.');
                            row.find('.repair_location').empty().append('<option value="">No locations available</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching repair locations:', xhr.responseText);
                    }
                });
            } else {
                row.find('.repair_location').html(''); // Clear repair location dropdown if no part is selected
            }
        });

        /* ---------- Other Parts Table ---------- */

        // Add Part Row for Other Parts Table
        $('#other-parts-table').on('click', '.btn-add-part-other', function() {
            addPartRowOther();
        });

        // Function to handle adding a new part row for Other Parts table
        function addPartRowOther() {
            var newRow = $('#other-parts-table .part-row:first').clone();

            // Clear all input fields
            newRow.find('input').val('');

            // Append the new row to the Other Parts table container
            $('#other-parts-container').append(newRow);
        }

        // Remove Part Row for any table
        $(document).on('click', '.btn-remove-part', function() {
            $(this).closest('.part-row').remove();
        });

        // Initialize Select2 for dropdowns
        function initializeSelect2(selectElement) {
            selectElement.select2({
                width: '100%',
                placeholder: 'Select Spare Part',
                ajax: {
                    url: "{{ route('fetch.parts') }}", // Route to the controller method
                    dataType: 'json',
                    delay: 250, // Delay to prevent rapid requests
                    data: function(params) {
                        return {
                            term: params.term, // Search term from user input
                            stock_type: $(this).closest('.part-row').find('.stock_type').val() // Pass selected stock type to server
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        }
    });
</script>



@endsection
