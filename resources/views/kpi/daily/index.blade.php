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
                                    <form id="update-form" action="{{ route('kpi.daily.data.update') }}" method="POST">
                                        @csrf <!-- CSRF protection for POST request -->
                                        <div class="form-group mb-4">
                                            <div class="row">
                                                <div class="col-sm-12 d-flex justify-content-end align-items-center">
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
                                                        <th>Date</th>
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

                                        <!-- Submit Button for Bulk Update -->
                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-primary">Submit Changes</button>
                                        </div>
                                    </form>
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
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('kpi.daily.data') }}",
                data: function (d) {
                    d.month = $('#filter-month').val();
                    d.year = $('#filter-year').val();
                    d.report = $('#filter-report').val();
                }
            },
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'machine', name: 'machine' },  // Machine column with op_no and machine_name
                {
                    data: 'date',
                    name: 'date',
                    render: function(data, type, row, meta) {
                        return `
                            <input type="hidden" name="rows[${meta.row}][id]" value="${row.id}">
                            <input type="date" name="rows[${meta.row}][date]" value="${data}" class="form-control">
                        `;
                    }
                },
                {
                    data: 'kpi',
                    name: 'kpi',
                    render: function (data, type, row, meta) {
                        return `<input type="checkbox" name="rows[${meta.row}][kpi]" value="A" ${data == 'A' ? 'checked' : ''}> KPI`;
                    }
                },
                {
                    data: 'balance',
                    name: 'balance',
                    render: function (data, type, row, meta) {
                        return `<input type="text" name="rows[${meta.row}][balance]" value="${data}" class="form-control">`;
                    }
                },
                { data: 'pic', name: 'pic' },
                { data: 'problem', name: 'problem' },
                { data: 'cause', name: 'cause' },
                { data: 'action', name: 'action' },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        var icon = '';
                        switch (data) {
                            case 'Temporary':
                                icon = "<span style='font-size: 35px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000;'>&#9651;</span>";
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

        // Handle filter changes
        $('#filter-month, #filter-year, #filter-report').change(function() {
            table.draw();
        });
    });
</script>

@endsection
