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
                      {{--   <div class="page-header-subtitle">Manage Daily Report</div> --}}
                    </div>
                </div>
            </div>
        </div>
    </header>

     <!-- Main page content-->
     <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <!-- Consolidated Card Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Machine History Summary Report</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablehistory" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Type</th> <!-- New Type column -->
                                            <th>Date</th>
                                            <th>Shift</th>
                                            <th>Shop</th>
                                            <th>Problem</th>
                                            <th>Analysis & Cause</th>
                                            <th>Action Taken</th>
                                            <th>Repair Hours</th>
                                            <th>Remarks</th>
                                            <th>Person In Charge</th>
                                            <th>Status</th> <!-- Added Status column -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($combinedData as $data)
                                        <tr>
                                            <td>
                                                {{ $no++ }}
                                                @if($data->status_logs->isNotEmpty() || $data->flag) <!-- Show flag if status logs exist or the record has a flag -->
                                                <i class="fas fa-flag" style="color: rgba(0, 103, 127, 1); margin-left: 10px;"></i>
                                                @endif
                                            </td>
                                            <td>{{ $data->Category }}</td>
                                            <td>{{ $data->date }}</td>
                                            <td>{{ $data->data->shift ?? '-' }}</td>
                                            <td>{{ $data->data->shop }}</td>
                                            <td>{{ $data->data->problem ?? 'Preventive Maintenance'}}</td>
                                            <td>{{ $data->data->cause ?? 'Preventive Maintenance'}}</td>
                                            <td>{{ $data->data->action ?? 'Preventive Maintenance'}}</td>
                                            <td>
                                                {{ $data->data->start_time ? date('H:i', strtotime($data->data->start_time)) : '-' }} -
                                                {{ $data->data->finish_time ? date('H:i', strtotime($data->data->finish_time)) : '-' }}
                                                @if($data->data->balance)
                                                    (Total: {{ number_format($data->data->balance, 2) }} hours)
                                                @endif
                                            </td>
                                            <td>{{ $data->data->remarks ?? 'OK' }}</td>
                                            <td>{{ $data->data->pic ?? 'Hmd. Prod' }}</td>
                                            <td>
                                                <span class="badge bg-success">Close</span>
                                            </td>
                                            <td>
                                                @if($data->type == 'Daily Report')
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->data->id }}">Detail</button>
                                                @else
                                                <a target="_blank" title="Detail" class="btn btn-sm btn-primary" href="{{ url("checksheet/detail/".encrypt($data->data->id_ch)) }}">
                                                    Detail
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End of Consolidated Card Table -->

                    <!-- Modals for details -->
                    @foreach ($combinedData as $data)
                        @if ($data->type == 'Daily Report')
                            @include('partials.logmachine', ['data' => $data])
                        @endif
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</main>
<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $("#tablehistory").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
@endsection
