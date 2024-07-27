@extends('layouts.master')

@section('content')
<style>
    .btn {
        width: 100px; /* Adjust width as needed */
        height: 40px; /* Adjust height as needed */
        font-size: 14px; /* Adjust font size as needed */
    }
</style>
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
                                <div class="col-md-4">
                                    <strong>Machine Name</strong>
                                    <p>{{ $machine->machine_name }}</p>
                                    <strong>Plant</strong>
                                    <p>{{ $machine->plant }}</p>
                                    <strong>Line</strong>
                                    <p>{{ $machine->line }}</p>
                                    <strong>OP No.</strong>
                                    <p>{{$machine->op_no}}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Process</strong>
                                    <p>{{ $machine->process }}</p>
                                    <strong>Maker</strong>
                                    <p>{{ $machine->maker }}</p>
                                    <strong>Model</strong>
                                    <p>{{ $machine->model }}</p>
                                    <strong>Serial Number</strong>
                                    <p>{{ $machine->serial_number }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Date</strong>
                                    <p>{{ $machine->date }}</p>
                                    <strong>Control Nc</strong>
                                    <p>{{ $machine->control_nc }}</p>
                                    <strong>Control Plc</strong>
                                    <p>{{ $machine->control_plc }}</p>
                                    <strong>Control Servo</strong>
                                    <p>{{ $machine->control_servo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                                                                <div class="btn-group">
                                                                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                        Actions
                                                                                    </button>
                                                                                    <ul class="dropdown-menu">
                                                                                        <li>
                                                                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-repair{{ $part->id }}">
                                                                                                <i class="fas fa-tools"></i>Repair
                                                                                            </button>
                                                                                        </li>
                                                                                        <li>
                                                                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-detail{{ $part->id }}">
                                                                                                <i class="fas fa-info"></i> Detail
                                                                                            </button>
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
                                                    <div class="row">
                                                        <div class="mb-3 col-sm-12">
                                                        </div>

                                                        <div class="table-responsive">
                                                            <table id="tablehistory" class="table table-bordered table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Machine No</th>
                                                                        <th>Date</th>
                                                                        <th>Shift</th>
                                                                        <th>Shop</th>
                                                                        <th>Problem</th>
                                                                        <th>Cause</th>
                                                                        <th>Action</th>
                                                                        <th>Start Time</th>
                                                                        <th>Finish Time</th>
                                                                        <th>Balance</th>
                                                                        <th>PIC</th>
                                                                        <th>Remarks</th>
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
                                                                        <td>{{ $data->machine->op_no }}</td>
                                                                        <td>{{ $data->date }}</td>
                                                                        <td>{{ $data->shift }}</td>
                                                                        <td>{{ $data->shop }}</td>
                                                                        <td>{{ $data->problem }}</td>
                                                                        <td>{{ $data->cause }}</td>
                                                                        <td>{{ $data->action }}</td>
                                                                        <td>{{ $data->start_time }}</td>
                                                                        <td>{{ $data->finish_time }}</td>
                                                                        <td>{{ $data->balance }} Hour</td>
                                                                        <td>{{ $data->pic }}</td>
                                                                        <td>{{ $data->remarks }}</td>
                                                                        <td>{{ $data->status }}</td>
                                                                        <td>
                                                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->id }}">Detail</button>
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
                                @foreach ($items as $data)
                                <!-- Modal for showing spare parts used in historical problem -->
                                <div class="modal fade" id="modal-detail-{{ $data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modal-detail-label-{{ $data->id }}">Detail of Historical Problem</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <h6><strong>Machine No:</strong> {{ $data->no_machine }}</h6>
                                                        <h6><strong>Date:</strong> {{ $data->date }}</h6>
                                                        <h6><strong>Shift:</strong> {{ $data->shift }}</h6>
                                                        <h6><strong>Shop:</strong> {{ $data->shop }}</h6>
                                                        <h6><strong>Problem:</strong> {{ $data->problem }}</h6>
                                                        <h6><strong>Cause:</strong> {{ $data->cause }}</h6>
                                                        <h6><strong>Action:</strong> {{ $data->action }}</h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6><strong>Start Time:</strong> {{ $data->start_time }}</h6>
                                                        <h6><strong>Finish Time:</strong> {{ $data->finish_time }}</h6>
                                                        <h6><strong>Balance:</strong> {{ $data->balance }} Hour</h6>
                                                        <h6><strong>PIC:</strong> {{ $data->pic }}</h6>
                                                        <h6><strong>Remarks:</strong> {{ $data->remarks }}</h6>
                                                        <h6><strong>Status:</strong> {{ $data->status }}</h6>
                                                    </div>
                                                </div>
                                                @if($data->img)
                                                <div class="row mb-3">
                                                    <div class="col-md-12 text-center">
                                                        <img src="{{ asset( $data->img) }}" class="img-fluid" alt="Problem Image">
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
                                                            @foreach ($data->spareParts as $part)
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
