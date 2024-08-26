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
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
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
                                <!-- /.card-header -->
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
                                                <input type="hidden" name="report" value="Daily Report">

                                                <div class="row mb-4">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="shop">Shop</label>
                                                            <select class="form-control" id="shop" name="shop" required>
                                                                <option value="Electric">Electric</option>
                                                                <option value="Mechanic">Mechanic</option>
                                                                <option value="Power House">Power House</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="problem">Problem</label>
                                                            <textarea class="form-control" id="problem" name="problem" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="cause">Cause</label>
                                                            <textarea class="form-control" id="cause" name="cause" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="action">Action</label>
                                                            <textarea class="form-control" id="action" name="action" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="parts-container">
                                                    <div class="row mb-4 part-row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="spare_part">Spare Part</label>
                                                                <select class="form-control spare_part chosen-select" name="spare_part[]" >
                                                                    <option value="">Select Spare Part</option>
                                                                    @foreach($spareParts as $part)
                                                                        <option value="{{ $part->id }}" data-sap="{{ $part->begining_qty }}" data-repair="{{ $part->total_stock }}">{{ $part->material }} - {{$part->material_description}}</option>
                                                                    @endforeach
                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="stock_type">Stock Type</label>
                                                                <select class="form-control stock_type" name="stock_type[]">
                                                                    <option value="sap">New (SAP)</option>
                                                                    <option value="repair">Repair (Extend)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 sap_quantity_container">
                                                            <div class="form-group">
                                                                <label for="sap_quantity">SAP Quantity</label>
                                                                <input readonly type="number" class="form-control sap_quantity" name="sap_quantity[]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 repair_location_container" style="display:none;">
                                                            <div class="form-group">
                                                                <label for="repair_location">Repair Location</label>
                                                                <select class="form-control repair_location" name="repair_location[]">
                                                                    <option value="">Select Location</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 qty">
                                                            <div class="form-group">
                                                                <label for="used_qty">Quantity</label>
                                                                <input type="number" class="form-control used_qty" name="used_qty[]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <button type="button" class="btn btn-primary btn-add-part">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row mb-4">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="category">Category</label>
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
                                                            <label for="start_time">Start Time</label>
                                                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="finish_time">Finish Time</label>
                                                            <input type="time" class="form-control" id="finish_time" name="finish_time" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="balance">Balance</label>
                                                            <input readonly type="number" class="form-control" id="balance" name="balance" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row mb-4">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="pic">PIC</label>
                                                            <input type="text" class="form-control" id="pic" name="pic" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="status">Status</label>
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
                                                            <label for="img">Image</label>
                                                            <input type="file" class="form-control" id="img" name="img">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="remarks">Remarks</label>
                                                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                                                        </div>
                                                    </div>
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
        <!-- /.content-wrapper -->
    </div>
</main>

<script>
    $(document).ready(function() {
        // Initialize Chosen for dynamically searchable dropdown
        $('.chosen-select').chosen({
            width: '100%',
            no_results_text: 'No results matched', // Customize the text displayed when no results are found
            allow_single_deselect: true
        });

        function handleStockTypeChange(row) {
            var stockType = row.find('.stock_type').val();
            if (stockType === 'sap') {
                row.find('.sap_quantity_container').show();
                row.find('.repair_location_container').hide();
                row.find('.repair_quantity_container').hide();
            } else if (stockType === 'repair') {
                row.find('.sap_quantity_container').hide();
                row.find('.repair_location_container').show();
                row.find('.repair_quantity_container').show();
                row.find('.spare_part').trigger('change'); // Trigger change to load repair locations
            }
        }

        function addPartRow() {
            var newRow = $('.part-row:first').clone();
            newRow.find('input, select').val('');
            newRow.find('.chosen-select').chosen('destroy').chosen(); // Reinitialize Chosen for new row
            newRow.find('.sap_quantity_container').show();
            newRow.find('.repair_location_container').hide();
            newRow.find('.repair_quantity_container').hide();
            $('#parts-container').append(newRow);
        }

        $('#parts-container').on('change', '.spare_part', function() {
            var row = $(this).closest('.part-row');
            var selectedOption = $(this).find('option:selected');
            var sapStock = selectedOption.data('sap');
            var repairStock = selectedOption.data('repair');

            row.find('.sap_quantity').val(sapStock);

            // Fetch repair locations if stock type is repair
            if (row.find('.stock_type').val() === 'repair') {
                var partId = $(this).val();

                $.ajax({
                    url: '/get-repair-locations-for-part/' + partId,
                    method: 'GET',
                    success: function(data) {
                        if (Array.isArray(data)) {
                            var repairLocationSelect = row.find('.repair_location');
                            repairLocationSelect.empty().append('<option value="">Select Location</option>');
                            data.forEach(function(location) {
                                repairLocationSelect.append('<option value="' + location.id + '" data-repaired-qty="' + location.repaired_qty + '">' + location.sloc + ' - ' + location.repaired_qty + ' Qty</option>');
                            });
                        } else {
                            console.error('Unexpected data format:', data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching repair locations:', xhr.responseText);
                    }
                });
            }
        });

        $('#parts-container').on('change', '.stock_type', function() {
            var row = $(this).closest('.part-row');
            handleStockTypeChange(row);
        });

        $('#parts-container').on('change', '.repair_location', function() {
            var row = $(this).closest('.part-row');
            var selectedOption = $(this).find('option:selected');
            var repairQty = selectedOption.data('repaired-qty');
            row.find('.repair_quantity').val(repairQty);
        });

        $('#parts-container').on('click', '.btn-add-part', function() {
            addPartRow();
        });

        // Initialize the first part row
        handleStockTypeChange($('.part-row:first'));

        // Trigger the change event on page load to set initial state
        $('#parts-container').find('.stock_type').trigger('change');

        // Calculate balance based on start and end time
        $('#start_time, #finish_time').change(function() {
            var startTime = $('#start_time').val();
            var finishTime = $('#finish_time').val();
            if (startTime && finishTime) {
                var start = new Date("1970-01-01T" + startTime + "Z");
                var end = new Date("1970-01-01T" + finishTime + "Z");
                var diff = (end - start) / (1000 * 60 * 60); // Difference in hours
                if (diff < 0) {
                    diff += 24; // Adjust for overnight times
                }
                $('#balance').val(diff);
            }
        });
    });
</script>

@endsection
