@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-info-circle"></i></div>
                            Part Details
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-header">
                <h1 style="color: white">{{ $part->material }} - {{ $part->material_description }}</h1>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Part Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Type:</strong>
                                        <p>{{ $part->type }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Plant:</strong>
                                        <p>{{ $part->plnt }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Storage Location:</strong>
                                        <p>{{ $part->sloc }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Vendor:</strong>
                                        <p>{{ $part->vendor }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Unit:</strong>
                                        <p>{{ $part->bun }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Beginning Quantity:</strong>
                                        <p>{{ $part->begining_qty }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Beginning Value:</strong>
                                        <p>{{ $part->begining_value }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Total Stock:</strong>
                                        <p>{{ $part->total_stock }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Total Value:</strong>
                                        <p>{{ $part->total_value }}</p>
                                    </div>
                                </div>
                                <div class="card mb-2 border border-dark rounded">
                                    <div class="card-body p-2">
                                        <strong>Currency:</strong>
                                        <p>{{ $part->currency }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Repair Part Information</h5>
                        <table id="RepairList" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Repaired Quantity</th>
                                    <th>Repair Date</th>
                                    <th>Storage Location</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repairParts as $repairPart)
                                    <tr>
                                        <td>{{ $repairPart->repaired_qty }}</td>
                                        <td>{{ $repairPart->repair_date }}</td>
                                        <td>{{ $repairPart->sloc }}</td>
                                        <td>{{ $repairPart->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <h5>Machines Using This Part</h5>
                <table id="MachineList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OP. No.</th>
                            <th>Machine Name</th>
                            <th>Critical Part</th>
                            <th>Type</th>
                            <th>Estimation Lifetime</th>
                            <th>Cost</th>
                            <th>Last Replace</th>
                            <th>Safety Stock</th>
                            <th>Repair Stock</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($machineParts as $machinePart)
                            <tr>
                                <td>{{ $machinePart->machine->op_no }}</td>
                                <td>{{ $machinePart->machine->machine_name }}</td>
                                <td>{{ $machinePart->critical_part }}</td>
                                <td>{{ $machinePart->type }}</td>
                                <td>{{ $machinePart->estimation_lifetime }}</td>
                                <td>{{ $machinePart->cost }}</td>
                                <td>{{ $machinePart->last_replace }}</td>
                                <td>{{ $machinePart->safety_stock }}</td>
                                <td>{{ $machinePart->repair_stock }}</td>
                                <td>{{ $machinePart->total }}</td>
                                <td>
                                    @if ($machinePart->status)
                                        @php
                                            $statusClass = $machinePart->status == 'Safe' ? 'btn-success' : ($machinePart->status == 'Need to Order' ? 'btn-warning' : 'btn-danger');
                                        @endphp
                                        <button class="btn {{ $statusClass }} btn-sm">
                                            {{ $machinePart->status }}
                                        </button>
                                    @else
                                        <button class="btn btn-danger btn-sm">
                                            Out Of Stock
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<style>
    .card-body p {
        margin: 0;
    }
    .card-body {
        padding: 0.5rem;
    }
    .card {
        margin-bottom: 0.5rem;
    }
</style>

<script>
    $(document).ready(function() {
        $('#RepairList').DataTable({
            "pageLength": 5, // Limit the number of rows to 2 per page
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
        });

        $('#MachineList').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false
        });
    });
</script>
@endsection
