@extends('layouts.master')

@section('content')
<main>
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
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1 style="color: white">Preventive Maintenance Schedule</h1>
            </div>
            <div class="card-body">
                 <!--alert success -->
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

                 <!--alert success -->
                 <!--validasi form-->
                   @if (count($errors)>0)
                     <div class="alert alert-info alert-dismissible fade show" role="alert">
                       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         <ul>
                             <li><strong>Data Process Failed !</strong></li>
                             @foreach ($errors->all() as $error)
                                 <li><strong>{{ $error }}</strong></li>
                             @endforeach
                         </ul>
                     </div>
                   @endif
                 <!--end validasi form-->
                <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                    <i class="fas fa-plus-square"></i> Add Schedule
                </button>

                <!-- Modal for Adding Schedule -->
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
                                        <div class="col-md-3 mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <select id="type" name="type" class="form-select" required>
                                                <option value="" disabled selected>Select Type</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Plant Dropdown -->
                                        <div class="col-md-3 mb-3">
                                            <label for="plant" class="form-label">Plant</label>
                                            <select id="plant" name="plant" class="form-select" required>
                                                <option value="" disabled selected>Select Plant</option>
                                            </select>
                                        </div>

                                        <!-- Shop Dropdown -->
                                        <div class="col-md-3 mb-3">
                                            <label for="shop" class="form-label">Shop</label>
                                            <select id="shop" name="shop" class="form-select" required>
                                                <option value="" disabled selected>Select Shop</option>
                                            </select>
                                        </div>

                                        <!-- OP Number Dropdown -->
                                        <div class="col-md-3 mb-3">
                                            <label for="op_no" class="form-label">OP No</label>
                                            <select id="op_no" name="op_no" class="form-select" required>
                                                <option value="" disabled selected>Select OP No</option>
                                            </select>
                                        </div>

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
                            fetch(`/fetch-plants/${this.value}`)
                                .then(response => response.json())
                                .then(data => {
                                    plantSelect.innerHTML = '<option value="" disabled selected>Select Plant</option>';
                                    data.forEach(plant => {
                                        plantSelect.innerHTML += `<option value="${plant}">${plant}</option>`;
                                    });
                                    shopSelect.innerHTML = '<option value="" disabled selected>Select Shop</option>';
                                    opNoSelect.innerHTML = '<option value="" disabled selected>Select OP No</option>';
                                });
                        });

                        plantSelect.addEventListener('change', function () {
                            fetch(`/fetch-shops/${typeSelect.value}/${this.value}`)
                                .then(response => response.json())
                                .then(data => {
                                    shopSelect.innerHTML = '<option value="" disabled selected>Select Shop</option>';
                                    data.forEach(shop => {
                                        shopSelect.innerHTML += `<option value="${shop}">${shop}</option>`;
                                    });
                                    opNoSelect.innerHTML = '<option value="" disabled selected>Select OP No</option>';
                                });
                        });

                        shopSelect.addEventListener('change', function () {
                            fetch(`/fetch-opnos/${typeSelect.value}/${plantSelect.value}/${this.value}`)
                                .then(response => response.json())
                                .then(data => {
                                    opNoSelect.innerHTML = '<option value="" disabled selected>Select OP No</option>';
                                    data.forEach(opNo => {
                                        opNoSelect.innerHTML += `<option value="${opNo}">${opNo}</option>`;
                                    });
                                });
                        });
                    });
                </script>


                @foreach($items as $type => $lines)
                    <h2>{{ $type }}</h2>
                    <div class="table-responsive">
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
                                        <th>
                                            <a style="color: white" target="_blank" href="{{ url('/mst/preventive/schedule/detail/' . $month) }}">
                                                {{ DateTime::createFromFormat('!m', $month)->format('M') }}
                                            </a>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($lines as $line => $schedules)
                                    @php
                                        $rowCount = $schedules->count();
                                        $scheduleMap = [];
                                        foreach($schedules as $schedule) {
                                            foreach($schedule->details as $detail) {
                                                $month = \Carbon\Carbon::createFromFormat('Y-m-d', $detail->annual_date)->month;
                                                $icon = $detail->actual_date ? '<i class="fas fa-circle"></i>' : '<i class="far fa-circle"></i>';
                                                $scheduleMap[$schedule->preventiveMaintenance->machine->op_no][$month] = $icon;
                                            }
                                        }
                                    @endphp
                                    @foreach($schedules->unique('preventiveMaintenance.machine.op_no') as $schedule)
                                        <tr>
                                            @if($loop->first)
                                                <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ $no++ }}</td>
                                                <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ $line }}</td>
                                            @endif
                                            <td class="text-center align-middle">{{ $schedule->preventiveMaintenance->machine->op_no ?? '' }}</td>
                                            <td class="text-center align-middle">{{ $schedule->preventiveMaintenance->machine->process ?? '' }}</td>
                                            <td class="text-center align-middle">{{ $schedule->preventiveMaintenance->machine->install_date ?? '' }}</td>
                                            @for($month = 1; $month <= 12; $month++)
                                                <td class="text-center align-middle">
                                                    {!! $scheduleMap[$schedule->preventiveMaintenance->machine->op_no][$month] ?? '' !!}
                                                </td>
                                            @endfor
                                            <td class="text-center align-middle">
                                                <!-- Button to trigger modal -->
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
                @endforeach
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
            console.log('Removing detail with ID:', detailId); // Log the detail ID being removed
            const detailInput = document.getElementById('annual_date_' + detailId);
            detailInput.removeAttribute('name'); // Remove name attribute to exclude from form submission
            event.currentTarget.closest('tr').remove(); // Remove the detail element
        }

        function addRemoveDetailEventListeners() {
            document.querySelectorAll('.remove-detail').forEach(button => {
                button.removeEventListener('click', removeDetailHandler); // Remove any previous event listener
                button.addEventListener('click', removeDetailHandler); // Add the new event listener
                console.log('Added remove event listener to:', button); // Log the button to which the listener is added
            });
        }

        document.querySelectorAll('.edit-schedule').forEach(button => {
            button.addEventListener('click', function () {
                const schedule = JSON.parse(this.getAttribute('data-schedule'));
                console.log('Editing schedule:', schedule); // Log the schedule being edited

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
            console.log('Add schedule button clicked'); // Log when the button is clicked
            const button = event.currentTarget;
            const scheduleId = button.getAttribute('data-schedule-id');
            console.log('Adding new detail to schedule ID:', scheduleId); // Log the schedule ID to which a new detail is added
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
            console.log('New detail added with ID:', newDetailId); // Log the new detail ID being added
            scheduleDetails.appendChild(newDetail);
            addRemoveDetailEventListeners(); // Add event listener to the new remove button
        }

        document.querySelector('.add-schedule').addEventListener('click', addScheduleHandler);
    });
</script>
@endsection
