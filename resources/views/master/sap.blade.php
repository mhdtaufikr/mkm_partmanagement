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
                    </div>
                </div>
            </div>
        </div>
    </header>
       <!-- Modal -->
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
                                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                                            <i class="fas fa-plus-square"></i> Add SAP Part
                                        </button>

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
                                                    <th>Beginning Qty</th>
                                                    <th>Beginning Value</th>
                                                    <th>Received Qty</th>
                                                    <th>Received Value</th>
                                                    <th>Consumed Qty</th>
                                                    <th>Consumed Value</th>
                                                    <th>Total Stock</th>
                                                    <th>Total Value</th>
                                                    <th>Action</th>
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
                { "data": "begining_qty", "name": "begining_qty", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "begining_value", "name": "begining_value", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "received_qty", "name": "received_qty", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "received_value", "name": "received_value", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "consumed_qty", "name": "consumed_qty", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "consumed_value", "name": "consumed_value", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "total_stock", "name": "total_stock", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "total_value", "name": "total_value", "render": $.fn.dataTable.render.number(',', '.', 0) },
                { "data": "action", "name": "action", "orderable": false, "searchable": false },
            ],
            "order": [[1, 'asc']]
        });
    });
</script>


@endsection
