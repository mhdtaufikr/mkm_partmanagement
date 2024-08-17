@extends('layouts.master')

@section('content')

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{$itemHead->preventiveMaintenance->machine->machine_name}}</h3>
                                </div>

                              <!-- /.card-header -->
                              <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-sm-12">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">No. Document</label>
                                                        <input type="text" class="form-control" id="no_document" name="no_document" placeholder="Enter No. Document" value="{{$itemHead->no_document}}" readonly required>
                                                    </div>
                                                </div>
                                                @if($itemHead->type == 'Mechanic')
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="department">Department</label>
                                                            <input type="text" class="form-control" id="department" name="department" placeholder="Enter Department" value="Maintenance" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="shop">Shop</label>
                                                            <input type="text" class="form-control" id="shop" name="shop" placeholder="Enter Shop" value="Mechanic Stamping" readonly required>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="department">Department</label>
                                                            <input type="text" class="form-control" id="department" name="department" placeholder="Enter Department" value="Manufacturing Engineering Stamping" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="shop">Shop</label>
                                                            <input type="text" class="form-control" id="shop" name="shop" placeholder="Enter Shop" value="MTC Electric" readonly required>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-md-6 mb-4">
                                                    <div class="form-group">
                                                        <label for="">Effective Date</label>
                                                        <input type="date" class="form-control" id="effective_date" name="effective_date" placeholder="Enter Effective Date"  value="{{$itemHead->effective_date}}" readonly required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Revision</label>
                                                        <input type="text" class="form-control" id="revision" name="revision" placeholder="Enter Revision" value="{{$itemHead->revision}}" readonly required>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">OP No.</label>
                                                        <input type="text" class="form-control" id="op_number" name="op_number" placeholder="Enter OP No." value="{{$itemHead->preventiveMaintenance->machine->op_no}}" readonly required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">Mfg Date</label>
                                                        <input readonly value="{{$itemHead->preventiveMaintenance->machine->mfg_date}}" type="date" class="form-control" id="mfg_date" name="mfg_date" placeholder="Enter MFG Date" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">Planning Date</label>
                                                        <input readonly value="{{$itemHead->planning_date}}" type="date" class="form-control" id="planning_date" name="planning_date" placeholder="Enter Planning Date" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">Machine Name</label>
                                                        <input readonly value="{{$itemHead->machine_name}}" type="text" class="form-control" id="machine_name" name="machine_name" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">Process</label>
                                                        <input type="text" class="form-control" id="process" name="process" value="{{$itemHead->preventiveMaintenance->machine->process}}" readonly placeholder="Enter Process" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="">Actual Date</label>
                                                        <input readonly value="{{$itemHead->actual_date}}" type="date" class="form-control" id="actual_date" name="actual_date" placeholder="Enter Actual Date" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>


                                </div>

                              </div>
                              <!-- /.card-body -->
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $itemHead->machine_name }}</h3>
                                </div>

                                <!-- Legend Section -->
                                <div class="top-0 end-0 bg-white p-3 border rounded shadow">
                                    <span style="padding: 10px">B : Bagus</span>
                                    <span style="padding: 10px">R : Repair</span>
                                    <span style="padding: 10px">G : Ganti</span>
                                    <span style="padding: 10px">PP: Perlu Perbaikan</span>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <div class="table-responsive">
                                                <!-- Display all grouped items in one view -->
                                                @foreach ($groupedResults as $checksheetCategory => $items)
                                                    <h1>{{ $checksheetCategory }} - {{ $items[0]['checksheet_type'] }}</h1>
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Description</th>
                                                                <th>Spec</th>
                                                                <th>Act</th>
                                                                <th>B</th>
                                                                <th>R</th>
                                                                <th>G</th>
                                                                <th>PP</th>
                                                                <th>Judge</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($items as $item)
                                                                <tr>
                                                                    <td>{{ $item->item_name }}</td>
                                                                    <td>{{ $item->spec }}</td>
                                                                    <td>{{ $item->act }}</td>
                                                                    <td><input type="checkbox" {{ $item->B ? 'checked' : '' }} disabled></td>
                                                                    <td><input type="checkbox" {{ $item->R ? 'checked' : '' }} disabled></td>
                                                                    <td><input type="checkbox" {{ $item->G ? 'checked' : '' }} disabled></td>
                                                                    <td><input type="checkbox" {{ $item->PP ? 'checked' : '' }} disabled></td>
                                                                    <td>{{ $item->judge }}</td>
                                                                    <td>{{ $item->remarks }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
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
@endsection
