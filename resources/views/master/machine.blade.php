@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-database"></i></div>
                            Master Machine
                        </h1>
                        <div class="page-header-subtitle">Manage Master Machine</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadMasterMachine">
                            <i class="fas fa-file-excel"></i> Master Machine
                        </button>
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadMasterPart">
                            <i class="fas fa-file-excel"></i> Master Part List
                        </button>
                        <br>
                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add-machine">
                            <i class="fas fa-plus-square"></i> Add Machine
                        </button>
                       <!-- Delete Machine Button -->
<button type="button" class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-delete-machine">
    <i class="fas fa-trash-alt"></i> Delete Machine
</button>

<!-- Modal for Deleting Machines -->
<div class="modal fade" id="modal-delete-machine" tabindex="-1" aria-labelledby="modal-delete-machine-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-machine-label">Delete Machine(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-machine-form" action="{{ url('/mst/machine/delete') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="machines" class="form-label">Select Machines to Delete</label>
                        <select class="form-select chosen-select" id="machines" name="machines[]" multiple required>
                            <option value="" disabled>Select Machine(s)</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->op_no }} - {{ $machine->machine_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>


                        <!-- Modal for Adding New Machine -->
                        <div class="modal fade" id="modal-add-machine" tabindex="-1" aria-labelledby="modal-add-machine-label" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal-add-machine-label">Add New Machine</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="machine-form" action="{{ url('/mst/machine/add') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label style="color: black" for="plant" class="form-label">Plant</label>
                                                        <input type="text" class="form-control" id="plant" name="plant" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="line" class="form-label">Line</label>
                                                        <input type="text" class="form-control" id="line" name="line" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="op_no" class="form-label">OP No</label>
                                                        <input type="text" class="form-control" id="op_no" name="op_no" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="machine_name" class="form-label">Machine Name</label>
                                                        <input type="text" class="form-control" id="machine_name" name="machine_name" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="process" class="form-label">Process</label>
                                                        <textarea class="form-control" id="process" name="process" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="maker" class="form-label">Maker</label>
                                                        <input type="text" class="form-control" id="maker" name="maker" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="model" class="form-label">Model</label>
                                                        <input type="text" class="form-control" id="model" name="model" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label style="color: black" for="serial_number" class="form-label">Serial Number</label>
                                                        <input type="text" class="form-control" id="serial_number" name="serial_number" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="date" class="form-label">Date</label>
                                                        <input type="number" class="form-control" id="date" name="date" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="control_nc" class="form-label">Control NC</label>
                                                        <input type="text" class="form-control" id="control_nc" name="control_nc">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="control_plc" class="form-label">Control PLC</label>
                                                        <input type="text" class="form-control" id="control_plc" name="control_plc">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="color: black" for="control_servo" class="form-label">Control Servo</label>
                                                        <input type="text" class="form-control" id="control_servo" name="control_servo">
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

                         <!-- Modal -->
                         <div class="modal fade" id="uploadMasterMachine" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal-add-label">Upload Master Machine</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('/mst/machine/upload') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <input type="file" class="form-control" id="csvFile" name="excel-file" accept=".csv, .xlsx">
                                                <p class="text-danger">*file must be .xlsx or .csv</p>
                                            </div>
                                            @error('excel-file')
                                                <div class="alert alert-danger" role="alert">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{url('/mst/machine/template')}}" class="btn btn-link">
                                                Download Excel Format
                                            </a>
                                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                         <!-- Modal -->
                         <div class="modal fade" id="uploadMasterPart" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal-add-label">Upload Master Part</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('/mst/part/upload') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <input type="file" class="form-control" id="csvFile" name="excel-file" accept=".csv, .xlsx">
                                                <p class="text-danger">*file must be .xlsx or .csv</p>
                                            </div>
                                            @error('excel-file')
                                                <div class="alert alert-danger" role="alert">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{url('/mst/part/template')}}" class="btn btn-link">
                                                Download Excel Format
                                            </a>
                                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of Machine</h3>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-sm-12">
                                        @include('partials.alert')
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tableUser" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Plant</th>
                                                    <th>Line</th>
                                                    <th>OP. No</th>
                                                    <th>Machine Name</th>
                                                    <th>Process</th>
                                                    <th>Maker</th>
                                                    <th>Mfg. Date</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>

</main>

<script>
    $(document).ready(function() {
        // Initialize Chosen plugin for searchable dropdown
        $('.chosen-select').chosen({
            width: '100%', // Make sure the dropdown is full width
            no_results_text: "No machines found", // Text to display when no results are found
            placeholder_text_multiple: "Select Machine(s)" // Placeholder text
        });

        // Additional setup for DataTables, if required
        var location = '{{ request()->segment(4) ?? '' }}'; // Assuming location is the 4th segment in your URL

        var table = $('#tableUser').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": location ? "{{ url('/mst/machine/part') }}/" + location : "{{ route('mst.machine.part') }}",
                "type": "GET"
            },
            "columns": [
                { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
                { "data": "plant", "name": "plant" },
                { "data": "line", "name": "line" },
                { "data": "op_no", "name": "op_no" },
                { "data": "machine_name", "name": "machine_name" },
                { "data": "process", "name": "process" },
                { "data": "maker", "name": "maker" },
                { "data": "mfg_date", "name": "mfg_date" }
            ],
            "order": [[1, 'asc'], [2, 'asc']],
            "dom": 'Blfrtip', // This enables the export button and length menu
            "buttons": [
                {
                    title: 'Machine Data Export',
                    text: '<i class="fas fa-file-excel"></i> Export to Excel',
                    extend: 'excel',
                    className: 'btn btn-success btn-sm mb-2',
                    exportOptions: {
                        columns: ':visible', // Export only visible columns
                        modifier: {
                            search: 'applied', // Export only filtered data
                            order: 'applied', // Export data in current order
                            page: 'all' // Export all pages of data
                        }
                    }
                }
            ],
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "pageLength": 10, // Set the default number of rows to display
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('onclick', 'window.location.href="{{ url("/mst/machine/detail/") }}/'+data.encrypted_id +'";');
                $(row).css('cursor', 'pointer');
            }
        });
    });
</script>


@endsection
