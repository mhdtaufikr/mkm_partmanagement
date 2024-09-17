@extends('layouts.master')

@section('content')
<main>
    <!-- Page Header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-calendar-week"></i></div>
                            Master Schedule Preventive Maintenance
                        </h1>
                        <div class="page-header-subtitle">Manage Preventive Maintenance Schedule</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadPlannedModal">
                            <i class="fas fa-file-excel"></i> Master Schedule
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="uploadPlannedModal" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal-add-label">Upload Annual Schedule PM</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('/annual/schedule/upload') }}" method="POST" enctype="multipart/form-data">
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
                                            <a href="{{url('/annual/schedule/template')}}" class="btn btn-link">
                                                Download Excel Format
                                            </a>
                                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1 style="color: white">Preventive Maintenance Schedule</h1>
            </div>
            <div class="card-body">
                <!-- Success and Error Messages -->
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('failed') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (count($errors)>0)
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul>
                            <li><strong>Data Process Failed!</strong></li>
                            @foreach ($errors->all() as $error)
                                <li><strong>{{ $error }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add Schedule Button -->
                <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                    <i class="fas fa-plus-square"></i> Add Schedule
                </button>

                <!-- Add Schedule Modal -->
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-add-label">Add Schedule</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ url('/schedule/store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <!-- Type Dropdown -->
                                        <div class="col-md-4 mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <select id="type" name="type" class="form-select" required>
                                                <option value="" disabled selected>Select Type</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Plant Dropdown -->
                                        <div class="col-md-4 mb-3">
                                            <label for="plant" class="form-label">Plant</label>
                                            <select id="plant" name="plant" class="form-select" required>
                                                <option value="" disabled selected>Select Plant</option>
                                            </select>
                                        </div>

                                        <!-- Shop Dropdown (Hidden by default) -->
                                        <div hidden class="col-md-3 mb-3">
                                            <label for="shop" class="form-label">Shop</label>
                                            <select id="shop" name="shop" class="form-select" disabled>
                                                <option value="" disabled selected>Select Shop</option>
                                            </select>
                                        </div>

                                        <!-- OP Number Dropdown -->
                                        <div class="col-md-4 mb-3">
                                            <label for="op_no" class="form-label">OP No</label>
                                            <select id="op_no" name="op_no" class="form-select" required>
                                                <option value="" disabled selected>Select OP No</option>
                                            </select>
                                        </div>

                                        <!-- Frequency and Date Inputs -->
                                        <div class="col-md-6 mb-3">
                                            <label for="frequency" class="form-label">Frequency</label>
                                            <select class="form-select" id="frequency" name="frequency" required>
                                                <option value="1">1 Month</option>
                                                <option value="2">2 Months</option>
                                                <option value="3">3 Months</option>
                                                <option value="4">4 Months</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="date" class="form-label">Date</label>
                                            <input class="form-control" type="number" name="date" id="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
 const typeSelect = document.getElementById('type');
 const plantSelect = document.getElementById('plant');
 const shopSelect = document.getElementById('shop');
 const opNoSelect = document.getElementById('op_no');

 typeSelect.addEventListener('change', function () {
     const selectedType = this.value;

     // Set the shop value based on the type selection
     if (selectedType === 'Mechanic' || selectedType === 'Electric') {
         shopSelect.value = 'ME';
     } else {
         shopSelect.value = 'PH';
     }

     // Disable the shop dropdown
     shopSelect.disabled = true;

     // Fetch plants based on the type
     fetch(`/fetch-plants/${selectedType}`)
         .then(response => response.json())
         .then(data => {
             plantSelect.innerHTML = '<option value="" disabled selected>Select Plant</option>';
             data.forEach(plant => {
                 plantSelect.innerHTML += `<option value="${plant}">${plant}</option>`;
             });

             // Clear the OP No dropdown
             opNoSelect.innerHTML = '<option value="" disabled selected>Select OP No</option>';
         })
         .catch(error => {
             console.error('Error fetching plants:', error);
         });
 });

 plantSelect.addEventListener('change', function () {
     const selectedType = typeSelect.value;
     const selectedPlant = this.value;
     const shopValue = (selectedType === 'Powerhouse') ? 'PH' : 'ME';

     // Fetch OP Nos based on the type, plant, and shop
     fetch(`/fetch-opnos/${selectedType}/${selectedPlant}/${shopValue}`)
         .then(response => response.json())
         .then(data => {
             opNoSelect.innerHTML = '<option value="" disabled selected>Select OP No</option>';
             data.forEach(opNoAndMachine => {
                 opNoSelect.innerHTML += `<option value="${opNoAndMachine}">${opNoAndMachine}</option>`;
             });
         })
         .catch(error => {
             console.error('Error fetching OP Nos:', error);
         });
 });
});

             </script>

                <!-- Nav Tabs for Each Line -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach($items as $type => $lines)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ Str::slug($type) }}" data-bs-toggle="tab" data-bs-target="#content-{{ Str::slug($type) }}" type="button" role="tab" aria-controls="content-{{ Str::slug($type) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $type }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content for Each Line -->
                <div class="tab-content" id="myTabContent">
                  <!-- Loop through each type (Electric, Mechanic, Powerhouse) -->
@foreach($items as $type => $lines)
<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ Str::slug($type) }}" role="tabpanel" aria-labelledby="tab-{{ Str::slug($type) }}">
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
            <thead class="text-center align-middle">
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Line</th>
                    <th rowspan="2">OP No</th>
                    <th rowspan="2">Process Name</th>
                    <th rowspan="2">Year</th>
                    <th colspan="12">2024</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    @for($month = 1; $month <= 12; $month++)
                        <th>{{ DateTime::createFromFormat('!m', $month)->format('M') }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp

                <!-- Loop over each line -->
                @foreach($lines as $line => $schedules)
                    @php
                        // Count how many schedules there are for this line
                        $rowCount = $schedules->count();
                        $scheduleMap = [];

                        // Build the schedule map for the given line
                        foreach($schedules as $schedule) {
                            $opNo = optional(optional($schedule->preventiveMaintenance)->machine)->op_no ?? optional($schedule->machine)->op_no;

                            foreach($schedule->details as $detail) {
                                $month = \Carbon\Carbon::createFromFormat('Y-m-d', $detail->annual_date)->month;
                                $icon = $detail->actual_date ? '<i class="fas fa-circle"></i>' : '<i class="far fa-circle"></i>';
                                $scheduleMap[$opNo][$month] = $icon;
                            }
                        }
                    @endphp

                    <!-- Loop through each schedule in this line -->
                    @foreach($schedules as $index => $schedule)
                        <tr>
                            <!-- Only show the No. and Line for the first schedule -->
                            @if($index === 0)
                                <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ $no++ }}</td>
                                <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ $line }}</td>
                            @endif

                            @php
                                // Extract OP No, Process Name, and Install Date
                                $opNo = optional(optional($schedule->preventiveMaintenance)->machine)->op_no ?? optional($schedule->machine)->op_no;
                                $process = optional(optional($schedule->preventiveMaintenance)->machine)->process ?? optional($schedule->machine)->process;
                                $installDate = optional(optional($schedule->preventiveMaintenance)->machine)->install_date ?? optional($schedule->machine)->install_date;
                            @endphp

                            <!-- Display the OP No, Process Name, and Install Date -->
                            <td class="text-center align-middle">{{ $opNo }}</td>
                            <td class="text-center align-middle">{{ $process }}</td>
                            <td class="text-center align-middle">{{ $installDate }}</td>

                            <!-- Loop over each month (January - December) and show the schedule -->
                            @for($month = 1; $month <= 12; $month++)
                                <td class="text-center align-middle">
                                    {!! $scheduleMap[$opNo][$month] ?? '' !!}
                                </td>
                            @endfor

                            <!-- Action Button (e.g., Edit) -->
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-primary edit-schedule" data-schedule="{{ $schedule->toJson() }}" data-bs-toggle="modal" data-bs-target="#editScheduleModal">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>

        </table>
    </div>
</div>
@endforeach

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="modal-edit-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-label">Edit Schedule PM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/annual/schedule/update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" id="modal-schedule-id">
                    <div class="mb-3">
                        <label for="frequency" class="form-label">Frequency</label>
                        <input type="text" class="form-control" id="modal-frequency" name="frequency">
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="modal-schedule-details"></tbody>
                    </table>
                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-success btn-sm add-schedule" data-schedule-id="">Add Schedule</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function removeDetailHandler(event) {
            const detailId = event.currentTarget.getAttribute('data-detail-id');
            const detailInput = document.getElementById('annual_date_' + detailId);
            detailInput.removeAttribute('name'); // Remove name attribute to exclude from form submission
            event.currentTarget.closest('tr').remove(); // Remove the detail element
        }

        function addRemoveDetailEventListeners() {
            document.querySelectorAll('.remove-detail').forEach(button => {
                button.removeEventListener('click', removeDetailHandler); // Remove any previous event listener
                button.addEventListener('click', removeDetailHandler); // Add the new event listener
            });
        }

        document.querySelectorAll('.edit-schedule').forEach(button => {
            button.addEventListener('click', function () {
                const schedule = JSON.parse(this.getAttribute('data-schedule'));
                document.getElementById('modal-schedule-id').value = schedule.id;
                document.getElementById('modal-frequency').value = schedule.frequency;

                const scheduleDetails = document.getElementById('modal-schedule-details');
                scheduleDetails.innerHTML = ''; // Clear previous details

                schedule.details.forEach(detail => {
                    const row = document.createElement('tr');
                    row.classList.add('schedule-detail');
                    row.innerHTML = `
                        <td>
                            <input type="date" class="form-control" id="annual_date_${detail.id}" name="annual_dates[${detail.id}]" value="${detail.annual_date}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-detail" data-detail-id="${detail.id}">Remove</button>
                        </td>
                    `;
                    scheduleDetails.appendChild(row);
                });

                addRemoveDetailEventListeners(); // Add event listener to the new remove buttons
            });
        });

        function addScheduleHandler(event) {
            const button = event.currentTarget;
            const scheduleId = button.getAttribute('data-schedule-id');
            const scheduleDetails = document.getElementById('modal-schedule-details');

            const newDetailId = 'new_' + Date.now();
            const newDetail = document.createElement('tr');
            newDetail.classList.add('schedule-detail');
            newDetail.innerHTML = `
                <td>
                    <input type="date" class="form-control" id="annual_date_${newDetailId}" name="new_annual_dates[]">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-detail" data-detail-id="${newDetailId}">Remove</button>
                </td>
            `;
            scheduleDetails.appendChild(newDetail);
            addRemoveDetailEventListeners(); // Add event listener to the new remove button
        }

        document.querySelector('.add-schedule').addEventListener('click', addScheduleHandler);
    });
</script>
@endsection
