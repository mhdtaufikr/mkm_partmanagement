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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">{{$itemHead->machine_name}}</h3>
                                    @if($itemHead->status == 1)
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            Submit
                                        </button>
                                    @endif
                                </div>
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="approveModalLabel">Approve or Remand Checksheet</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('/checksheet/checker/store') }}" method="POST">
                                                @csrf
                                                <input type="text" name="id" value="{{$itemHead->id}}" hidden>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Action:</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="approvalStatus" id="approveRadio" value="approve" checked>
                                                            <label class="form-check-label" for="approveRadio">Approve</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="approvalStatus" id="remandRadio" value="remand">
                                                            <label class="form-check-label" for="remandRadio">Remand</label>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="remark" class="form-label">Remark:</label>
                                                        <textarea class="form-control" id="remark" name="remark" rows="3" required disabled>{{$itemHead->remark}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" id="oneClickButton">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                              <!-- /.card-header -->
                              <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-sm-12">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Row 1: OP No and Machine Name -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="op_number"><strong>OP No.</strong></label>
                                                        <input type="text" class="form-control" id="op_number" name="op_number" value="{{$itemHead->preventiveMaintenance->machine->op_no}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="machine_name"><strong>Machine Name</strong></label>
                                                        <input readonly value="{{$itemHead->machine_name}}" type="text" class="form-control" id="machine_name" name="machine_name">
                                                    </div>
                                                </div>

                                                <!-- Row 2: PIC and No Document -->
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="pic"><strong>PIC</strong></label>
                                                        <input type="text" class="form-control" id="pic" name="pic" value="{{$itemHead->pic}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="no_document"><strong>No. Document</strong></label>
                                                        <input type="text" class="form-control" id="no_document" name="no_document" value="{{$itemHead->preventiveMaintenance->no_document}}" readonly>
                                                    </div>
                                                </div>

                                                <!-- Row 3: Process and Mfg Date -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="process"><strong>Process</strong></label>
                                                        <input type="text" class="form-control" id="process" name="process" value="{{$itemHead->preventiveMaintenance->machine->process}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="mfg_date"><strong>Mfg Date</strong></label>
                                                        <input readonly value="{{$itemHead->preventiveMaintenance->mfg_date}}" type="date" class="form-control" id="mfg_date" name="mfg_date">
                                                    </div>
                                                </div>

                                                <!-- Row 4: Department and Effective Date -->
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="dept"><strong>Dept</strong></label>
                                                        <input type="text" class="form-control" id="dept" name="dept" value="{{$itemHead->preventiveMaintenance->dept}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="effective_date"><strong>Effective Date</strong></label>
                                                        <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{$itemHead->preventiveMaintenance->effective_date}}" readonly>
                                                    </div>
                                                </div>

                                                <!-- Row 5: Plan Date, Actual Date, Shop, Revision -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="planning_date"><strong>Plan Date</strong></label>
                                                        <input readonly value="{{$itemHead->planning_date}}" type="date" class="form-control" id="planning_date" name="planning_date">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group">
                                                        <label for="actual_date"><strong>Actual Date</strong></label>
                                                        <input readonly value="{{$itemHead->actual_date}}" type="date" class="form-control" id="actual_date" name="actual_date">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="shop"><strong>Shop</strong></label>
                                                        <input type="text" class="form-control" id="shop" name="shop" value="{{$itemHead->preventiveMaintenance->machine->shop}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <div class="form-group">
                                                        <label for="rev"><strong>Revision</strong></label>
                                                        <input type="text" class="form-control" id="rev" name="rev" value="{{$itemHead->preventiveMaintenance->revision}}" readonly>
                                                    </div>
                                                </div>

                                                <!-- Row 6: Image Carousel (Gambar) and Remarks -->
                                                <div class="col-md-6 mb-4">
                                                    <div class="form-group">
                                                        <label for="gambar"><strong>Image</strong></label>
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
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="form-group">
                                                        <label for="remark"><strong>Remark</strong></label>
                                                        <textarea class="form-control" id="remark" name="remark" readonly>{{$itemHead->remark}}</textarea>
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
                                            <!-- Legend Section -->

                                                <!-- Display all grouped items in one view (without tabs) -->
                                                @foreach ($groupedResults as $checksheetCategory => $items)
                                                    @php
                                                        $checksheetType = $items[0]['checksheet_type'];
                                                    @endphp
                                                    <h1>{{ $checksheetCategory }} - {{ $checksheetType }}</h1>



                                                    <!-- Table with the data -->
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

                                                                    <!-- Check icons for B, R, G, PP columns -->
                                                                    <td>
                                                                        @if($item->B)
                                                                            <i class="fas fa-check-square" style="color: #30a138; font-size: 20px;"></i> <!-- Green check icon -->
                                                                        @else
                                                                            <span style="width: 20px; height: 20px; display: inline-block; background-color: #ccc; border: 2px solid #999; border-radius: 3px;"></span> <!-- Empty box -->
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if($item->R)
                                                                            <i class="fas fa-check-square" style="color: #30a138; font-size: 20px;"></i>
                                                                        @else
                                                                            <span style="width: 20px; height: 20px; display: inline-block; background-color: #ccc; border: 2px solid #999; border-radius: 3px;"></span>
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if($item->G)
                                                                            <i class="fas fa-check-square" style="color: #30a138; font-size: 20px;"></i>
                                                                        @else
                                                                            <span style="width: 20px; height: 20px; display: inline-block; background-color: #ccc; border: 2px solid #999; border-radius: 3px;"></span>
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if($item->PP)
                                                                            <i class="fas fa-check-square" style="color: #30a138; font-size: 20px;"></i>
                                                                        @else
                                                                            <span style="width: 20px; height: 20px; display: inline-block; background-color: #ccc; border: 2px solid #999; border-radius: 3px;"></span>
                                                                        @endif
                                                                    </td>

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
@endsection
