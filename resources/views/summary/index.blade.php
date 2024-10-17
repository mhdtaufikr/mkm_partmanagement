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
                                   {{--  <a href="{{ route('form') }}" class="btn btn-dark btn-sm me-2">
                                        <i class="fas fa-plus-square"></i> Input Daily Report
                                    </a> --}}
                                  <!-- Button for Update Daily Report -->
                                <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#openReportModal">
                                    <i class="fas fa-plus-square"></i> Update Daily Report
                                </button>

                                <!-- Button for Update Preventive Maintenance -->
                                <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#openPMReportModal">
                                    <i class="fas fa-tools"></i> Update Preventive Maintenance
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
                                            <th>Flag</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>OP No.</th>
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
   <!-- Modal for selecting the open daily report -->
   <div class="modal fade" id="openReportModal" tabindex="-1" aria-labelledby="openReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-x">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="openReportModalLabel">Select Open Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="openReportsTable">
                    <thead>
                        <tr>
                            <th>Status</th>
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
                                <td class="text-center">
                                    @if($report->status == 'Temporary')
                                    <span style='font-size:50px;color:#FFDF00;'>&#9651;</span> <!-- CSS-based triangle -->
                                    @elseif($report->status == 'Not Good')
                                        <i class="fas fa-times" style='font-size:50px;color: red;'></i> <!-- Times icon -->
                                    @elseif($report->status == 'OK')
                                        <i class="fas fa-check" style="color: green;"></i> <!-- Check icon -->
                                    @else
                                        <i class="fas fa-question" style="color: gray;"></i> <!-- Default icon for unknown status -->
                                    @endif
                                </td>
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
<!-- Modal for selecting the open preventive maintenance report -->
<div class="modal fade" id="openPMReportModal" tabindex="-1" aria-labelledby="openPMReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-x">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="openPMReportModalLabel">Select Open Preventive Maintenance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="openPMReportsTable">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Planning Date</th>
                            <th>Actual Date</th>
                            <th>OP No.</th>
                            <th>Machine</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through open PM reports and display them -->
                        @foreach ($openPMReports as $report)
                            <tr>
                                <td class="text-center">
                                    @if($report->pm_status == 'Temporary')
                                        <span style='font-size:50px;color:#FFDF00;'>&#9651;</span> <!-- CSS-based triangle -->
                                    @elseif($report->pm_status == 'Not Good')
                                        <i class="fas fa-times" style='font-size:50px;color: red;'></i> <!-- Times icon -->
                                    @elseif($report->pm_status == 'OK')
                                        <i class="fas fa-check" style="color: green;"></i> <!-- Check icon -->
                                    @else
                                        <i class="fas fa-question" style="color: gray;"></i> <!-- Default icon for unknown status -->
                                    @endif
                                </td>
                                <td>{{ $report->planning_date }}</td>
                                <td>{{ $report->actual_date ?? '--/--/----' }}</td> <!-- Show actual date if exists -->
                                <td>{{ $report->preventiveMaintenance->machine->op_no ?? 'N/A' }}</td>
                                <td>{{ $report->preventiveMaintenance->machine->machine_name ?? 'N/A' }}</td>
                                <td>
                                    <!-- Form for selecting the report -->
                                    <form action="{{ url('checksheet/change-status')}}" method="POST">
                                        @csrf
                                        <input hidden name="id_pm" value="{{ $report->id }}">
                                        <input hidden name="checksheet_id" value="{{ $report->preventive_maintenances_id }}">
                                        <input value="{{ date('Y-m-d') }}" type="date" class="form-control" id="date" name="date" hidden >
                                        <input value="Day" type="text" class="form-control" id="shift" name="shift" hidden >
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

<script>
$(document).ready(function() {
    var table = $("#tablehistory").DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "autoWidth": false,
        "ajax": {
            "url": "{{ route('summary.data') }}",
            "data": function(d) {
                d.line = $('#lineFilter').val(); // Add the line filter to the request data
            }
        },
        "columns": [
            { "data": "DT_RowIndex", "name": "DT_RowIndex", "orderable": false, "searchable": false },
            { "data": "flag", "name": "flag", "orderable": false, "searchable": false },
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
            { "data": "type", "name": "type" },
            { "data": "date", "name": "date" },
            { "data": "op_no", "name": "op_no" },
            { "data": "line", "name": "line" },
            { "data": "shift", "name": "shift" },
            { "data": "shop", "name": "shop" },
            { "data": "problem", "name": "problem", "render": function(data, type, row) {
               if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
            }},
            { "data": "cause", "name": "cause", "render": function(data, type, row) {
               if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
            }},
            { "data": "start_time", "name": "start_time" },
            { "data": "finish_time", "name": "finish_time" },
            { "data": "balance", "name": "balance" },
            { "data": "pic", "name": "pic" },
            { "data": "remarks", "name": "remarks", "render": function(data, type, row) {
               if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
            }},

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
