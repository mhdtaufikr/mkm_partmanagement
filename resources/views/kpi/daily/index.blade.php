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
                                        <label for="filter-month">Filter by Month:</label>
                                        <select id="filter-month" class="form-control" style="width: 200px;">
                                            <option value="">Select Month</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Machine</th>  <!-- Change header to "Machine" -->
                                                    <th>Report</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>KPI</th>
                                                    <th>Category</th>
                                                    <th>Total Balance</th>
                                                    <th>PIC</th>
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
        ajax: {
            url: "{{ route('kpi.daily.data') }}",
            data: function (d) {
                d.month = $('#filter-month').val(); // Pass the selected month to the server
            }
        },
        // Extend the lengthMenu to include "All" option
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id_machine', name: 'id_machine' },
            { data: 'report', name: 'report' },
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
            {
                data: 'kpi',
                name: 'kpi',
                render: function (data, type, row) {
                    // If KPI value is 'A', show the icon, otherwise show the raw value
                    if (data == 'A') {
                        return '<i class="far fa-check-square" style="color: #109e12;"></i>';
                    } else {
                        return data;
                    }
                }
            },
            { data: 'category', name: 'category' },
            {
                data: 'total_balance',
                name: 'total_balance',
                render: function (data) {
                    var formattedBalance = (data % 1 === 0) ? parseInt(data) : parseFloat(data).toString().replace('.', ',');
                    return formattedBalance + ' Hour';
                }
            },
            { data: 'pic', name: 'pic' },
            {
                data: 'latest_status',
                name: 'latest_status',
                render: function(data) {
                    var statusClass;
                    switch (data) {
                        case 'OK': statusClass = 'btn-success'; break;
                        case 'Not Good': statusClass = 'btn-danger'; break;
                        case 'Temporary': statusClass = 'btn-warning'; break;
                        default: statusClass = 'btn-primary';
                    }
                    return '<button class="btn ' + statusClass + '">' + data + '</button>';
                }
            }
        ]
    });

    // Handle month filter change
    $('#filter-month').change(function() {
        table.draw(); // Redraw the table with the selected month filter
    });
});


</script>
@endsection
