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
                            Daily Report
                        </h1>
                        <div class="page-header-subtitle">Manage Daily Report</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#dailyReport">
                            <i class="fas fa-file-excel"></i> Upload Daily Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
<!-- Modal for Upload -->
<div class="modal fade" id="dailyReport" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-label">Upload Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/history/upload') }}" method="POST" enctype="multipart/form-data">
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
                    <a href="{{ url('/history/template') }}" class="btn btn-link">Download Excel Format</a>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Daily Reports</h3>
                                </div>

                                @include('partials.alert')

                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <a href="{{ route('form') }}" class="btn btn-dark btn-sm mb-2">
                                                <i class="fas fa-plus-square"></i> Input Daily Report
                                            </a>
                                            <button class="btn btn-info btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#openReportModal">
                                                <i class="fas fa-plus-square"></i> Update Daily Report
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Modal for selecting the open daily report -->
                                    <div class="modal fade" id="openReportModal" tabindex="-1" aria-labelledby="openReportModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="openReportModalLabel">Select Open Daily Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-bordered" id="openReportsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Shift</th>
                                                                <th>Plant</th>
                                                                <th>Line</th>
                                                                <th>OP No.</th>
                                                                <th>Shop</th>
                                                                <th>Problem</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Loop through open reports and display them -->
                                                            @foreach ($openReports as $report)
                                                                <tr>
                                                                    <td>{{ $report->date }}</td>
                                                                    <td>{{ $report->shift }}</td>
                                                                    <td>{{ $report->machine->plant ?? 'N/A' }}</td> <!-- Display the plant -->
                                                                    <td>{{ $report->machine->line ?? 'N/A' }}</td>  <!-- Display the line -->
                                                                    <td>{{ $report->machine->op_no ?? 'N/A' }}</td> <!-- Display the OP No -->
                                                                    <td>{{ $report->shop }}</td>
                                                                    <td>{{ $report->problem }}</td>
                                                                    <td>
                                                                        <!-- Form for selecting the report -->
                                                                        <form action="{{ url('form/update/' . encrypt($report->id)) }}" method="GET">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-primary">Select</button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- DataTable -->
                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>OP No.</th>
                                                    <th>Date</th>
                                                    <th>Shift</th>
                                                    <th>Shop</th>
                                                    <th>Problem</th>
                                                    <th>Cause</th>
                                                    <th>Start Time</th>
                                                    <th>Finish Time</th>
                                                    <th>Balance</th>
                                                    <th>PIC</th>
                                                    <th>Remarks</th>
                                                    <th>Status</th> <!-- Show the latest status here -->
                                                    <th>Flag</th> <!-- Show flag if the record has a child or is a child -->
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTables will populate the rows here -->
                                            </tbody>
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

    <!-- Modal Structure -->
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
            "ajax": "{{ route('history') }}",
            "columns": [
                { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
                { "data": "machine.op_no", "name": "machine.op_no" },
                { "data": "date", "name": "date" },
                { "data": "shift", "name": "shift" },
                { "data": "shop", "name": "shop" },
                { "data": "problem", "name": "problem" },
                { "data": "cause", "name": "cause" },
                { "data": "start_time", "name": "start_time" },
                { "data": "finish_time", "name": "finish_time" },
                { "data": "balance", "name": "balance" },
                { "data": "pic", "name": "pic" },
                { "data": "remarks", "name": "remarks" },
                {
                    "data": "status",
                    "name": "status",
                    "render": function(data, type, row) {
                        let statusClass = '';
                        switch (data) {
                            case 'Close': statusClass = 'btn-success'; break;
                            case 'Open': statusClass = 'btn-danger'; break;
                            case 'Delay': statusClass = 'btn-warning'; break;
                            case 'Ongoing': statusClass = 'btn-info'; break;
                            default: statusClass = 'btn-primary';
                        }
                        return '<button class="btn ' + statusClass + ' btn-sm">' + data + '</button>';
                    }
                },
                {
                    // Add a flag icon if the record has a parent (indicating it was reopened)
                    "data": "parent_id",
                    "name": "parent_id",
                    "render": function(data, type, row) {
                        return data ? '<i class="fas fa-flag" style="color: rgba(0, 103, 127, 1)"></i>' : '';
                    }
                },
                {
                    "data": "id",
                    "name": "id",
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, row) {
                        return `<button class="btn btn-sm btn-primary btn-detail" data-id="${data}" data-bs-toggle="modal" data-bs-target="#modal-detail">Detail</button>`;
                    }
                }
            ],
            "dom": 'Blfrtip', // Enable buttons and length menu
            "buttons": [
                {
                    title: 'History Data Export',
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
        });

        // Load modal content dynamically when the detail button is clicked
        $('#tablehistory').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('/history/detail') }}/" + id,
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

@endsection
