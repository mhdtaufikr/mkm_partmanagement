@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4"></div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header"></section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{$item->machine_name}}</h3>
                                </div>
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
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <form action="{{ url('/checksheet/store') }}" method="POST">
                                                @csrf
                                                <input name="id" value="{{ $item->id }}" type="text" hidden>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <!-- Row 1: OP No and Machine Name -->
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="op_number"><strong>OP No.</strong></label>
                                                                <input type="text" class="form-control" id="op_number" name="op_number" value="{{ $item->op_no }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="machine_name"><strong>Machine Name</strong></label>
                                                                <input readonly value="{{ $item->machine_name }}" type="text" class="form-control" id="machine_name" name="machine_name">
                                                            </div>
                                                        </div>

                                                        <!-- Row 2: PIC and No Document -->
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="pic"><strong>PIC</strong></label>
                                                                <input type="text" class="form-control" id="pic" name="pic" value="{{ $item->pic }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="no_document"><strong>No. Document</strong></label>
                                                                <input type="text" class="form-control" id="no_document" name="no_document" value="{{ $item->no_document }}" readonly>
                                                            </div>
                                                        </div>

                                                        <!-- Row 3: Process and Mfg Date -->
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="process"><strong>Process</strong></label>
                                                                <input type="text" class="form-control" id="process" name="process" value="{{ $item->process }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="mfg_date"><strong>Mfg Date</strong></label>
                                                                <input readonly value="{{ $item->mfg_date }}" type="date" class="form-control" id="mfg_date" name="mfg_date">
                                                            </div>
                                                        </div>

                                                        <!-- Row 4: Department and Effective Date -->
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="dept"><strong>Dept</strong></label>
                                                                <input type="text" class="form-control" id="dept" name="dept" value="{{ $item->dept }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="effective_date"><strong>Effective Date</strong></label>
                                                                <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{ $item->effective_date }}" readonly>
                                                            </div>
                                                        </div>

                                                        <!-- Row 5: Plan Date and Actual Date -->
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="planning_date"><strong>Plan Date</strong></label>
                                                                <select class="form-control" id="planning_date" name="planning_date" required>
                                                                    <option value="">-- Select Planning Date --</option>
                                                                    @foreach($plannedDates as $date)
                                                                        <option value="{{ $date->id }}">{{ date('d/m/Y', strtotime($date->annual_date)) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group">
                                                                <label for="actual_date"><strong>Actual Date</strong></label>
                                                                <input value="{{ date('Y-m-d') }}" type="date" class="form-control" id="actual_date" name="actual_date" required>
                                                            </div>
                                                        </div>

                                                        <!-- Row 6: Shop and Revision -->
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="shop"><strong>Shop</strong></label>
                                                                <input type="text" class="form-control" id="shop" name="shop" value="{{ $item->shop }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <div class="form-group">
                                                                <label for="rev"><strong>Revision</strong></label>
                                                                <input type="text" class="form-control" id="rev" name="rev" value="{{ $item->revision }}" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary" id="oneClickButton">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
    $(document).ready(function() {
        var table = $("#tableUser").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    });
</script>
@endsection
