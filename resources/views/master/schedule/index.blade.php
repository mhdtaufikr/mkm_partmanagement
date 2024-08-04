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
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1>Preventive Maintenance Schedule</h1>
            </div>
            <div class="card-body">
                @foreach($items as $type => $lines)
                    <h2>{{ $type }}</h2>
                    <div class="table-responsive">


                    <table class="table table-striped table-bordered">
                        <thead class="text-center align-middle ">
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
                                    <a target="_blank" href="{{ url('/mst/preventive/schedule/detail/' . $month) }}">
                                        {{ DateTime::createFromFormat('!m', $month)->format('M') }}
                                    </a>
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
                                            $icon = $detail->actual_date ? '<i class="fas fa-dot-circle"></i>' : '<i class="far fa-dot-circle"></i>';
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
                                            <!-- Dropdown button for PM actions -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton{{ $schedule->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $schedule->id }}">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                                </ul>
                                            </div>
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
@endsection
