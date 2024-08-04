@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-database"></i></div>
                            Master SAP Part
                        </h1>
                        <div class="page-header-subtitle">Manage SAP Master Part</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#uploadMasterPart">
                            <i class="fas fa-file-excel"></i> Master Part Machine
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
      <!-- Modal -->
      <div class="modal fade" id="uploadMasterPart" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-label">Upload Master Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/mst/part/sap/upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="csvFile" name="excel-file" accept=".csv, .xlsx">
                            <p class="text-danger">*file must be .xlsx or .csv</p>
                        </div>
                        @error('excel-file')
                            <div class="alert alert-danger" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <a href="{{url('/mst/part/sap/template')}}" class="btn btn-link">
                            Download Excel Format
                        </a>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Main page content-->
<div class="container-fluid px-4 mt-n10">
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
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Stock Part</h3>
              </div>

              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-sm-12">
                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                            <i class="fas fa-plus-square"></i>
                          </button>




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
                <div class="table-responsive">
                <table id="tableUser" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material</th>
                            {{-- <th>Material Description</th> --}}
                            <th>Plnt</th>
                            <th>SLoc</th>
                            <th>Beginning Qty</th>
                            <th>Beginning Value</th>
                            <th>Received Qty</th>
                            <th>Received Value</th>
                            <th>Consumed Qty</th>
                            <th>Consumed Value</th>
                            <th>Total Stock</th>
                            <th>Total Value</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item as $key => $data)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->material }}</td>
                                    {{-- <td>{{ $data->material_description }}</td> --}}
                                    <td>{{ $data->plnt }}</td>
                                    <td>{{ $data->sloc }}</td>
                                    <td>{{ number_format($data->begining_qty, 0, '.', '') }} {{ $data->bun }}</td>
                                    <td>{{ $data->currency }} {{ number_format($data->begining_value, 2) }}</td>
                                    <td>{{ $data->received_qty }} {{ $data->bun }}</td>
                                    <td>{{ $data->currency }} {{ number_format($data->received_value, 2) }}</td>
                                    <td>{{ $data->consumed_qty }} {{ $data->bun }}</td>
                                    <td>{{ $data->currency }} {{ number_format($data->consumed_value, 2) }}</td>
                                    <td>{{ $data->total_stock }} {{ $data->bun }}</td>
                                    <td>{{ $data->currency }} {{ number_format($data->total_value, 2) }}</td>
                               <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <button title="Edit Part" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-update{{ $data->id }}">
                                                <i class="fas fa-edit me-2"></i>Edit Part
                                            </button>
                                        </li>
                                        <li>
                                            <button title="Delete Part" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
                                                <i class="fas fa-trash-alt me-2"></i>Delete Part
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a title="Detail Part" class="dropdown-item" href="{{ url('/mst/sap/part/info/' . encrypt($data->id)) }}">
                                                <i class="fas fa-info me-2"></i>Detail Part
                                            </a>
                                        </li>
                                    </ul>
                                </div>

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
