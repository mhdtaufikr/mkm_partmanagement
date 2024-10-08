@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="far fa-chart-bar"></i></div>
                            KPI Daily Report
                        </h1>
                        <div class="page-header-subtitle">KPI Daily Report</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

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

                                <div class="card-body">
                                    <div class="form-group mb-4">
                                        <div class="row">
                                            <div class="col-sm-12 d-flex justify-content-end align-items-center">
                                                <!-- Legend section aligned to the right -->
                                                <div class="legend">
                                                    <strong>Legend:</strong>
                                                    <span style='font-size: 20px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span> Temporary |
                                                    <i class="fas fa-times" style='font-size: 20px; color: red;'></i> Not Good |
                                                    <i class="fas fa-check" style='font-size: 20px; color: green;'></i> OK
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="filter-month">Filter by Month:</label>
                                                <select id="filter-month" class="form-control">
                                                    <option value="">Select Month</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="filter-year">Filter by Year:</label>
                                                <select id="filter-year" class="form-control">
                                                    <option value="">Select Year</option>
                                                    @for ($i = date('Y'); $i >= 2000; $i--)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="filter-report">Filter by Report:</label>
                                                <select id="filter-report" class="form-control">
                                                    <option value="">Select Report</option>
                                                    @foreach($reports as $report)
                                                        <option value="{{ $report }}">{{ $report }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Machine</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>KPI</th>
                                                    <th>Total Balance</th>
                                                    <th>PIC</th>
                                                    <th>Problem</th>
                                                    <th>Cause</th>
                                                    <th>Action</th>
                                                    <th>Status</th>
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
</main>

<script>
$(document).ready(function() {
    var table = $("#tablehistory").DataTable({
        processing: true,
        serverSide: true,
        responsive: true, // Enable responsive mode
        autoWidth: false, // Prevent the table from stretching out
        ajax: {
            url: "{{ route('kpi.daily.data') }}",
            data: function (d) {
                d.month = $('#filter-month').val();
                d.year = $('#filter-year').val();
                d.report = $('#filter-report').val(); // Add report filter to the request
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id_machine', name: 'id_machine' },
            {
                data: "start_date",
                name: "start_date",
                render: function(data) {
                    if (data) {
                        var dateParts = data.split("-");
                        return dateParts[2] + "/" + dateParts[1] + "/" + dateParts[0];
                    }
                    return data;
                }
            },
            {
                data: "end_date",
                name: "end_date",
                render: function(data) {
                    if (data) {
                        var dateParts = data.split("-");
                        return dateParts[2] + "/" + dateParts[1] + "/" + dateParts[0];
                    }
                    return data;
                }
            },
            { data: 'kpi', name: 'kpi' },
            {
                data: 'total_balance',
                name: 'total_balance',
                render: function (data) {
                    var formattedBalance = (data % 1 === 0) ? parseInt(data) : parseFloat(data).toString().replace('.', ',');
                    return formattedBalance + ' Hour';
                }
            },
            { data: 'pic', name: 'pic' },
            { data: 'problem', name: 'problem' },
            { data: 'cause', name: 'cause' },
            { data: 'action', name: 'action' },
            {
                data: 'latest_status',
                name: 'latest_status',
                render: function(data) {
                    var icon = ''; // Placeholder for icon

                    switch (data) {
                        case 'Temporary':
                            icon = "<span style='font-size: 35px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span>";
                            break;
                        case 'Not Good':
                            icon = "<i class='fas fa-times' style='font-size: 35px; color: red;'></i>";
                            break;
                        case 'OK':
                            icon = "<i class='fas fa-check' style='font-size: 35px; color: green;'></i>";
                            break;
                        default:
                            icon = "<span>Unknown Status</span>";
                    }
                    return icon;
                }
            }
        ]
    });

    // Handle month, year, and report filter changes
    $('#filter-month, #filter-year, #filter-report').change(function() {
        table.draw(); // Redraw the table with the selected filters
    });
});

</script>
@endsection
