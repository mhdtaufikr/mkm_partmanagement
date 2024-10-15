@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <!-- Scan QR Machine Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Scan QR Machine</h3>
                            </div>
                            <div class="card-body">
                                @include('partials.alert')
                                <form action="{{ url('/checksheet/scan') }}" method="POST">
                                    @csrf
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <label for="typeSelect">Select Type</label>
                                            <select name="type" id="typeSelect" class="form-control" {{ $userType != 'all' ? 'readonly disabled' : '' }}>
                                                @if($userType != 'all')
                                                    <option value="{{ $userType }}" selected>{{ $userType }}</option>
                                                @else
                                                    <option value="">Select Type</option>
                                                    @foreach($types as $type)
                                                        <option value="{{ $type->type }}">{{ $type->type }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="plantSelect">Select Plant</label>
                                            <select name="plant" id="plantSelect" class="form-control" {{ $userPlant != 'all' ? 'readonly disabled' : '' }}>
                                                @if($userPlant != 'all')
                                                    <option value="{{ $userPlant }}" selected>{{ $userPlant }}</option>
                                                @else
                                                    <option value="">Select Plant</option>
                                                    <!-- Plants will be populated by JavaScript if plant is 'all' -->
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="shopSelect">Select Line</label>
                                            <select name="shop" id="shopSelect" class="form-control">
                                                <option value="">Select Line</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="opNoSelect">Select OP. No</label>
                                            <select name="op_no" id="opNoSelect" class="form-control">
                                                <option value="">Select OP. No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <script>
                                        $(document).ready(function() {
                                            var userType = "{{ $userType }}"; // Pre-filled user type from profile
                                            var userPlant = "{{ $userPlant }}"; // Pre-filled user plant from profile

                                            // Pre-fill the type and plant dropdowns if userType and userPlant are not 'all'
                                            if (userType !== 'all') {
                                                $('#typeSelect').val(userType).prop('disabled', true); // Pre-fill and disable the type dropdown
                                            }

                                            if (userPlant !== 'all') {
                                                $('#plantSelect').val(userPlant).prop('disabled', true); // Pre-fill and disable the plant dropdown

                                                // Now, manually trigger the AJAX call to fetch the lines
                                                fetchLines(userType, userPlant);
                                            }

                                            // If userType and userPlant are 'all', allow the user to select them
                                            if (userType === 'all') {
                                                $('#typeSelect').change(function() {
                                                    var type = $(this).val();
                                                    $.ajax({
                                                        url: '{{ route('get.plants') }}',
                                                        method: 'GET',
                                                        data: { type: type },
                                                        success: function(data) {
                                                            $('#plantSelect').empty().append('<option value="">Select Plant</option>');
                                                            $.each(data, function(index, value) {
                                                                $('#plantSelect').append('<option value="'+value.plant+'">'+value.plant+'</option>');
                                                            });
                                                        },
                                                        error: function(xhr, status, error) {
                                                            console.error('Error fetching plants:', error);
                                                        }
                                                    });
                                                });
                                            }

                                            $('#plantSelect').change(function() {
                                                var type = $('#typeSelect').val();
                                                var plant = $(this).val();
                                                fetchLines(type, plant); // Fetch lines based on type and plant
                                            });

                                            $('#shopSelect').change(function() {
                                                var type = $('#typeSelect').val();
                                                var plant = $('#plantSelect').val();
                                                var shop = $(this).val();
                                                fetchOpNos(type, plant, shop); // Fetch OP Nos when the line is selected
                                            });

                                            // Manually trigger the line fetch if the plant and type are pre-filled
                                            function fetchLines(type, plant) {
                                                $.ajax({
                                                    url: '{{ route('get.shops') }}',
                                                    method: 'GET',
                                                    data: { type: type, plant: plant },
                                                    success: function(data) {
                                                        console.log('Line Data:', data); // Debugging the response for lines
                                                        $('#shopSelect').empty().append('<option value="">Select Line</option>');
                                                        $.each(data, function(index, value) {
                                                            $('#shopSelect').append('<option value="'+value.line+'">'+value.line+'</option>');
                                                        });
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error('Error fetching lines:', error);
                                                    }
                                                });
                                            }

                                            // Function to fetch OP Nos
                                            function fetchOpNos(type, plant, shop) {
                                                $.ajax({
                                                    url: '{{ route('get.opNos') }}',
                                                    method: 'GET',
                                                    data: { type: type, plant: plant, shop: shop },
                                                    success: function(data) {
                                                        $('#opNoSelect').empty().append('<option value="">Select OP. No</option>');
                                                        $.each(data, function(index, value) {
                                                            $('#opNoSelect').append('<option value="'+value.op_no+'">'+value.op_no+' - '+value.machine_name+'</option>');
                                                        });
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error('Error fetching OP Nos:', error);
                                                    }
                                                });
                                            }

                                            // Manually trigger the change event to load the line if the plant is pre-filled
                                            if (userPlant !== 'all') {
                                                fetchLines(userType, userPlant);
                                            }
                                        });
                                    </script>


                                    <div class="d-flex justify-content-center">
                                        <div id="qr-reader" style="width:500px"></div>
                                        <div id="qr-reader-results"></div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        <input readonly type="text" name="no_mechine" id="qr-value" class="form-control" placeholder="Scanned QR Code Value">
                                    </div>

                                </div>
                                <div class="card-footer d-flex justify-content-center">
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                </form>
                            </div>
                            <!-- End of Scan QR Machine Card -->

                            <!-- List Checksheet Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">List Preventive Maintenance</h3>
                                </div>
                                <!-- Legend Section -->
                                <div class="top-0 end-0 bg-white p-3 border rounded shadow">
                                    <div class="legend">
                                        <strong>Legend:</strong>
                                        <span style='font-size: 20px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span> Temporary |
                                        <i class="fas fa-times" style='font-size: 20px; color: red;'></i> Not Good |
                                        <i class="fas fa-check" style='font-size: 20px; color: green;'></i> OK
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tableUser" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Plant</th> <!-- Add Plant column here -->
                                                            <th>OP No. (Machine Name)</th>
                                                            <th>Type</th>
                                                            <th>Planning Date</th>
                                                            <th>Actual Date</th>
                                                            <th>PIC</th>
                                                            <th>Approval</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @foreach ($items as $data)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $data->plant ?? 'Unknown' }}</td> <!-- Display the plant here -->
                                                            <td>{{ $data->op_name ?? '' }} ({{ $data->machine_name ?? '' }})</td>
                                                            <td>{{ $data->type ?? 'Unknown' }}</td>
                                                            <td>{{ $data->planning_date ? date('d/m/Y', strtotime($data->planning_date)) : '' }}</td>
                                                            <td>{{ $data->actual_date ? date('d/m/Y', strtotime($data->actual_date)) : '' }}</td>
                                                            <td>{{ $data->pic }}</td>
                                                            <td>
                                                                @if($data->status == 0)
                                                                    <span class="badge bg-primary">Update</span>
                                                                @elseif($data->status == 1)
                                                                    <span class="badge bg-info">Check</span>
                                                                @elseif($data->status == 2)
                                                                    <span class="badge bg-warning">Waiting Approval</span>
                                                                @elseif($data->status == 3)
                                                                    <span class="badge bg-danger">Remand</span>
                                                                @elseif($data->status == 4)
                                                                    <span class="badge bg-success">Done</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Unknown Status</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($data->pm_status == 'OK')
                                                                    <span class="text-success">
                                                                        <i class='fas fa-check' style='font-size: 30px; color: green;'></i>
                                                                    </span>
                                                                @elseif($data->pm_status == 'Not Good')
                                                                    <span class="text-danger">
                                                                        <i class='fas fa-times' style='font-size: 30px; color: red;'></i>
                                                                    </span>
                                                                @elseif($data->pm_status == 'Temporary')
                                                                    <span style='font-size: 30px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span>
                                                                @else
                                                                    <span>Unknown Status</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @include('partials.action-dropdown', ['data' => $data])
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Include Modals -->
                            @foreach ($items as $data)
                                    @include('partials.change-status-modal', ['data' => $data])
                                    @include('partials.journey-modal', ['data' => $data])
                                    @include('partials.status-log-modal', ['data' => $data])
                            @endforeach

                            <!-- End of List Checksheet Card -->
                        </div>
                    </div>
            </section>
        </div>
    </div>
</main>

<!-- DataTables and QR Scanner Scripts -->
<script>
    $(document).ready(function() {
        var table = $("#tableUser").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    function docReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    docReady(function () {
        var resultContainer = document.getElementById('qr-reader-results');
        var inputField = document.getElementById('qr-value');
        var lastResult, countResults = 0;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                console.log(`Decoded text: ${decodedText}`);
                lastResult = decodedText;
                inputField.value = decodedText;
            }
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    });
</script>

@endsection
