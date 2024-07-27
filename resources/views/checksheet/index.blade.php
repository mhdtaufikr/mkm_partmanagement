@extends('layouts.master')

@section('content')

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Scan QR Machine</h3>
                                </div>
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

                                    <!-- Validasi form -->
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
                                    <!-- End validasi form -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <form action="{{ url('/checksheet/scan') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row mb-4">
                                                        <div class="col-md-3">
                                                            <label for="plantSelect">Select Plant</label>
                                                            <select name="plant" id="plantSelect" class="form-control">
                                                                <option value="">Select Plant</option>
                                                                @foreach($plants as $plant)
                                                                    <option value="{{ $plant->plant }}">{{ $plant->plant }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="locationSelect">Select Location</label>
                                                            <select name="location" id="locationSelect" class="form-control">
                                                                <option value="">Select Location</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="lineSelect">Select Line</label>
                                                            <select name="line" id="lineSelect" class="form-control">
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
                                                        document.getElementById('plantSelect').addEventListener('change', function() {
                                                                    fetchLocations(this.value);
                                                                });

                                                                document.getElementById('locationSelect').addEventListener('change', function() {
                                                                    fetchLines(document.getElementById('plantSelect').value, this.value);
                                                                });

                                                                document.getElementById('lineSelect').addEventListener('change', function() {
                                                                    fetchOpNos(document.getElementById('plantSelect').value, document.getElementById('locationSelect').value, this.value);
                                                                });

                                                                function fetchLocations(plant) {
                                                                    let url = `/get-locations?plant=${plant}`;
                                                                    console.log('Fetching URL:', url); // Log URL for debugging

                                                                    fetch(url)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            console.log('Fetched data:', data); // Debug log
                                                                            let selectElement = document.getElementById('locationSelect');
                                                                            selectElement.innerHTML = '<option value="">Select Location</option>';
                                                                            document.getElementById('lineSelect').innerHTML = '<option value="">Select Line</option>';
                                                                            document.getElementById('opNoSelect').innerHTML = '<option value="">Select OP. No</option>';

                                                                            data.forEach(item => {
                                                                                const option = new Option(item.location, item.location);
                                                                                selectElement.add(option);
                                                                            });
                                                                        })
                                                                        .catch(error => console.error('Error fetching data:', error));
                                                                }

                                                                function fetchLines(plant, location) {
                                                                    let url = `/get-lines?plant=${plant}&location=${location}`;
                                                                    console.log('Fetching URL:', url); // Log URL for debugging

                                                                    fetch(url)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            console.log('Fetched data:', data); // Debug log
                                                                            let selectElement = document.getElementById('lineSelect');
                                                                            selectElement.innerHTML = '<option value="">Select Line</option>';
                                                                            document.getElementById('opNoSelect').innerHTML = '<option value="">Select OP. No</option>';

                                                                            data.forEach(item => {
                                                                                const option = new Option(item.line, item.line);
                                                                                selectElement.add(option);
                                                                            });
                                                                        })
                                                                        .catch(error => console.error('Error fetching data:', error));
                                                                }

                                                                function fetchOpNos(plant, location, line) {
                                                                    let url = `/get-opnos?plant=${plant}&location=${location}&line=${line}`;
                                                                    console.log('Fetching URL:', url); // Log URL for debugging

                                                                    fetch(url)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            console.log('Fetched data:', data); // Debug log
                                                                            let selectElement = document.getElementById('opNoSelect');
                                                                            selectElement.innerHTML = '<option value="">Select OP. No</option>';

                                                                            data.forEach(item => {
                                                                                const option = new Option(`${item.op_name} - (${item.machine_name})`, item.op_name);
                                                                                selectElement.add(option);
                                                                            });
                                                                        })
                                                                        .catch(error => console.error('Error fetching data:', error));
                                                                }

                                                    </script>



                                                    <div class="d-flex justify-content-center">
                                                        <div id="qr-reader" style="width:500px"></div>
                                                        <div id="qr-reader-results"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-center mt-3">
                                                        <input readonly type="text" name="no_mechine" id="qr-value" class="form-control" placeholder="Scanned QR Code Value">
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center">
                                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit</button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                            <!-- /.card -->

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">List Checksheet</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tableUser" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Machine Name (OP No.)</th>
                                                            <th>Department (Shop)</th>
                                                            <th>Created By</th>
                                                            <th>Mfg.Date</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                        $no = 1;
                                                        @endphp
                                                        @foreach ($items as $data)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $data->machine_name ?? '' }} ({{ $data->op_name ?? '' }})</td>
                                                            <td>{{ $data->dept ?? '' }} ({{ $data->shop ?? '' }})</td>
                                                            <td>{{ $data->created_by }}</td>
                                                            <td>{{ $data->mfg_date ?? '' }}</td>
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
                                                                <div class="btn-group">
                                                                    <a href="checksheet/detail/{{ encrypt($data->id) }}" class="btn btn-primary btn-sm" title="Detail">
                                                                        <i class="fas fa-info"></i>
                                                                    </a>
                                                                    <button title="Delete" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                    @if($data->status == 1)
                                                                    <a href="checksheet/checkher/{{ encrypt($data->id) }}" class="btn btn-success btn-sm" title="Check">
                                                                        <i class="fas fa-search"></i>
                                                                    </a>
                                                                @elseif($data->status == 0)
                                                                    <a href="checksheet/fill/{{ encrypt($data->id) }}" class="btn btn-success btn-sm" title="Fill">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                    </a>
                                                                @elseif($data->status == 2)
                                                                    <a href="checksheet/approve/{{ encrypt($data->id) }}" class="btn btn-success btn-sm" title="Approve">
                                                                        <i class="fas fa-thumbs-up"></i>
                                                                    </a>
                                                                @elseif($data->status == 3)
                                                                    <a href="checksheet/update/{{ encrypt($data->id) }}" class="btn btn-success btn-sm" title="Update">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="checksheet/generate-pdf/{{ encrypt($data->id) }}" class="btn btn-success btn-sm" title="Generate PDF">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                    </a>
                                                                @endif

                                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <span class="visually-hidden">Toggle Dropdown</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#journeyModal{{ $data->id }}"><i class="fas fa-history me-1"></i>View Journey</a></li>
                                                                        <!-- Add other dropdown items here if needed -->
                                                                    </ul>
                                                                </div>
                                                                <!-- Modal -->
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


<script>
    // Populate options dynamically from PHP variable
    var machines = @json($machines);

    // Function to populate select options
    function populateOptions() {
        var select = $('#machineSelect');
        select.empty();
        select.append('<option></option>'); // Add an empty option
        machines.forEach(function(machine) {
            select.append('<option value="' + machine.id + '">' + machine.machine_name + '</option>');
        });
        // Initialize Chosen plugin
        select.chosen();
    }

    // Call the function to populate options on page load
    $(document).ready(function() {
        populateOptions();
    });
</script>



@endsection
