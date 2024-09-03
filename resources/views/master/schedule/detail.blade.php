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
                            Master Schedule Preventive Maintenance - {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </h1>
                        <div class="page-header-subtitle">Manage Preventive Maintenance Schedule for {{ DateTime::createFromFormat('!m', $month)->format('F') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1 style="color: white">Preventive Maintenance Schedule - {{ DateTime::createFromFormat('!m', $month)->format('F') }}</h1>
            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="pmScheduleTab" role="tablist">
                    @foreach($items as $type => $schedules)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ Str::slug($type) }}" data-bs-toggle="tab" href="#{{ Str::slug($type) }}" role="tab" aria-controls="{{ Str::slug($type) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $type }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="pmScheduleTabContent">
                    @foreach($items as $type => $schedules)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ Str::slug($type) }}" role="tabpanel" aria-labelledby="tab-{{ Str::slug($type) }}">
                            <div class="table-responsive mt-3">
                                <div class="table-container">
                                    <table class="table table-striped table-bordered">
                                        <thead class="text-center align-middle">
                                            <tr>
                                                <th rowspan="2">No.</th>
                                                <th rowspan="2">Line</th>
                                                <th rowspan="2">OP No</th>
                                                <th rowspan="2">Process Name</th>
                                                <th rowspan="2">Year</th>
                                                <th colspan="31">{{ DateTime::createFromFormat('!m', $month)->format('F') }}</th>
                                                <th rowspan="2">Action</th>
                                            </tr>
                                            <tr>
                                                @for($day = 1; $day <= 31; $day++)
                                                    <th>{{ $day }}</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($schedules as $schedule)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                                    <td class="text-center align-middle">{{ $schedule->machine_line }}</td>
                                                    <td class="text-center align-middle">{{ $schedule->machine_op_no }}</td>
                                                    <td class="text-center align-middle">{{ $schedule->machine_process }}</td>
                                                    <td class="text-center align-middle">{{ $schedule->machine_install_date }}</td>
                                                    @for($day = 1; $day <= 31; $day++)
                                                        <td class="text-center align-middle">
                                                            @php
                                                                $detail = $schedule->details->firstWhere('annual_date', now()->setMonth($month)->setDay($day)->format('Y-m-d'));
                                                            @endphp
                                                            @if($detail)
                                                                {!! $detail->actual_date ? '<i class="fas fa-circle"></i>' : '<i class="far fa-circle"></i>' !!}
                                                            @endif
                                                        </td>
                                                    @endfor
                                                    <td class="text-center align-middle">
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
