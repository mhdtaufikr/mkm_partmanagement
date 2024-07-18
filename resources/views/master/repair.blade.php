@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i data-feather="tool"></i></div>
                    Repair Parts Management
                </h1>
                <div class="page-header-subtitle">Manage repair parts records</div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Repair Parts</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                                                <i class="fas fa-plus-square"></i> Add Repair Part
                                            </button>

                                            <!-- Modal for Adding Repair Part -->
                                            <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modal-add-label">Add Repair Part</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="repair-part-form" action="{{ url('/repair-parts/store') }}" method="POST">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="part_id" class="form-label">Part</label>
                                                                            <select class="form-control" id="part_id" name="part_id" required>
                                                                                <option value="">-- Select Part --</option>
                                                                                @foreach($parts as $part)
                                                                                    <option value="{{ $part->id }}">{{ $part->material }} - {{ $part->material_description }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="sloc" class="form-label">SLOC</label>
                                                                            <input type="text" class="form-control" id="sloc" name="sloc" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="repaired_qty" class="form-label">Repaired Quantity</label>
                                                                            <input type="number" class="form-control" id="repaired_qty" name="repaired_qty" step="0.01" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="repair_date" class="form-label">Repair Date</label>
                                                                            <input type="date" class="form-control" id="repair_date" name="repair_date" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="notes" class="form-label">Notes</label>
                                                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Alert Success -->
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

                                            <!-- Validation Errors -->
                                            @if (count($errors)>0)
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
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="repairPartsTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Part No</th>
                                                    <th>Description</th>
                                                    <th>SLOC</th>
                                                    <th>Repaired Quantity</th>
                                                    <th>Repair Date</th>
                                                    <th>Notes</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $item)
                                                <tr>
                                                    <td>{{ $item->part->material }}</td>
                                                    <td>{{ $item->part->material_description }}</td>
                                                    <td>{{ $item->sloc }}</td>
                                                    <td>{{ $item->repaired_qty }}</td>
                                                    <td>{{ $item->repair_date }}</td>
                                                    <td>{{ $item->notes }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Actions
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $item->id }}">Edit</button></li>
                                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $item->id }}">Delete</button></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Modal for Editing Repair Part -->
                                                <div class="modal fade" id="modal-edit-{{ $item->id }}" tabindex="-1" aria-labelledby="modal-edit-label-{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modal-edit-label-{{ $item->id }}">Edit Repair Part</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="repair-part-form-{{ $item->id }}" action="{{ url('/repair-parts/update/' . $item->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="part_id" class="form-label">Part</label>
                                                                                <select class="form-control" id="part_id" name="part_id" required>
                                                                                    <option value="{{ $item->part->id }}">{{ $item->part->material }} - {{ $item->part->material_description }}</option>
                                                                                    @foreach($parts as $part)
                                                                                        <option value="{{ $part->id }}">{{ $part->material }} - {{ $part->material_description }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="sloc" class="form-label">SLOC</label>
                                                                                <input type="text" class="form-control" id="sloc" name="sloc" value="{{ $item->sloc }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="repaired_qty" class="form-label">Repaired Quantity</label>
                                                                                <input type="number" class="form-control" id="repaired_qty" name="repaired_qty" value="{{ $item->repaired_qty }}" step="0.01" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="repair_date" class="form-label">Repair Date</label>
                                                                                <input type="date" class="form-control" id="repair_date" name="repair_date" value="{{ $item->repair_date }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="notes" class="form-label">Notes</label>
                                                                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $item->notes }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal for Deleting Repair Part -->
                                                <div class="modal fade" id="modal-delete-{{ $item->id }}" tabindex="-1" aria-labelledby="modal-delete-label-{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modal-delete-label-{{ $item->id }}">Delete Repair Part</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="delete-repair-part-form-{{ $item->id }}" action="{{ url('/repair-parts/delete/' . $item->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <p>Are you sure you want to delete this repair part?</p>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $("#repairPartsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    });
</script>
@endsection
