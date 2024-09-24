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
                        <!-- Machine Details Card -->
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
                                                    <!-- Machine Information Section -->
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group">
                                                            <label for="">No. Document</label>
                                                            <input type="text" class="form-control" id="no_document" name="no_document" value="{{$itemHead->no_document}}" readonly required>
                                                        </div>
                                                    </div>
                                                    @if($itemHead->type == 'Mechanic')
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="department">Department</label>
                                                                <input type="text" class="form-control" id="department" name="department" value="Maintenance" readonly required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="shop">Shop</label>
                                                                <input type="text" class="form-control" id="shop" name="shop" value="Mechanic Stamping" readonly required>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="department">Department</label>
                                                                <input type="text" class="form-control" id="department" name="department" value="Manufacturing Engineering Stamping" readonly required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="shop">Shop</label>
                                                                <input type="text" class="form-control" id="shop" name="shop" value="MTC Electric" readonly required>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-md-6 mb-4">
                                                        <div class="form-group">
                                                            <label for="">Effective Date</label>
                                                            <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{$itemHead->effective_date}}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Revision</label>
                                                            <input type="text" class="form-control" id="revision" name="revision" value="{{$itemHead->revision}}" readonly required>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group">
                                                            <label for="">OP No.</label>
                                                            <input type="text" class="form-control" id="op_number" name="op_number" value="{{$itemHead->preventiveMaintenance->machine->op_no}}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group">
                                                            <label for="">Mfg Date</label>
                                                            <input readonly value="{{$itemHead->preventiveMaintenance->machine->mfg_date}}" type="date" class="form-control" id="mfg_date" name="mfg_date" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group">
                                                            <label for="">Planning Date</label>
                                                            <input readonly value="{{$itemHead->planning_date}}" type="date" class="form-control" id="planning_date" name="planning_date" required>
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
                                                            <input type="text" class="form-control" id="process" name="process" value="{{$itemHead->preventiveMaintenance->machine->process}}" readonly required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group">
                                                            <label for="">Actual Date</label>
                                                            <input readonly value="{{$itemHead->actual_date}}" type="date" class="form-control" id="actual_date" name="actual_date" required>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        @php
                                                            $imagePaths = json_decode($itemHead->img);
                                                        @endphp
                                                            @if(!empty($imagePaths))
                                                                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                                                    <div class="carousel-indicators">
                                                                        @foreach ($imagePaths as $index => $image)
                                                                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="carousel-inner">
                                                                        @foreach ($imagePaths as $index => $image)
                                                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                                <img src="{{ asset($image) }}" class="d-block w-100" style="max-height: 400px; object-fit: contain;" alt="Image {{ $index + 1 }}">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                        <span class="visually-hidden">Previous</span>
                                                                    </button>
                                                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                        <span class="visually-hidden">Next</span>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <p>No images available.</p>
                                                            @endif


                                                        {{-- crousell image --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>



                        <!-- Grouped Checksheet Details Section -->
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{$itemHead->preventiveMaintenance->machine->machine_name}}</h3>
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

                     <!-- Status Log Details Section (if available) -->
                     @if($logStatus)
                     <div class="col-12 mb-4">
                         <div class="card">
                             <div class="card-header">
                                 <h3 class="card-title">Follow Up Problem</h3>
                             </div>
                             <!-- /.card-header -->
                             <div class="card-body">
                                 <div class="row">
                                    <div class="col-md-4">
                                        @if($logStatus->img)
                                        <img src="{{ asset($logStatus->img) }}" class="img-fluid" alt="Problem Image" style="max-width: 400px; max-height: 300px;">
                                     @endif
                                    </div>
                                     <div class="col-md-4">
                                         <h6><strong>Machine No:</strong> {{ $logStatus->machine->no_machine }}</h6>
                                         <h6><strong>Date:</strong> {{ $logStatus->date }}</h6>
                                         <h6><strong>Shift:</strong> {{ $logStatus->shift }}</h6>
                                         <h6><strong>Shop:</strong> {{ $logStatus->shop }}</h6>
                                         <h6><strong>Problem:</strong> {{ $logStatus->problem }}</h6>
                                         <h6><strong>Cause:</strong> {{ $logStatus->cause }}</h6>
                                         <h6><strong>Action:</strong> {{ $logStatus->action }}</h6>
                                     </div>
                                     <div class="col-md-4">
                                         <h6><strong>Start Time:</strong> {{ $logStatus->start_time }}</h6>
                                         <h6><strong>Finish Time:</strong> {{ $logStatus->finish_time }}</h6>
                                         <h6><strong>Balance:</strong> {{ $logStatus->balance }} Hour</h6>
                                         <h6><strong>PIC:</strong> {{ $logStatus->pic }}</h6>
                                         <h6><strong>Remarks:</strong> {{ $logStatus->remarks }}</h6>
                                         <h6><strong>Status:</strong> {{ $logStatus->status }}</h6>
                                     </div>

                                 </div>

                                 <hr>
                                 <h5 class="mb-3">Spare Parts Used</h5>
                                 <div class="table-responsive">
                                     <table class="table table-bordered table-striped">
                                         <thead>
                                             <tr>
                                                 <th>Part No</th>
                                                 <th>Description</th>
                                                 <th>Quantity</th>
                                                 <th>Location</th>
                                                 <th>Stock Type</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach ($logStatus->spareParts as $part)
                                                 <tr>
                                                     <td>{{ $part->part->material ?? null }}</td>
                                                     <td>{{ $part->part->material_description ?? null }}</td>
                                                     <td>{{ $part->qty ?? 0 }}</td>
                                                     <td>{{ $part->location ?? '-' }}</td>
                                                     <td>{{ $part->routes ?? '-' }}</td>
                                                 </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                             <!-- /.card-body -->
                         </div>
                     </div>
                     @endif
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
