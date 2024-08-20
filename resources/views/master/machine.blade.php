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
                            <i class="fas fa-file-excel"></i> Master Part Machine
                        </button>

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
                                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add-machine">
                                            <i class="fas fa-plus-square"></i> Add Machine
                                        </button>

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
        var table = $('#tableUser').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('mst.machine.part') }}",
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
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('onclick', 'window.location.href="{{ url("/mst/machine/detail/") }}/'+data.encrypted_id +'";');
                $(row).css('cursor', 'pointer');
            }
        });
    });
</script>


@endsection
