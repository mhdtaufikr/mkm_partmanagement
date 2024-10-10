@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                            Machine Summary Report
                        </h1>
                        <div class="page-header-subtitle">Manage Daily Reports and Preventive Maintenance</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-file-excel"></i> Upload Daily Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <!-- Consolidated Card Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Machine History Summary Report</h3>
                        </div>

                        @include('partials.alert')

                        <div class="card-body">
                            <!-- Filter by Line -->
                            <div class="row">
                                <div class="col-sm-12 d-flex align-items-center mb-3">
                                    <a href="{{ route('form') }}" class="btn btn-dark btn-sm me-2">
                                        <i class="fas fa-plus-square"></i> Input Daily Report
                                    </a>
                                    <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#openReportModal">
                                        <i class="fas fa-plus-square"></i> Update Daily Report
                                    </button>

                                  {{--   <div class="form-group mb-0 me-2" style="width: 200px;">
                                        <select id="lineFilter" class="form-control form-control-sm">
                                            <option value="">Filter by Line</option>
                                            @foreach ($lines as $line)
                                                <option value="{{ $line->line }}">{{ $line->line }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                    <!-- Legend aligned to the right -->
                                    <div class="ms-auto">
                                        <strong>Legend:</strong>
                                        <span style='font-size: 20px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span> Temporary |
                                        <i class="fas fa-times" style='font-size: 20px; color: red;'></i> Not Good |
                                        <i class="fas fa-check" style='font-size: 20px; color: green;'></i> OK
                                    </div>
                                </div>
                            </div>

                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table id="tablehistory" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>OP No. (Machine Name)</th>
                                            <th>Line</th>
                                            <th>Shift</th>
                                            <th>Shop</th>
                                            <th>Problem</th>
                                            <th>Cause</th>
                                            <th>Start Time</th>
                                            <th>Finish Time</th>
                                            <th>Balance</th>
                                            <th>PIC</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Flag</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <!-- DataTables will populate the rows here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal Structure for Historical Problem Details -->
    <div class="modal fade" id="modal-detail" tabindex="-1" aria-labelledby="modal-detail-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-detail-label">Detail of Historical Problem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <!-- Dynamic content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    var table = $("#tablehistory").DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "ajax": {
            "url": "{{ route('summary.data') }}",
            "data": function(d) {
                d.line = $('#lineFilter').val(); // Add the line filter to the request data
            }
        },
        "columns": [
            { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
            { "data": "type", "name": "type" },
            { "data": "date", "name": "date" },
            { "data": "op_no", "name": "op_no" },
            { "data": "line", "name": "line" },
            { "data": "shift", "name": "shift" },
            { "data": "shop", "name": "shop" },
            { "data": "problem", "name": "problem", "render": function(data, type, row) {
                return data.length > 8 ? data.substring(0, 8) + '...' : data;
            }},
            { "data": "cause", "name": "cause", "render": function(data, type, row) {
                return data.length > 8 ? data.substring(0, 8) + '...' : data;
            }},
            { "data": "start_time", "name": "start_time" },
            { "data": "finish_time", "name": "finish_time" },
            { "data": "balance", "name": "balance" },
            { "data": "pic", "name": "pic" },
            { "data": "remarks", "name": "remarks", "render": function(data, type, row) {
                return data.length > 8 ? data.substring(0, 8) + '...' : data;
            }},
            { "data": "status", "name": "status", "render": function(data, type, row) {
                let icon = '';
                switch (data) {
                    case 'Temporary':
                        icon = "<span style='font-size: 30px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span>";
                        break;
                    case 'Not Good':
                        icon = "<i class='fas fa-times' style='font-size: 30px; color: red;'></i>";
                        break;
                    case 'OK':
                        icon = "<i class='fas fa-check' style='font-size: 30px; color: green;'></i>";
                        break;
                    default:
                        icon = "<i class='fas fa-question' style='font-size: 30px; color: gray;'></i>";
                }
                return icon;
            }},
            { "data": "flag", "name": "flag", "orderable": false, "searchable": false },
            { "data": "action", "name": "action", "orderable": false, "searchable": false }
        ],
        "drawCallback": function(settings) {
            var api = this.api();
            var startIndex = api.page.info().start; // Get the start index for the current page
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = startIndex + i + 1; // Update the numbering
            });
        }
    });

    // Redraw the table when the line filter changes
    $('#lineFilter').change(function() {
        table.draw();
    });

    // Load modal content dynamically when the detail button is clicked
    $('#tablehistory').on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ url('/summary/detail') }}/" + id,
            method: 'GET',
            success: function(data) {
                $('#modal-body-content').html(data);
                $('#modal-detail').modal('show');
            },
            error: function(xhr, status, error) {
                alert('An error occurred while fetching details: ' + error);
            }
        });
    });
});

</script>



<style>
    .modal-lg-x {
        max-width: 90%;
    }
    .modal-lg {
        max-width: 70%;
    }
</style>

@endsection
