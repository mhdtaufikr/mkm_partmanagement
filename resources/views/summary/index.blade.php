@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="list"></i></div>
                            Summary of All Machines
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Combined Data for All Machines
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableSummary" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Machine</th>
                                <th>Category</th>
                                <th>Problem / Maintenance</th>
                                <th>Action Taken</th>
                                <th>Status</th>
                                <th>Flag</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($combinedData as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data->type }}</td>
                                <td>{{ $data->date }}</td>
                                <td>{{ $data->data->machine->name ?? 'N/A' }}</td> <!-- Machine name -->
                                <td>{{ $data->Category }}</td>
                                <td>
                                    @if($data->type == 'Preventive Maintenance')
                                        <a href="{{ $data->checksheet_link }}" target="_blank">
                                            View Preventive Maintenance
                                        </a>
                                    @else
                                        {{ $data->data->problem ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $data->data->action ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $data->data->status == 'Close' ? 'success' : 'danger' }}">
                                        {{ $data->data->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($data->status_logs->isNotEmpty() || $data->flag)
                                    <i class="fas fa-flag" style="color: rgba(0, 103, 127, 1);"></i>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->data->id }}">Detail</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Structure for Detail -->
@foreach ($combinedData as $data)
<div class="modal fade" id="modal-detail-{{ $data->data->id }}" tabindex="-1" aria-labelledby="modal-detail-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detail-label">Detail for {{ $data->data->machine->name ?? 'N/A' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Type: {{ $data->type }}</p>
                <p>Date: {{ $data->date }}</p>
                <p>Category: {{ $data->Category }}</p>
                <p>Problem/Maintenance: {{ $data->data->problem ?? $data->data->type }}</p>
                <p>Action Taken: {{ $data->data->action }}</p>
                <p>Status: {{ $data->data->status }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
