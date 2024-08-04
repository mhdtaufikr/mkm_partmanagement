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
                            <h3>Machine Details</h3>
                        </div>
                        <div class="card-body">
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

                                @if (count($errors) > 0)
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

                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @php
                                            $imagePaths = $machine->img ? json_decode($machine->img) : [];
                                            @endphp

                                            @foreach($imagePaths as $key => $imagePath)
                                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                <img src="{{ asset($imagePath) }}" class="d-block w-100" alt="Image {{ $key + 1 }}">
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

                                    <h3 class="text-center">{{ $machine->machine_name }}</h3>

                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        Manage Images
                                    </button>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>OP No.</strong>
                                            <p>{{ $machine->op_no }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Plant</strong>
                                            <p>{{ $machine->plant }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Line</strong>
                                            <p>{{ $machine->line }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Location</strong>
                                            <p>{{ $machine->location }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Asset No.</strong>
                                            <p>{{ $machine->asset_no }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Machine Name</strong>
                                            <p>{{ $machine->machine_name }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Process</strong>
                                            <p>{{ $machine->process }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Maker</strong>
                                            <p>{{ $machine->maker }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Model</strong>
                                            <p>{{ $machine->model }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Serial Number</strong>
                                            <p>{{ $machine->serial_number }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Mfg Date</strong>
                                            <p>{{ $machine->mfg_date }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Install Date</strong>
                                            <p>{{ $machine->install_date }}</p>
                                        </div>
                                    </div>

                                    <div class="card mb-2 border border-dark rounded">
                                        <div class="card-body p-2">
                                            <strong>Specification (Electrical Control)</strong>
                                            <p>{{ $machine->electrical_co }}</p>
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
                                    <button style="color: black" class="nav-link active" id="part-list-tab" data-bs-toggle="tab" data-bs-target="#part-list" type="button" role="tab" aria-controls="part-list" aria-selected="true">Part List</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button style="color: black" class="nav-link" id="daily-report-tab" data-bs-toggle="tab" data-bs-target="#daily-report" type="button" role="tab" aria-controls="daily-report" aria-selected="false">Machine History</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button style="color: black" class="nav-link" id="documentation-tab" data-bs-toggle="tab" data-bs-target="#documentation" type="button" role="tab" aria-controls="documentation" aria-selected="false">Documentation</button>
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
                                                                        <th>Type</th>
                                                                        <th>SAP Stock</th>
                                                                        <th>Repair Stock</th>
                                                                        <th>Total</th>
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

                                                                        <tr>
                                                                            @php
                                                                                $status = $machine->inventoryStatus->firstWhere('part_id', $part->part_id);
                                                                            @endphp
                                                                            <td>{{ $status ? $status->material : '-' }}</td>
                                                                            <td>{{ $status ? $status->material_description : '-' }}</td>
                                                                            <td>{{ $part->estimation_lifetime }}</td>
                                                                            <td>{{ date('d M Y', strtotime($part->last_replace)) }}</td> {{-- Format the date --}}
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
                                                        <table id="combinedTable" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Date</th>
                                                                    <th>Type</th>
                                                                    <th>Details</th>
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
                                                                    <td>{{ $data->date }}</td>
                                                                    <td>{{ $data->type }}</td>
                                                                    <td>
                                                                        @if ($data->type == 'Daily Report')
                                                                            Machine No: {{ $data->data->machine->op_no }}<br>
                                                                            Problem: {{ $data->data->problem }}<br>
                                                                            <!-- Add more details if needed -->
                                                                        @else
                                                                            Machine Name: {{ $data->data->machine_name }}<br>
                                                                            Department: {{ $data->data->dept }}<br>
                                                                            <!-- Add more details if needed -->
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->data->id }}">Detail</button>
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
                                    <!-- Modal for Daily Report -->
                                    <div class="modal fade" id="modal-detail-{{ $data->data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->data->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modal-detail-label-{{ $data->data->id }}">Detail of Daily Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h6><strong>Machine No:</strong> {{ $data->data->machine->op_no }}</h6>
                                                            <h6><strong>Date:</strong> {{ $data->data->date }}</h6>
                                                            <h6><strong>Shift:</strong> {{ $data->data->shift }}</h6>
                                                            <h6><strong>Shop:</strong> {{ $data->data->shop }}</h6>
                                                            <h6><strong>Problem:</strong> {{ $data->data->problem }}</h6>
                                                            <h6><strong>Cause:</strong> {{ $data->data->cause }}</h6>
                                                            <h6><strong>Action:</strong> {{ $data->data->action }}</h6>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6><strong>Start Time:</strong> {{ $data->data->start_time }}</h6>
                                                            <h6><strong>Finish Time:</strong> {{ $data->data->finish_time }}</h6>
                                                            <h6><strong>Balance:</strong> {{ $data->data->balance }} Hour</h6>
                                                            <h6><strong>PIC:</strong> {{ $data->data->pic }}</h6>
                                                            <h6><strong>Remarks:</strong> {{ $data->data->remarks }}</h6>
                                                            <h6><strong>Status:</strong> {{ $data->data->status }}</h6>
                                                        </div>
                                                    </div>
                                                    @if($data->data->img)
                                                    <div class="row mb-3">
                                                        <div class="col-md-12 text-center">
                                                            <img src="{{ asset( $data->data->img) }}" class="img-fluid" alt="Problem Image">
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
                                                                @foreach ($data->data->spareParts as $part)
                                                                <tr>
                                                                    <td>{{ $part->part->material }}</td>
                                                                    <td>{{ $part->part->material_description }}</td>
                                                                    <td>{{ $part->qty }}</td>
                                                                    <td>{{ $part->location }}</td>
                                                                    <td>{{ $part->routes }}</td>
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
                                @else
                                    <!-- Modal for Preventive Maintenance -->
                                    <div class="modal fade" id="modal-detail-{{ $data->data->checksheet_id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->data->checksheet_id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modal-detail-label-{{ $data->data->checksheet_id }}">Detail of Preventive Maintenance</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h6><strong>Machine Name:</strong> {{ $data->data->machine_name }}</h6>
                                                            <h6><strong>Department:</strong> {{ $data->data->dept }}</h6>
                                                            <h6><strong>Shop:</strong> {{ $data->data->shop }}</h6>
                                                            <h6><strong>Effective Date:</strong> {{ $data->data->effective_date }}</h6>
                                                            <h6><strong>Mfg Date:</strong> {{ $data->data->mfg_date }}</h6>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6><strong>Type:</strong> {{ $data->data->type }}</h6>
                                                            <h6><strong>Revision:</strong> {{ $data->data->revision }}</h6>
                                                            <h6><strong>Procedure No:</strong> {{ $data->data->no_procedure }}</h6>
                                                            <h6><strong>Planning Date:</strong> {{ $data->data->planning_date }}</h6>
                                                            <h6><strong>Actual Date:</strong> {{ $data->data->actual_date }}</h6>
                                                            <h6><strong>Status:</strong> {{ $data->data->status }}</h6>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <h5 class="mb-3">Checksheet Logs</h5>
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
                                                                @foreach ($data->data->logs as $log)
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
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
