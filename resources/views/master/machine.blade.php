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

                        <!-- Modal HTML omitted for brevity -->
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

                                        <!-- Alert Success and Validation Errors -->

                                    </div>

                                    <div class="table-responsive">
                                        <table id="tableUser" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Line</th>
                                                    <th>OP. No</th>
                                                    <th>Process</th>
                                                    <th>Maker</th>
                                                    <th>Mfg. Date</th>
                                                    <th>Action</th>
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

<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $('#tableUser').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('mst.machine.part') }}",  // Your server-side route here
            "columns": [
                { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
                { "data": "line", "name": "line" },
                { "data": "op_no", "name": "op_no" },
                { "data": "process", "name": "process" },
                { "data": "maker", "name": "maker" },
                { "data": "mfg_date", "name": "mfg_date" },
                { "data": "action", "name": "action", "orderable": false, "searchable": false }
            ],
            "order": [[1, 'asc'], [2, 'asc']]
        });
    });
</script>
@endsection
