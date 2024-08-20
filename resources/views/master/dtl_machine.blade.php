@extends('layouts.master')

@section('content')
{{-- <style>
    .btn {
        width: 100px; /* Adjust width as needed */
        height: 40px; /* Adjust height as needed */
        font-size: 14px; /* Adjust font size as needed */
    }
</style> --}}
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4"></div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header"></section>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-header-actions mb-4">
                        <div class="card-header text-dark">
                            <h3 style="color: white">Machine Details</h3>
                        </div>
                        <div class="card-body mt-3">
                            @include('partials.alert')

                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @php
                                            $imagePaths = $machine->img ? json_decode($machine->img) : [];
                                            @endphp

                                            @foreach($imagePaths as $key => $imagePath)
                                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                <img src="{{ asset($imagePath) }}" class="d-block w-100 carousel-image" alt="Image {{ $key + 1 }}">
                                            </div>
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>

                                    <h3 class="text-center">{{ $machine->op_no }}</h3>

                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        Manage Images
                                    </button>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>OP No.</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->op_no ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Plant</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->plant ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Line</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->line ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Location</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->location ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Asset No.</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->asset_no ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Machine Name</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->machine_name ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Process</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->process ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Maker</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->maker ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Model</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->model ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Serial Number</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->serial_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Mfg Date</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->mfg_date ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Install Date</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->install_date ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded" style="position: relative; padding-top: 10px; height: 80px;">
                                        <div style="position: absolute; top: -10px; left: 10px; background-color: rgba(0, 103, 127, 1); color: white; padding: 3px 10px; border-radius: 15px;">
                                            <strong>Specification (Electrical Control)</strong>
                                        </div>
                                        <div class="card-body d-flex align-items-center justify-content-center p-1" style="text-align: center;">
                                            <p style="margin: 0;">{{ $machine->electrical_co ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            {{-- Modal Image CRUD --}}
                            <!-- Modal -->
                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Manage Images</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Add a container for the "Add New" row -->
                                            <div class="mb-3">
                                                <form id="searchForm" action="{{ url('/mst/machine/add/image') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <h5>Add New Images</h5>
                                                    <input name="id" type="text" value="{{ $machine->id }}" hidden>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control" name="new_images[]" multiple>
                                                        <button class="btn btn-primary" type="submit">Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="row">
                                                <!-- Loop through the images and display them in a grid -->
                                                @foreach($imagePaths as $key => $imagePath)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card">
                                                        <img src="{{ asset($imagePath) }}" class="card-img-top" alt="Image {{ $key + 1 }}" style="height: 200px; width: auto;">
                                                        <div class="card-body">
                                                            <!-- Use a form to delete the image -->
                                                            <form action="{{ route('machine.delete.image') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="img_path" value="{{ $imagePath }}">
                                                                <input type="hidden" name="id" value="{{ $machine->id }}">
                                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <!-- Add buttons for saving changes or performing other actions -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .card-body p {
                            margin: 0;
                        }
                        .card-body {
                            padding: 0.5rem;
                        }
                        .card {
                            margin-bottom: 0.5rem;
                        }
                    </style>

                </div>

                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="part-list-tab" data-bs-toggle="tab" data-bs-target="#part-list" type="button" role="tab" aria-controls="part-list" aria-selected="true">Part List</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="daily-report-tab" data-bs-toggle="tab" data-bs-target="#daily-report" type="button" role="tab" aria-controls="daily-report" aria-selected="false">Machine History</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documentation-tab" data-bs-toggle="tab" data-bs-target="#documentation" type="button" role="tab" aria-controls="documentation" aria-selected="false">Documentation</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="myTabContent">
                                <!-- Part List Tab -->
                                <div class="tab-pane fade show active" id="part-list" role="tabpanel" aria-labelledby="part-list-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="mb-3 col-sm-12">
                                                            <div class="col-sm-12">
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

                                                                @if (count($errors)>0)
                                                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                        <ul>
                                                                            <li><strong>Data Process Failed !</strong></li>
                                                                            @foreach ($errors->all() as $error)
                                                                                <li><strong>{{ $error }}</strong></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                                                                <i class="fas fa-plus-square"></i> Add Part
                                                            </button>
                                                            <!-- Modal for Adding New Part -->
                                                            <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="modal-add-label">Add New Part</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ url('/mst/machine/add-part') }}" enctype="multipart/form-data" method="POST">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <div class="mb-3 form-group">
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <label for="part_id">Part</label>
                                                                                            <input name="machine_id" type="text" value="{{ $machine->id }}" hidden>
                                                                                            <select name="part_id" id="part_id" class="form-control" required>
                                                                                                <option value="">-- Select Part --</option>
                                                                                                @foreach($parts as $part)
                                                                                                    <option value="{{ $part->id }}" data-type="{{ $part->type }}" data-cost="{{ $part->total_value }}" data-sap_stock="{{ $part->total_stock }}" data-repair_stock="{{ $part->total_repaired_qty ?? 0 }}">
                                                                                                        {{ $part->material }} - {{ $part->material_description }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                            <label for="estimation_lifetime">Estimation Lifetime</label>
                                                                                            <input name="estimation_lifetime" class="form-control" type="number" required>
                                                                                            <label for="last_replace">Last Replace</label>
                                                                                            <input name="last_replace" class="form-control" type="date" required>
                                                                                            <label for="sap_stock">SAP Stock</label>
                                                                                            <input name="sap_stock" id="sap_stock" class="form-control" type="number" step="0.01" required disabled>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <label for="type">Type</label>
                                                                                            <input name="type" id="type" class="form-control" type="text" required disabled>
                                                                                            <label for="cost">Cost</label>
                                                                                            <input name="cost" id="cost" class="form-control" type="number" step="0.01" required disabled>
                                                                                            <label for="safety_stock">Safety Stock</label>
                                                                                            <input name="safety_stock" class="form-control" type="number" required>
                                                                                            <label for="total">Total</label>
                                                                                            <input name="total" id="total" class="form-control" type="number" step="0.01" required disabled>
                                                                                            <input type="hidden" name="total_hidden" id="total_hidden">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer justify-content-between">
                                                                                <button type="button" class="btn btn-dark btn-default" data-bs-dismiss="modal">Close</button>
                                                                                <input type="submit" class="btn btn-primary" value="Submit">
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <script>
                                                                document.getElementById('part_id').addEventListener('change', function() {
                                                                    const selectedOption = this.options[this.selectedIndex];
                                                                    const type = selectedOption.getAttribute('data-type');
                                                                    const cost = selectedOption.getAttribute('data-cost');
                                                                    const sapStock = selectedOption.getAttribute('data-sap_stock');
                                                                    const partId = selectedOption.value;

                                                                    // Fetch the repair stock dynamically
                                                                    fetch(`/get-repair-stock/${partId}`)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            const repairStock = data.repair_stock || 0;

                                                                            document.getElementById('type').value = type;
                                                                            document.getElementById('cost').value = cost;
                                                                            document.getElementById('sap_stock').value = sapStock;
                                                                            document.getElementById('repair_stock').value = repairStock;
                                                                            const total = parseFloat(sapStock) + parseFloat(repairStock);
                                                                            document.getElementById('total').value = total;
                                                                            document.getElementById('total_hidden').value = total;
                                                                        });
                                                                });

                                                                document.getElementById('repair_stock').addEventListener('input', function() {
                                                                    const sapStock = parseFloat(document.getElementById('sap_stock').value) || 0;
                                                                    const repairStock = parseFloat(this.value) || 0;
                                                                    const total = sapStock + repairStock;
                                                                    document.getElementById('total').value = total;
                                                                    document.getElementById('total_hidden').value = total;
                                                                });
                                                            </script>
                                                        </div>

                                                        <div class="table-responsive">
                                                            <table id="tableUser" class="table table-bordered table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Material</th>
                                                                        <th>Description</th>
                                                                        <th>Estimation Lifetime</th>
                                                                        <th>Last Replace</th>
                                                                        <th>Periodic Check</th>
                                                                        <th>Periodic Status</th> <!-- New Periodic Status column -->
                                                                        <th>Type</th>
                                                                        <th>SAP Stock</th>
                                                                        <th>Repair Stock</th>
                                                                        <th>Total Stock</th>
                                                                        <th>Safety Stock</th>
                                                                        <th>Status</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php

                                                                        $no=1;
                                                                    @endphp
                                                                    @foreach ($machine->spareParts as $part)
                                                                    @php
                                                                $currentYear = date('Y');
                                                                $currentMonth = date('m');

                                                                $status = $machine->inventoryStatus->firstWhere('part_id', $part->part_id);

                                                                // Calculate the periodic check date
                                                                $periodicCheckDate = date('d M Y', strtotime($part->last_replace . ' + ' . $part->estimation_lifetime . ' years'));
                                                                $periodicCheckYear = date('Y', strtotime($part->last_replace . ' + ' . $part->estimation_lifetime . ' years'));
                                                                $periodicCheckMonth = date('m', strtotime($part->last_replace . ' + ' . $part->estimation_lifetime . ' years'));

                                                                // Determine the periodic status based on the current date (year and month) and the periodic check date
                                                                $yearDifference = $currentYear - $periodicCheckYear;
                                                                $monthDifference = $currentMonth - $periodicCheckMonth;

                                                                if ($yearDifference > 0 || ($yearDifference == 0 && $monthDifference > 0)) {
                                                                    $periodicStatus = 'Overdue';
                                                                    $statusClass = 'btn-danger';
                                                                } elseif ($yearDifference == 0 && $monthDifference == 0) {
                                                                    $periodicStatus = 'Check';
                                                                    $statusClass = 'btn-warning';
                                                                } else {
                                                                    $periodicStatus = 'Safe';
                                                                    $statusClass = 'btn-success';
                                                                }

                                                                $status = $machine->inventoryStatus->firstWhere('part_id', $part->part_id);
                                                                        // Calculate the periodic check date
                                                                        $periodicCheck = date('d M Y', strtotime($part->last_replace . ' + ' . $part->estimation_lifetime . ' years'));
                                                                    @endphp
                                                                        <tr>
                                                                            @php
                                                                                $status = $machine->inventoryStatus->firstWhere('part_id', $part->part_id);
                                                                            @endphp
                                                                            <td>{{ $status ? $status->material : '-' }}</td>
                                                                            <td>{{ $status ? $status->material_description : '-' }}</td>
                                                                            <td>{{ $part->estimation_lifetime }}</td>
                                                                            <td>{{ date('d M Y', strtotime($part->last_replace)) }}</td> {{-- Format the date --}}
                                                                            <td>{{ $periodicCheck }}</td>
                                                                            <td>
                                                                                <button class="btn {{ $statusClass }} btn-sm">
                                                                                    {{ $periodicStatus }}
                                                                                </button>
                                                                            </td> <!-- Display the periodic status with appropriate styling -->
                                                                            <td>{{ $part->type }}</td>
                                                                            <td>{{ $status ? (int) $status->sap_stock : 0 }}</td>
                                                                            <td>{{ $status ? (int) $status->repair_stock : 0 }}</td>
                                                                            <td>{{ $status ? (int) $status->total : 0 }}</td>
                                                                            <td>{{ $status ? (int) $status->safety_stock : 0 }}</td>
                                                                            <td>
                                                                                @if ($status)
                                                                                    @php
                                                                                        $statusClass = $status->status == 'Safe' ? 'btn-success' : ($status->status == 'Need to Order' ? 'btn-warning' : 'btn-danger');
                                                                                    @endphp
                                                                                    <button class="btn {{ $statusClass }} btn-sm">
                                                                                        {{ $status->status }}
                                                                                    </button>
                                                                                @else
                                                                                    <button class="btn btn-danger btn-sm">
                                                                                        Out Of Stock
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div class="dropdown">
                                                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        Actions
                                                                                    </button>
                                                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                        <li>
                                                                                            <button title="Repair Part" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-repair{{ $part->id }}">
                                                                                                <i class="fas fa-tools me-2"></i>Repair Part
                                                                                            </button>
                                                                                        </li>
                                                                                        <li>
                                                                                            <button title="Detail Part" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-detail{{ $part->id }}">
                                                                                                <i class="fas fa-info me-2"></i>Detail Part
                                                                                            </button>
                                                                                        </li>
                                                                                        <li><hr class="dropdown-divider"></li>
                                                                                        <li>
                                                                                            <a target="_blank" title="Master Part" class="dropdown-item" href="{{ url('/mst/sap/part/info/' . encrypt($part->part_id)) }}">
                                                                                                <i class="fas fa-info me-2"></i>Master Part
                                                                                            </a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>


                                                                                {{-- Modal Update --}}
                                                                                <div class="modal fade" id="modal-repair{{ $part->id }}" tabindex="-1" aria-labelledby="modal-repair{{ $part->id }}-label" aria-hidden="true">
                                                                                    <div class="modal-dialog">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title" id="modal-repair{{ $part->id }}-label">Repair Part</h4>
                                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                            </div>
                                                                                            <form action="{{ url('/mst/machine/repair') }}" enctype="multipart/form-data" method="POST">
                                                                                                @csrf
                                                                                                <div class="modal-body">
                                                                                                    <div class="mb-3 form-group">
                                                                                                        <input type="text" name="id" value="{{$part->part_id}}" hidden>
                                                                                                        <label for="qty">Quantity</label>
                                                                                                        <input name="qty" class="form-control" type="number" required>
                                                                                                        <label for="location">Storage Location</label>
                                                                                                        <input name="location" class="form-control" type="text" required>
                                                                                                        <label for="date">Date</label>
                                                                                                        <input name="date" class="form-control" type="date" required>
                                                                                                        <label for="remark">Remarks</label>
                                                                                                        <input name="remark" class="form-control" type="text">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer justify-content-between">
                                                                                                    <button type="button" class="btn btn-dark btn-default" data-bs-dismiss="modal">Close"></button>
                                                                                                    <input type="submit" class="btn btn-primary" value="Submit">
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                {{-- Modal Update --}}

                                                                                <!-- Modal for part repair details -->
                                                                                <div class="modal fade" id="modal-detail{{ $part->id }}" tabindex="-1" aria-labelledby="modal-detail{{ $part->id }}-label" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-lg">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title" id="modal-detail{{ $part->id }}-label">Repair Details -  {{ $part->part->material }}</h5>
                                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <table class="table table-bordered table-striped">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Quantity</th>
                                                                                                            <th>Repair Date</th>
                                                                                                            <th>Location</th>
                                                                                                            <th>Notes</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($part->repairs as $repair)
                                                                                                            <tr>
                                                                                                                <td>{{ intval($repair->repaired_qty) }}</td>
                                                                                                                <td>{{ date('d M Y', strtotime($repair->repair_date)) }}</td>
                                                                                                                <td>{{ $repair->sloc }}</td>
                                                                                                                <td>{{ $repair->notes }}</td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
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
                                    </div>
                                </div>

                                <!-- Daily Report Tab -->
                                <div class="tab-pane fade" id="daily-report" role="tabpanel" aria-labelledby="daily-report-tab">
                                    <!-- Add your daily report content here -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="tablehistory" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Type</th> <!-- New Type column -->
                                                                    <th>Date</th>
                                                                    <th>Shift</th>
                                                                    <th>Shop</th>
                                                                    <th>Problem</th>
                                                                    <th>Analysis & Cause</th>
                                                                    <th>Action Taken</th>
                                                                    <th>Repair Hours</th>
                                                                    <th>Remarks</th>
                                                                    <th>Person In Charge</th>
                                                                    <th>Status</th> <!-- Added Status column -->
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $no = 1;
                                                                @endphp
                                                                @foreach ($combinedData as $data)
                                                                <tr>
                                                                    <td>{{ $no++ }}</td>
                                                                    <td>{{ $data->Category }}</td>
                                                                    <td>{{ $data->date }}</td>
                                                                    <td>{{ $data->data->shift ?? '-' }}</td>
                                                                    <td>{{ $data->data->shop }}</td>
                                                                    <td>
                                                                        @if($data->type == 'Daily Report')
                                                                            {{ $data->data->problem }}
                                                                        @else
                                                                           PM schedule
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($data->type == 'Daily Report')
                                                                            {{ $data->data->cause }}
                                                                        @else
                                                                           PM schedule
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($data->type == 'Daily Report')
                                                                            {{ $data->data->action }}
                                                                        @else
                                                                           PM schedule
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ $data->data->start_time ? date('H:i', strtotime($data->data->start_time)) : '-' }} -
                                                                        {{ $data->data->finish_time ? date('H:i', strtotime($data->data->finish_time)) : '-' }}
                                                                        @if($data->data->balance)
                                                                            (Total: {{ number_format($data->data->balance, 2) }} hours)
                                                                        @endif
                                                                    </td>

                                                                    <td>{{ $data->data->remarks ?? 'OK' }}</td>
                                                                    <td>{{ $data->data->pic ?? 'Hmd. Prod' }}</td>
                                                                    <td>
                                                                        @if($data->type == 'Daily Report')
                                                                            @if($data->data->status == 'Open')
                                                                                <span class="badge bg-warning">Open</span>
                                                                            @elseif($data->data->status == 'Close')
                                                                                <span class="badge bg-success">Close</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">Unknown Status</span>
                                                                            @endif
                                                                        @else
                                                                            @if($data->data->pm_status == 'Open')
                                                                                <span class="badge bg-warning">Open</span>
                                                                            @elseif($data->data->pm_status == 'Close')
                                                                                <span class="badge bg-success">Close</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">Unknown Status</span>
                                                                            @endif
                                                                        @endif
                                                                    </td> <!-- Status with conditional badge based on type -->
                                                                    <td>
                                                                        @if($data->type == 'Daily Report')
                                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->data->id }}">Detail</button>
                                                                        @else
                                                                        <a target="_blank" title="Detail" class=" btn btn-sm btn-primary" href="{{url("checksheet/detail/".encrypt($data->data->id_ch))}} ">
                                                                            Detail
                                                                        </a>
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
                                </div>
                                <!-- Modals for details -->
                                @foreach ($combinedData as $data)
                                @if ($data->type == 'Daily Report')
                                @include('partials.logmachine', ['data' => $data])
                                @endif
                                @endforeach

                                <!-- Documentation Tab -->
                                <div class="tab-pane fade" id="documentation" role="tabpanel" aria-labelledby="documentation-tab">
                                    <!-- Add your documentation content here -->
                                    <div class="container-fluid">
                                        <h3>Documentation</h3>
                                        <!-- Documentation content goes here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
</main>

<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $("#tableUser").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
<script>
    $(document).ready(function() {
        var table = $("#tablehistory").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>

@endsection
