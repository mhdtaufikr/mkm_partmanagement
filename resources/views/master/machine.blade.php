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
                            Master Machine
                        </h1>
                        <div class="page-header-subtitle">Manage Master Machine</div>
                    </div>
                    {{-- <div class="col-12 col-xl-auto mt-4">Optional page header content</div> --}}
                </div>
            </div>
        </div>
    </header>
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
                <h3 class="card-title">List of Machine</h3>
              </div>
              <!-- Modal for Adding New Machine -->
<div class="modal fade" id="modal-add-machine" tabindex="-1" aria-labelledby="modal-add-machine-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-machine-label">Add New Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="machine-form" action="{{ url('/mst/machine/add') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plant" class="form-label">Plant</label>
                                <input type="text" class="form-control" id="plant" name="plant" required>
                            </div>
                            <div class="mb-3">
                                <label for="line" class="form-label">Line</label>
                                <input type="text" class="form-control" id="line" name="line" required>
                            </div>
                            <div class="mb-3">
                                <label for="op_no" class="form-label">OP No</label>
                                <input type="text" class="form-control" id="op_no" name="op_no" required>
                            </div>
                            <div class="mb-3">
                                <label for="machine_name" class="form-label">Machine Name</label>
                                <input type="text" class="form-control" id="machine_name" name="machine_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="process" class="form-label">Process</label>
                                <textarea class="form-control" id="process" name="process" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="maker" class="form-label">Maker</label>
                                <input type="text" class="form-control" id="maker" name="maker" required>
                            </div>
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control" id="model" name="model" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="number" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="control_nc" class="form-label">Control NC</label>
                                <input type="text" class="form-control" id="control_nc" name="control_nc">
                            </div>
                            <div class="mb-3">
                                <label for="control_plc" class="form-label">Control PLC</label>
                                <input type="text" class="form-control" id="control_plc" name="control_plc">
                            </div>
                            <div class="mb-3">
                                <label for="control_servo" class="form-label">Control Servo</label>
                                <input type="text" class="form-control" id="control_servo" name="control_servo">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-sm-12">
                        <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add-machine">
                            <i class="fas fa-plus-square"></i> Add Machine
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
                <div class="table">
                <table id="tableUser" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>OP. No</th>
                            <th>Process</th>
                            <th>Model</th>
                            <th>Serial Number</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $data)
                        <tr>
                            <td>{{ $data->op_no }}</td>
                            <td>{{ $data->process }}</td>
                            <td>{{ $data->model }}</td>
                            <td>{{ $data->serial_number }}</td>
                            <td>{{ $data->date }}</td>

                               <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-update{{ $data->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ url('/mst/machine/detail/'.encrypt($data->id)) }}">
                                                <i class="fas fa-info"></i> Detail
                                            </a>


                                        </li>
                                    </ul>
                                </div>
                            </td>
                            </tr>

                    {{-- Modal Update --}}
                    <div class="modal fade" id="modal-update{{ $data->id }}" tabindex="-1" aria-labelledby="modal-update{{ $data->id }}-label" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h4 class="modal-title" id="modal-update{{ $data->id }}-label">Edit Rule</h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ url('/mst/update/'.encrypt($data->id)) }}" method="POST">
                              @csrf
                              @method('post')
                              <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="">Asset No.</label>
                                            <input value="{{$data->asset_no}}" type="text" class="form-control" id="asset_no" name="asset_no" placeholder="Enter Asset No." required>
                                          </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="">Asset Name</label>
                                            <input value="{{$data->name}}" type="text" class="form-control" id="asset_name" name="asset_name" placeholder="Enter Asset Name" required>
                                          </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="">Asset Description</label>
                                            <textarea  class="form-control" name="asset_description" cols="30" rows="5" required>{{$data->description}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="qty">Asset Quantity</label>
                                            <input value="{{$data->qty}}" class="form-control" name="qty" type="number" value="0" required>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="">Asset Status</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="{{$data->status}}">{{$data->status}}</option>
                                                @foreach ($dropdown as $status)
                                                    <option value="{{ $status->name_value }}">{{ $status->name_value }}</option>
                                                @endforeach
                                              </select>
                                            </div>
                                    </div> --}}
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    {{-- Modal Update --}}

                    {{-- Modal Delete --}}
                    <div class="modal fade" id="modal-delete{{ $data->id }}" tabindex="-1" aria-labelledby="modal-delete{{ $data->id }}-label" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h4 class="modal-title" id="modal-delete{{ $data->id }}-label">Delete Asset</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ url('/mst/delete/'.encrypt($data->id)) }}" method="POST">
                            @csrf
                            @method('post')
                            <div class="modal-body">
                                <div class="form-group">
                                Are you sure you want to delete <label for="rule">{{ $data->name }}</label>?
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            </form>
                        </div>
                        </div>
                    </div>
                    {{-- Modal Delete --}}

                    {{-- Modal Access --}}
                    <div class="modal fade" id="modal-access}">
                      <div class="modal-dialog">
                          <div class="modal-content">
                          <div class="modal-header">
                              <h4 class="modal-title">Give User Access</h4>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <form action="{{ url('') }}" enctype="multipart/form-data" method="GET">
                          @csrf
                          <div class="modal-body">

                          </div>
                          <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-dark btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" value="Submit">
                          </div>
                          </form>
                          </div>
                          <!-- /.modal-content -->
                      </div>
                    <!-- /.modal-dialog -->
                    </div>
                    {{-- Modal Revoke --}}

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
