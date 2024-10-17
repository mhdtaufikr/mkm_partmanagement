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
                        @if(auth()->user()->role == 'IT' || auth()->user()->role == 'Admin')
                            <!-- Button to trigger delete modal -->
                            <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt"></i> Delete Daily Report
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('history.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <!-- OP No Dropdown -->
                    <div class="mb-3">
                        <label for="op_no" class="form-label">Select OP No. - Line</label>
                        <select name="op_no" id="op_no" class="form-control chosen-select" required>
                            <option value="">Select OP No.</option>
                        </select>
                    </div>

                    <!-- Date Dropdown (will be dynamically populated based on OP No.) -->
                    <div class="mb-3">
                        <label for="date" class="form-label">Select Date</label>
                        <select name="date" id="date" class="form-control chosen-select" required>
                            <option value="">Select Date</option>
                            <!-- Dates will be dynamically loaded -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize Chosen and Handle Dynamic Fetching for OP No and Date -->
<script>
    $(document).ready(function() {
        // Initialize Chosen plugin
        $('.chosen-select').chosen({
            width: '100%',
            no_results_text: 'No results found'
        });

        // Fetch OP No. with Line dynamically via AJAX (without DataTables)
        $.ajax({
            url: "{{ route('history.getOpNoWithLine') }}", // Route to fetch OP No. with Line
            method: 'GET',
            success: function(response) {
                var opNoDropdown = $('#op_no');
                opNoDropdown.empty().append('<option value="">Select OP No.</option>');

                $.each(response, function(index, item) {
                    opNoDropdown.append('<option value="' + item.op_no + '">' + item.op_no + ' - ' + item.line + '</option>');
                });

                opNoDropdown.trigger('chosen:updated');
            },
            error: function() {
                alert('Failed to fetch OP No.');
            }
        });

        // Fetch dates dynamically when an OP No is selected
        $('#op_no').on('change', function() {
            var op_no = $(this).val();

            // Clear current date options
            $('#date').empty().append('<option value="">Select Date</option>');

            if (op_no) {
    $.ajax({
        url: "{{ route('history.getDatesByOpNo') }}",
        method: 'GET',
        data: { op_no: op_no },
        success: function(response) {
            // Populate date dropdown with the response data
            $.each(response, function(index, date) {
                let dateObj = new Date(date.date);
                let day = String(dateObj.getDate()).padStart(2, '0'); // Ensure two digits for the day
                let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                let year = dateObj.getFullYear();
                let formattedDate = `${day}/${month}/${year}`; // Format as dd/mm/yyyy

                $('#date').append('<option value="' + date.date + '">' + formattedDate + '</option>');
            });

            // Refresh the Chosen dropdown
            $('#date').trigger('chosen:updated');
        },
        error: function() {
            alert('Failed to fetch dates.');
        }
    });
}


            // Refresh the Chosen dropdown
            $('#date').trigger('chosen:updated');
        });
    });
</script>
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
                                        <div class="mb-3 col-sm-6">
                                            <a href="{{ route('form') }}" class="btn btn-dark btn-sm mb-2">
                                                <i class="fas fa-plus-square"></i> Input Daily Report
                                            </a>
                                            <button class="btn btn-info btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#openReportModal">
                                                <i class="fas fa-plus-square"></i> Update Daily Report
                                            </button>
                                        </div>
                                        <div class="col-sm-6 d-flex justify-content-end align-items-center">
                                            <!-- Legend section aligned to the right -->
                                            <div class="legend">
                                                <strong>Legend:</strong>
                                                <i class="fas fa-check" style='font-size: 20px; color: green;'></i> OK |
                                                <span style='font-size: 20px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span> Temporary |
                                                <i class="fas fa-times" style='font-size: 20px; color: red;'></i> Not Good

                                            </div>
                                        </div>
                                    </div>


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

                                    <!-- DataTable -->
                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Status</th> <!-- Show the latest status here -->
                                                    <th>OP No.</th>
                                                    <th>Plant</th>
                                                    <th>Line</th>
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

<style>
    table td {
        white-space: nowrap;  /* Prevent text wrapping */
        overflow: hidden;     /* Hide overflow text */
        text-overflow: ellipsis; /* Show ellipsis for overflow */
    }
</style>


<script>
   $(document).ready(function() {
    var table = $("#tablehistory").DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": false,
        "autoWidth": true,
        "ajax": "{{ route('history') }}",
        "columns": [
            {
                data: null, // Set data to null so we can customize what to display
                name: 'DT_RowIndex_flag',
                orderable: false, // Disable sorting
                searchable: false, // Disable searching
                render: function(data, type, row, meta) {
                    // Combine the row index and the flag column
                    return `${meta.row + 1} ${row.flag}`;
                }
            },
            {
                "data": "status",
                "name": "status",
                "render": function(data, type, row) {
                    let icon = '';
                    switch (data) {
                        case 'Temporary':
                            icon = "<span style='font-size: 30px; color: #FFDF00; font-weight: bold; text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, -1px 1px 0 #000, 1px -1px 0 #000;'>&#9651;</span>"; // Yellow triangle with bold effect
                            break;
                        case 'Not Good':
                            icon = "<i class='fas fa-times' style='font-size: 30px; color: red;'></i>"; // Red times icon
                            break;
                        case 'OK':
                            icon = "<i class='fas fa-check' style='font-size: 30px; color: green;'></i>"; // Green check icon
                            break;
                        default:
                            icon = "<i class='fas fa-question' style='font-size: 30px; color: gray;'></i>"; // Gray question mark icon for unknown status
                    }
                    return '<td class="text-center">' + icon + '</td>';
                }
            },
            {
                "data": "machine.op_no",
                "name": "machine.op_no",
                "render": function(data, type, row) {
                    return `<span style="white-space: nowrap;">${data}</span>`;
                }
            },
            { "data": "machine.plant", "name": "machine.plant" }, // Plant
            { "data": "machine.line", "name": "machine.line" },   // Line column
            {
                "data": "date",
                "name": "date",
                "render": function(data, type, row) {
                    if (data) {
                        var dateParts = data.split("-");
                        return dateParts[2] + "/" + dateParts[1] + "/" + dateParts[0];
                    }
                    return data;
                }
            },
            { "data": "shift", "name": "shift" },
            { "data": "shop", "name": "shop" },
            {
                "data": "problem",
                "name": "problem",
                "render": function(data, type, row) {
                   if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
                }
            },
            {
                "data": "cause",
                "name": "cause",
                "render": function(data, type, row) {
                   if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
                }
            },
            { "data": "start_time", "name": "start_time" },
            { "data": "finish_time", "name": "finish_time" },
            {
                "data": "balance",
                "name": "balance",
                "render": function(data, type, row) {
                    return parseFloat(data).toString() + ' Hour';
                }
            },
            { "data": "pic", "name": "pic" },
            {
                "data": "remarks",
                "name": "remarks",
                "render": function(data, type, row) {
                   if (data && data.length) { // Check if data exists and has a length property
            return data.length > 8 ? data.substring(0, 8) + '...' : data;
        } else {
            return ''; // Return an empty string if data is null or undefined
        }
                }
            }
        ]
    });

    // Row click event to load modal with details
    $('#tablehistory tbody').on('click', 'tr', function() {
        var data = table.row(this).data(); // Get data for the clicked row
        var id = data.id;

        // Fetch detail using AJAX and load it into the modal
        $.ajax({
            url: "{{ url('/history/detail') }}/" + id,
            method: 'GET',
            success: function(response) {
                $('#modal-body-content').html(response); // Load content into modal
                $('#modal-detail').modal('show'); // Show the modal
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
<script>
    $(document).ready(function() {
        var table = $("#openReportsTable").DataTable({
            "responsive": true,  // Enable responsive mode
            "lengthChange": false,  // Disable length change dropdown
            "autoWidth": false,  // Disable auto width to prevent table from stretching out
            "paging": true,  // Enable pagination
            "searching": true,  // Enable search functionality
            "info": false  // Disable table info
        });
    });
</script>


@endsection
