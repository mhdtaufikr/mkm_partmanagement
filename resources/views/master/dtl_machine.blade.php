@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                {{-- <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                            Dropdown App Menu
                        </h1>
                        <div class="page-header-subtitle">Use this blank page as a starting point for creating new pages inside your project!</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">Optional page header content</div>
                </div> --}}
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-n10">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
        {{-- <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1>    </h1>
            </div>
            </div>
        </div><!-- /.container-fluid --> --}}
        </section>

        <!-- Main content -->
        <section class="content">

        <div class="container-fluid">

            <div class="card card-header-actions mb-4">
                <div class="card-header text-dark">
                <h3>Machine Details</h3>
                </div>
                <div class="card-body">

                    <div class="col-sm-12">
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <ul>
                                    <li><strong>Data Process Failed !</strong></li>
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!--end validasi form-->
                    </div>

                    <div class="row">


                        <div class="col-md-4">
                            <label for="">Machine Name</label>
                            <p>{{ $machine->machine_name }}</p>
                            <label for="">Plant</label>
                            <p>{{ $machine->plant }}</p>
                            <label for="">Line</label>
                            <p>{{ $machine->line }}</p>
                            <label for="">OP No.</label>

                        </div>
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-4">

                        </div>
                        <h2>{{ $machine->machine_name }}</h2>
                        <p>Plant: {{ $machine->plant }}</p>
                        <p>Line: {{ $machine->line }}</p>
                        <p>Operation No.: {{ $machine->op_no }}</p>
                        <p>Process: {{ $machine->process }}</p>
                        <p>Maker: {{ $machine->maker }}</p>
                        <p>Model: {{ $machine->model }}</p>
                        <p>Serial Number: {{ $machine->serial_number }}</p>
                        <p>Date: {{ $machine->date }}</p>
                        <p>Control NC: {{ $machine->control_nc }}</p>
                        <p>Control PLC: {{ $machine->control_plc }}</p>
                        <p>Control Servo: {{ $machine->control_servo }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Part List</h3>
                    </div>


                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-sm-12">
                            <div class="col-sm-12">
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
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <ul>
                                        <li><strong>Data Process Failed !</strong></li>
                                        @foreach ($errors->all() as $error)
                                            <li><strong>{{ $error }}</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            <!--end validasi form-->
                            </div>
                        </div>
                        <div class="table-responsive">
                        <table id="tableUser" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Critical Part</th>
                            <th>Type</th>
                            <th>Estimation Lifetime</th>
                            <th>Cost</th>
                            <th>Last Replace</th>
                            <th>Safety Stock</th>
                            <th>SAP Stock</th>
                            <th>Repair Stock</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                            $no=1;
                            @endphp
                           @foreach ($machine->spareParts as $part)
                            <tr>
                                <td>{{ $part->critical_part }}</td>
                                <td>{{ $part->type }}</td>
                                <td>{{ $part->estimation_lifetime }}</td>
                                <td>{{ $part->cost }}</td>
                                <td>{{ $part->last_replace }}</td>
                                <td>{{ $part->safety_stock }}</td>
                                <td>{{ $part->sap_stock }}</td>
                                <td>{{ $part->repair_stock }}</td>
                                <td>{{ $part->total }}</td>
                                <td>{{ $part->status }}</td>
                                <td>

                                    <button title="Edit Asset" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-update{{ $part->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button title="Detail Sub Asset" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-detail{{ $part->id }}">
                                        <i class="fas fa-info"></i>
                                    </button>

                                    <button title="Delete Asset" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $part->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                        </table>
                    </div>
                    </div>
                    <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    </div>


</main>

<!-- For Datatables -->
<script>
    $(document).ready(function () {
                        var table = $("#tableUser").DataTable({
                            "responsive": false,
                            "lengthChange": false,
                            "autoWidth": false,
                            "order": [],
                            "dom": 'Bfrtip',
                            "buttons": []
                        });
                    });
    </script>
@endsection
