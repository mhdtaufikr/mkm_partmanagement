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
                    {{-- <div class="col-12 col-xl-auto mt-4">Optional page header content</div> --}}
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
                                    <h3 class="card-title">List of Daily Report</h3>
                                </div>

                                @include('partials.alert')

                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                                                <i class="fas fa-plus-square"></i>  Input Daily Report
                                            </button>
                                        </div>
                                    </div>
<!-- Modal for Adding Daily Report -->
<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-label">Add Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="historical-problem-form" action="{{ url('/history/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <label for="line" class="form-label">Line</label>
                                <select class="form-control" id="line" name="line" required>
                                    <option value="">-- Select Line --</option>
                                    @foreach($lines as $line)
                                        <option value="{{ $line->line }}">{{ $line->line }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="shift" class="form-label">Shift</label>
                                <select class="form-control" id="shift" name="shift" required>
                                    <option value="Day">Day</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="no_machine" class="form-label">Machine No (Op No)</label>
                                <select class="form-control" id="no_machine" name="no_machine" required>
                                    <option value="">-- Select Machine --</option>
                                </select>
                            </div>
                            <div>
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" form="historical-problem-form">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('line').addEventListener('change', function() {
        const line = this.value;
        fetch(`/get-op-nos/${line}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                const noMachineSelect = document.getElementById('no_machine');
                noMachineSelect.innerHTML = '<option value="">-- Select Machine --</option>';
                data.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.id;
                    option.textContent = machine.op_no;
                    noMachineSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching machine data:', error);
                alert('Error fetching machine data: ' + error.message);
            });
    });
</script>
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
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- The rows will be populated by DataTables -->
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
                            case 'Open': statusClass = 'btn-primary'; break;
                            case 'Delay': statusClass = 'btn-warning'; break;
                            case 'Ongoing': statusClass = 'btn-info'; break;
                            default: statusClass = 'btn-danger';
                        }
                        return '<button class="btn ' + statusClass + ' btn-sm">' + data + '</button>';
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



<!-- DataTables script remains unchanged -->
@endsection
