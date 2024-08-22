@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-info-circle"></i></div>
                            Part Details
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1 style="color: white">{{ $part->material }} - {{ $part->material_description }}</h1>
            </div>
            <div class="card-body">
                @include('partials.alert')
                <div class="row mb-4">
                    <div  class="col-md-3 text-center">
                        <h5>Image</h5>
                        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @php
                                $imagePaths = $part->img ? json_decode($part->img) : [];
                                @endphp

                                @if(count($imagePaths) > 0)
                                @foreach($imagePaths as $key => $imagePath)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <div class="image-box" style="border: 4px solid rgba(0, 103, 127, 1); padding: 5px; background-color: #f8f9fa; border-radius: 10px;">
                                        <img src="{{ asset($imagePath) }}" class="d-block w-100 carousel-image" alt="Image {{ $key + 1 }}">
                                    </div>
                                </div>
                            @endforeach

                                @else
                                    <div class="carousel-item active">
                                        <div class="d-flex align-items-center justify-content-center" style="height: 300px; background-color: #f0f0f0; border: 1px solid #ccc;">
                                            <span class="text-muted">No Images Available</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if(count($imagePaths) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>

                        <h3 class="text-center">{{ $part->material }}</h3>

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal">
                            Manage Images
                        </button>
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
                                                <form id="searchForm" action="{{ url('/mst/part/add/image') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <h5>Add New Images</h5>
                                                    <input name="id" type="text" value="{{ $part->id }}" hidden>
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
                                                                <input type="hidden" name="id" value="{{ $part->id }}">
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

                    <div class="col-md-5">
                        <h5 class="mb-4">Part Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Type</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->type ?? 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Plant</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->plnt ?? 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Storage Location</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->sloc ?? 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Vendor</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->vendor ?? 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Unit</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->bun ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Beginning Quantity</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->begining_qty !== null ? number_format($part->begining_qty, 0) : 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Beginning Value</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->begining_value !== null ? number_format($part->begining_value, 0) : 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Total Stock</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                         <p class="m-0">{{ $part->total_stock !== null ? number_format($part->total_stock, 0) : 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Total Value</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->total_value !== null ? number_format($part->total_value, 0) : 'N/A' }}</p>
                                    </div>
                                </div>
                               <div class="card mb-4 border border-3 border-mkm rounded" style="position: relative; padding-top: 10px; height: 60px;">
                                    <div class="position-absolute text-white py-1 rounded-pill text-center" style="top: -15px; left: 10px; right: 10px; background-color: rgba(0, 103, 127, 1);">
                                        <strong>Currency</strong>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center p-1 text-center">
                                        <p class="m-0">{{ $part->currency ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Repair Part Information</h5>
                        <table id="RepairList" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Repaired Quantity</th>
                                    <th>Repair Date</th>
                                    <th>Storage Location</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repairParts as $repairPart)
                                    <tr>
                                        <td>{{ $repairPart->repaired_qty !== null ? number_format($repairPart->repaired_qty, 0) : 'N/A' }}</td>
                                        <td>{{ date('d M Y', strtotime($repairPart->repair_date)) }}</td>
                                        <td>{{ $repairPart->sloc }}</td>
                                        <td>{{ $repairPart->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <h5 class="mt-4" >Machines Using This Part</h5>
                <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                    <i class="fas fa-plus-square"></i> Add Machine
                </button>
                <!-- Add Machine Modal -->
<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddLabel">Add Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{url('/mst/sap/part/store/')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="machine_id" class="form-label">Machine</label>
                                <select class="form-select" id="machine_id" name="machine_id" required>
                                    <option value="" disabled selected>Select Machine</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->op_no }} - {{ $machine->machine_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="critical_part" class="form-label">Critical Part</label>
                                <input readonly value="{{$part->material_description}}" type="text" class="form-control" id="critical_part" name="critical_part" required>
                            </div>
                            <div hidden class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input readonly value="{{$part->type}}" type="text" class="form-control" id="type" name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="estimation_lifetime" class="form-label">Estimation Lifetime (in years)</label>
                                <input type="number" class="form-control" id="estimation_lifetime" name="estimation_lifetime" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Cost</label>
                                <input readonly value="{{ $part->total_value !== null ? number_format($part->total_value, 0) : 'N/A' }}" type="number" step="0.01" class="form-control" id="cost" name="cost" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_replace" class="form-label">Last Replace Date</label>
                                <input type="date" class="form-control" id="last_replace" name="last_replace" required>
                            </div>
                            <div class="mb-3">
                                <label for="safety_stock" class="form-label">Safety Stock</label>
                                <input type="number" class="form-control" id="safety_stock" name="safety_stock" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="part_id" value="{{ $part->id }}">
                    <input type="hidden" name="sap_stock" value="{{ $part->total_stock }}">
                    <input type="hidden" name="repair_stock" value="{{ $repairPartsTotalQty }}">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Machine</button>
                </div>
            </form>
        </div>
    </div>
</div>


                <table id="MachineList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OP. No.</th>
                            <th>Machine Name</th>
                            <th>Critical Part</th>
                            <th>Type</th>
                            <th>Estimation Lifetime</th>
                            <th>Cost</th>
                            <th>Last Replace</th>
                            <th>Safety Stock</th>
                            <th>Repair Stock</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($machineParts as $machinePart)
                            <tr>
                                <td>{{ $machinePart->machine->op_no }}</td>
                                <td>{{ $machinePart->machine->machine_name }}</td>
                                <td>{{ $machinePart->critical_part }}</td>
                                <td>{{ $machinePart->type }}</td>
                                <td>{{ $machinePart->estimation_lifetime }}</td>
                                <td>{{ $machinePart->cost !== null ? number_format($machinePart->cost, 0) : 'N/A' }}</td>
                                <td>{{ date('d M Y', strtotime($machinePart->last_replace )) }}</td>
                                <td>{{ $machinePart->safety_stock }}</td>
                                <td>{{ $machinePart->repair_stock !== null ? number_format($machinePart->repair_stock, 0) : 'N/A' }}</td>
                                <td>{{  $machinePart->total  !== null ? number_format( $machinePart->total , 0) : 'N/A' }}</td>
                                <td>
                                    @if ($machinePart->status)
                                        @php
                                            $statusClass = $machinePart->status == 'Safe' ? 'btn-success' : ($machinePart->status == 'Need to Order' ? 'btn-warning' : 'btn-danger');
                                        @endphp
                                        <button class="btn {{ $statusClass }} btn-sm">
                                            {{ $machinePart->status }}
                                        </button>
                                    @else
                                        <button class="btn btn-danger btn-sm">
                                            Out Of Stock
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
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

<script>
    $(document).ready(function() {
        $('#RepairList').DataTable({
            "pageLength": 6, // Limit the number of rows to 2 per page
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
        });

        $('#MachineList').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
        });
    });
</script>
@endsection
