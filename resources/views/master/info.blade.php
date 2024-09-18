@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="list"></i></div>
                            Part Information
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Part Information with SAP and Repair Quantities</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="partInfoTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Description</th>
                                <th>Plant</th>
                                <th>SLoc</th>
                                <th>SAP Quantity</th>
                                <th>Repair Quantity</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- The table body will be dynamically populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- For Datatables -->
<script>
  $(document).ready(function() {
    $('#partInfoTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('part.info') }}", // Ensure this is correct
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'material', name: 'material' },
            { data: 'material_description', name: 'material_description' },
            { data: 'plnt', name: 'plnt' },
            { data: 'sloc', name: 'sloc' },
            { data: 'total_stock', name: 'total_stock', "render": $.fn.dataTable.render.number(',', '.', 0) },
            { data: 'repair_qty', name: 'repair_qty', "render": $.fn.dataTable.render.number(',', '.', 0) },
            { data: 'total_qty', name: 'total_qty', "render": $.fn.dataTable.render.number(',', '.', 0) }
        ],
        responsive: true,
        lengthChange: false,
        autoWidth: false
    });
});

</script>

@endsection
