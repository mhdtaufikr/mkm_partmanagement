@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                 <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                                Daily Report
                        </h1>
                        <div class="page-header-subtitle">Manage Daily Report</div>
                    </div>
                    {{-- <div class="col-12 col-xl-auto mt-4">Optional page header content</div> --}}
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header">
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Daily Report</h3>
                                </div>

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
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <button type="button" class="btn btn-dark btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add">
                                                <i class="fas fa-plus-square"></i>
                                            </button>
                                        </div>
                                    </div>
<!-- Modal for Adding Daily Report -->
<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-label">Add Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="historical-problem-form" action="{{ url('/history/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <label for="line" class="form-label">Line</label>
                                <select class="form-control" id="line" name="line" required>
                                    <option value="">-- Select Line --</option>
                                    @foreach($lines as $line)
                                        <option value="{{ $line->line }}">{{ $line->line }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="shift" class="form-label">Shift</label>
                                <select class="form-control" id="shift" name="shift" required>
                                    <option value="Day">Day</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="no_machine" class="form-label">Machine No (Op No)</label>
                                <select class="form-control" id="no_machine" name="no_machine" required>
                                    <option value="">-- Select Machine --</option>
                                </select>
                            </div>
                            <div>
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" form="historical-problem-form">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('line').addEventListener('change', function() {
        const line = this.value;
        fetch(`/get-op-nos/${line}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                const noMachineSelect = document.getElementById('no_machine');
                noMachineSelect.innerHTML = '<option value="">-- Select Machine --</option>';
                data.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.id;
                    option.textContent = machine.op_no;
                    noMachineSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching machine data:', error);
                alert('Error fetching machine data: ' + error.message);
            });
    });
</script>


                                    <div class="table-responsive">
                                        <table id="tablehistory" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Machine No</th>
                                                    <th>Date</th>
                                                    <th>Shift</th>
                                                    <th>Shop</th>
                                                    <th>Problem</th>
                                                    <th>Cause</th>
                                                    <th>Action</th>
                                                    <th>Start Time</th>
                                                    <th>Finish Time</th>
                                                    <th>Balance</th>
                                                    <th>PIC</th>
                                                    <th>Remarks</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($items as $data)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $data->machine->op_no }}</td>
                                                    <td>{{ $data->date }}</td>
                                                    <td>{{ $data->shift }}</td>
                                                    <td>{{ $data->shop }}</td>
                                                    <td>{{ $data->problem }}</td>
                                                    <td>{{ $data->cause }}</td>
                                                    <td>{{ $data->action }}</td>
                                                    <td>{{ $data->start_time }}</td>
                                                    <td>{{ $data->finish_time }}</td>
                                                    <td>{{ $data->balance }} Hour</td>
                                                    <td>{{ $data->pic }}</td>
                                                    <td>{{ $data->remarks }}</td>
                                                    <td>{{ $data->status }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-{{ $data->id }}">Detail</button>
                                                    </td>
                                                </tr>


                                                @endforeach
                                            </tbody>
                                        </table>
                                        @foreach ($items as $data)
                                        <!-- Modal for showing spare parts used in historical problem -->
                                        <div class="modal fade" id="modal-detail-{{ $data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modal-detail-label-{{ $data->id }}">Detail of Historical Problem</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6><strong>Machine No:</strong> {{ $data->no_machine }}</h6>
                                                                <h6><strong>Date:</strong> {{ $data->date }}</h6>
                                                                <h6><strong>Shift:</strong> {{ $data->shift }}</h6>
                                                                <h6><strong>Shop:</strong> {{ $data->shop }}</h6>
                                                                <h6><strong>Problem:</strong> {{ $data->problem }}</h6>
                                                                <h6><strong>Cause:</strong> {{ $data->cause }}</h6>
                                                                <h6><strong>Action:</strong> {{ $data->action }}</h6>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><strong>Start Time:</strong> {{ $data->start_time }}</h6>
                                                                <h6><strong>Finish Time:</strong> {{ $data->finish_time }}</h6>
                                                                <h6><strong>Balance:</strong> {{ $data->balance }} Hour</h6>
                                                                <h6><strong>PIC:</strong> {{ $data->pic }}</h6>
                                                                <h6><strong>Remarks:</strong> {{ $data->remarks }}</h6>
                                                                <h6><strong>Status:</strong> {{ $data->status }}</h6>
                                                            </div>
                                                        </div>
                                                        @if($data->img)
                                                        <div class="row mb-3">
                                                            <div class="col-md-12 text-center">
                                                                <img src="{{ asset( $data->img) }}" class="img-fluid" alt="Problem Image">
                                                            </div>
                                                        </div>
                                                        @endif
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
                                                                    @foreach ($data->spareParts as $part)
                                                                    <tr>
                                                                        <td>{{ $part->part->material ?? null }}</td>
                                                                        <td>{{ $part->part->material_description ?? null}}</td>
                                                                        <td>{{ $part->qty ?? null}}</td>
                                                                        <td>{{ $part->location ?? null}}</td>
                                                                        <td>{{ $part->routes?? null }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

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
        var table = $("#tablehistory").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
@endsection
