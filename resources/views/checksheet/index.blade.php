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
                                    <div class="col-sm-12">
                                        <!-- Alert success -->
                                        @if (session('status'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <strong>{{ session('status') }}</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        @endif

                                        @if (session('failed'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <strong>{{ session('failed') }}</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        @endif

                                        <!-- Form validation errors -->
                                        @if (count($errors) > 0)
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            <ul>
                                                <li><strong>Data Process Failed !</strong></li>
                                                @foreach ($errors->all() as $error)
                                                <li><strong>{{ $error }}</strong></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        <!-- End form validation errors -->
                                    </div>

                                    <form action="{{ url('/checksheet/scan') }}" method="POST">
                                        @csrf
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <label for="typeSelect">Select Type</label>
                                                <select name="type" id="typeSelect" class="form-control">
                                                    <option value="">Select Type</option>
                                                    @foreach($types as $type)
                                                        <option value="{{ $type->type }}">{{ $type->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="plantSelect">Select Plant</label>
                                                <select name="plant" id="plantSelect" class="form-control">
                                                    <option value="">Select Plant</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="shopSelect">Select Shop</label>
                                                <select name="shop" id="shopSelect" class="form-control">
                                                    <option value="">Select Shop</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="opNoSelect">Select OP. No</label>
                                                <select name="op_no" id="opNoSelect" class="form-control">
                                                    <option value="">Select OP. No</option>
                                                </select>
                                            </div>
                                        </div>

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
                            <script>
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
                                        }
                                    });
                                });

                                $('#plantSelect').change(function() {
                                    var type = $('#typeSelect').val();
                                    var plant = $(this).val();
                                    $.ajax({
                                        url: '{{ route('get.shops') }}',
                                        method: 'GET',
                                        data: { type: type, plant: plant },
                                        success: function(data) {
                                            $('#shopSelect').empty().append('<option value="">Select Shop</option>');
                                            $.each(data, function(index, value) {
                                                $('#shopSelect').append('<option value="'+value.shop+'">'+value.shop+'</option>');
                                            });
                                        }
                                    });
                                });

                                $('#shopSelect').change(function() {
                                    var type = $('#typeSelect').val();
                                    var plant = $('#plantSelect').val();
                                    var shop = $(this).val();
                                    $.ajax({
                                        url: '{{ route('get.opNos') }}',
                                        method: 'GET',
                                        data: { type: type, plant: plant, shop: shop },
                                        success: function(data) {
                                            $('#opNoSelect').empty().append('<option value="">Select OP. No</option>');
                                            $.each(data, function(index, value) {
                                                $('#opNoSelect').append('<option value="'+value.op_no+'">'+value.op_no+'</option>');
                                            });
                                        }
                                    });
                                });
                            </script>
                            <!-- List Checksheet Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">List Checksheet</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tableUser" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Machine Name (OP No.)</th>
                                                            <th>Type</th> <!-- Type Column -->
                                                            <th>Planning Date</th> <!-- Planning Date Column -->
                                                            <th>Actual Date</th> <!-- Actual Date Column -->
                                                            <th>Created By</th> <!-- Created By Column -->
                                                            <th>Status</th> <!-- Status Column -->
                                                            <th>PM Status</th> <!-- PM Status Column -->
                                                            <th>Action</th> <!-- Action Column -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @foreach ($items as $data)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $data->machine_name ?? '' }} ({{ $data->op_name ?? '' }})</td>
                                                            <td>{{ $data->type ?? 'Unknown' }}</td> <!-- Display Type -->
                                                            <td>{{ $data->planning_date ?? '' }}</td> <!-- Display Planning Date -->
                                                            <td>{{ $data->planning_date ? date('d/m/Y', strtotime($data->planning_date)) : '' }}</td> <!-- Planning Date -->
                                                            <td>{{ $data->actual_date ? date('d/m/Y', strtotime($data->actual_date)) : '' }}</td> <!-- Actual Date -->
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
                                                            <td>
                                                                @if($data->pm_status == 'Open')
                                                                    <span class="badge bg-warning">Open</span>
                                                                @elseif($data->pm_status == 'Close')
                                                                    <span class="badge bg-success">Close</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Unknown Status</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $data->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        Actions
                                                                    </button>
                                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $data->id }}">
                                                                        <li>
                                                                            <a title="Detail" class="dropdown-item" href="checksheet/detail/{{ encrypt($data->id) }}">
                                                                                <i class="fas fa-info me-2"></i>Detail
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <button title="Delete" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
                                                                                <i class="fas fa-trash-alt me-2"></i>Delete
                                                                            </button>
                                                                        </li>
                                                                        @if($data->status == 1)
                                                                            <li>
                                                                                <a href="checksheet/checkher/{{ encrypt($data->id) }}" class="dropdown-item" title="Check">
                                                                                    <i class="fas fa-search me-2"></i>Check
                                                                                </a>
                                                                            </li>
                                                                        @elseif($data->status == 0)
                                                                            <li>
                                                                                <a href="checksheet/fill/{{ encrypt($data->id) }}" class="dropdown-item" title="Fill">
                                                                                    <i class="fas fa-pencil-alt me-2"></i>Fill
                                                                                </a>
                                                                            </li>
                                                                        @elseif($data->status == 2)
                                                                            <li>
                                                                                <a href="checksheet/approve/{{ encrypt($data->id) }}" class="dropdown-item" title="Approve">
                                                                                    <i class="fas fa-thumbs-up me-2"></i>Approve
                                                                                </a>
                                                                            </li>
                                                                        @elseif($data->status == 3)
                                                                            <li>
                                                                                <a href="checksheet/update/{{ encrypt($data->id) }}" class="dropdown-item" title="Update">
                                                                                    <i class="fas fa-pencil-alt me-2"></i>Update
                                                                                </a>
                                                                            </li>
                                                                        @else
                                                                            <li>
                                                                                <a href="checksheet/generate-pdf/{{ encrypt($data->id) }}" class="dropdown-item" title="Generate PDF">
                                                                                    <i class="fas fa-file-pdf me-2"></i>Generate PDF
                                                                                </a>
                                                                            </li>
                                                                        @endif

                                                                        <!-- Button to change status from Open to Close -->
                                                                        @if($data->pm_status == 'Open')
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li>
                                                                                <!-- Button to trigger modal -->
                                                                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeStatusModal{{ $data->id }}" title="Change Status">
                                                                                    <i class="fas fa-exchange-alt me-2"></i>Change Status to Close
                                                                                </button>
                                                                            </li>
                                                                        @endif

                                                                        <li><hr class="dropdown-divider"></li>

                                                                        <!-- Button to show journey logs -->
                                                                        <li>
                                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#journeyModal{{ $data->id }}">
                                                                                <i class="fas fa-history me-2"></i>View Journey
                                                                            </a>
                                                                        </li>

                                                                        <!-- Button to show status logs -->
                                                                        @if($data->status_logs->isNotEmpty())
                                                                        <li>
                                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusLogModal{{ $data->id }}">
                                                                                <i class="fas fa-history me-2"></i>View Status Log
                                                                            </a>
                                                                        </li>
                                                                        @endif
                                                                    </ul>
                                                                </div>

                                                                <!-- Modal for changing status -->
                                                                <div class="modal fade" id="changeStatusModal{{ $data->id }}" tabindex="-1" aria-labelledby="changeStatusModalLabel{{ $data->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="changeStatusModalLabel{{ $data->id }}">Add Daily Report</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <form action="{{ url('checksheet/change-status') }}" method="POST">
                                                                                @csrf
                                                                                <div class="modal-body">
                                                                                    <input type="hidden" name="id_pm" value="{{ $data->id }}">
                                                                                    <input type="hidden" name="checksheet_id" value="{{ $data->checksheet_id }}">
                                                                                    <div class="mb-3">
                                                                                        <label for="shift" class="form-label">Shift</label>
                                                                                        <select class="form-select" id="shift" name="shift" required>
                                                                                            <option value="Day">Day</option>
                                                                                            <option value="Night">Night</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="date" class="form-label">Date</label>
                                                                                        <input value="{{ date('Y-m-d') }}" type="date" class="form-control" id="date" name="date" required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Modal for journey logs -->
                                                                <div class="modal fade" id="journeyModal{{ $data->id }}" tabindex="-1" aria-labelledby="journeyModalLabel{{ $data->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="journeyModalLabel{{ $data->id }}">Checksheet Journey</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                @if ($data->logs->isEmpty())
                                                                                    <p>No journey logs available for this checksheet.</p>
                                                                                @else
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-striped">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>No</th>
                                                                                                    <th>User</th>
                                                                                                    <th>Action</th>
                                                                                                    <th>Remark</th>
                                                                                                    <th>Date</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach ($data->logs as $log)
                                                                                                <tr>
                                                                                                    <td>{{ $loop->iteration }}</td>
                                                                                                    <td>{{ $log->user->name }}</td>
                                                                                                    <td>
                                                                                                        @if($log->action == 0)
                                                                                                            <span class="badge bg-primary">Update</span>
                                                                                                        @elseif($log->action == 1)
                                                                                                            <span class="badge bg-info">Check</span>
                                                                                                        @elseif($log->action == 2)
                                                                                                            <span class="badge bg-warning">Waiting Approval</span>
                                                                                                        @elseif($log->action == 3)
                                                                                                            <span class="badge bg-danger">Remand</span>
                                                                                                        @elseif($log->action == 4)
                                                                                                            <span class="badge bg-success">Done</span>
                                                                                                        @else
                                                                                                            <span class="badge bg-secondary">Unknown Status</span>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                    <td>{{ $log->remark }}</td>
                                                                                                    <td>{{ $log->created_at }}</td>
                                                                                                </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

<!-- Modal for status logs -->
@if($data->logStatus)
    <div class="modal fade" id="statusLogModal{{ $data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-detail-label-{{ $data->id }}">Detail of Historical Problem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Machine No:</strong> {{ $data->logStatus->machine->no_machine }}</h6>
                            <h6><strong>Date:</strong> {{ $data->logStatus->date }}</h6>
                            <h6><strong>Shift:</strong> {{ $data->logStatus->shift }}</h6>
                            <h6><strong>Shop:</strong> {{ $data->logStatus->shop }}</h6>
                            <h6><strong>Problem:</strong> {{ $data->logStatus->problem }}</h6>
                            <h6><strong>Cause:</strong> {{ $data->logStatus->cause }}</h6>
                            <h6><strong>Action:</strong> {{ $data->logStatus->action }}</h6>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Start Time:</strong> {{ $data->logStatus->start_time }}</h6>
                            <h6><strong>Finish Time:</strong> {{ $data->logStatus->finish_time }}</h6>
                            <h6><strong>Balance:</strong> {{ $data->logStatus->balance }} Hour</h6>
                            <h6><strong>PIC:</strong> {{ $data->logStatus->pic }}</h6>
                            <h6><strong>Remarks:</strong> {{ $data->logStatus->remarks }}</h6>
                            <h6><strong>Status:</strong> {{ $data->logStatus->status }}</h6>
                        </div>
                    </div>
                    @if($data->logStatus->img)
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <img src="{{ asset($data->logStatus->img) }}" class="img-fluid" alt="Problem Image">
                            </div>
                        </div>
                    @endif
                    <hr>
                    <h5 class="mb-3">Spare Parts Used</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Part No</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Stock Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->logStatus->spareParts as $part)
                                    <tr>
                                        <td>{{ $part->part->material ?? null }}</td>
                                        <td>{{ $part->part->material_description ?? null }}</td>
                                        <td>{{ $part->qty ?? 0 }}</td>
                                        <td>{{ $part->location ?? 'N/A' }}</td>
                                        <td>{{ $part->routes ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif

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
