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
                            Master SAP Part
                        </h1>
                        <div class="page-header-subtitle">Manage SAP Master Part</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadMasterPart">
                            <i class="fas fa-file-excel"></i> Master Part Machine
                        </button>
                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                            <i class="fas fa-plus-square"></i> Add SAP Part
                        </button>
                        <!-- Delete SAP Part Button -->
                        <button type="button" class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-delete-part">
                            <i class="fas fa-trash-alt"></i> Delete SAP Part
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Modal for Upload -->
    <div class="modal fade" id="uploadMasterPart" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-label">Upload Master Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/mst/part/sap/upload') }}" method="POST" enctype="multipart/form-data">
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
                        <a href="{{url('/mst/part/sap/template')}}" class="btn btn-link">
                            Download Excel Format
                        </a>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Deleting Parts -->
    <div class="modal fade" id="modal-delete-part" tabindex="-1" aria-labelledby="modal-delete-part-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-delete-part-label">Delete Parts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="delete-part-form" action="{{ url('/mst/sap/part/delete') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="parts" class="form-label">Select Parts to Delete</label>
                            <select class="form-control chosen-select" id="parts" name="parts[]" multiple="multiple" required>
                                @foreach($parts as $part)
                                    <option value="{{ $part->id }}">{{ $part->material }} - {{ $part->material_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Selected Parts</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of Stock Part</h3>
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
                                                    <th>Material</th>
                                                    <th>Plnt</th>
                                                    <th>SLoc</th>
                                                    <th>Total Stock</th>
                                                    <th>Total Value</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
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

<!-- Include Chosen jQuery plugin -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

<script>
    $(document).ready(function() {
        // Fetch the current plant from the URL or set to null
        var plnt = '{{ request()->segment(4) ?? '' }}'; // Assuming plant is the 4th segment in your URL

        var table = $('#tableUser').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": plnt ? "{{ url('/mst/sap/part') }}/" + plnt : "{{ route('mst.sap.part') }}",
                "type": "GET"
            },
            "columns": [
                { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
                { "data": "material", "name": "material" },
                { "data": "plnt", "name": "plnt" },
                { "data": "sloc", "name": "sloc" },
                { "data": "total_stock", "name": "total_stock", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "total_value", "name": "total_value", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "encrypted_id", "name": "encrypted_id", "visible": false }, // Add hidden encrypted ID for row click
            ],
            "order": [[1, 'asc']],
            "dom": 'Blfrtip', // Enable buttons and length menu
            "buttons": [
                {
                    title: 'SAP Part Data Export',
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
                $(row).attr('onclick', 'window.location.href="{{ url("/mst/sap/part/info/") }}/'+data.encrypted_id+'"');
                $(row).css('cursor', 'pointer');
            }
        });

        // Initialize Chosen plugin for multi-select
        $('.chosen-select').chosen({
            width: '100%',
            no_results_text: 'No results matched'
        });
    });
</script>
@endsection
