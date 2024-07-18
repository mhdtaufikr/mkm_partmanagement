@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
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
                                    <h3 class="card-title">List of Historical Problem</h3>
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
 <!-- Modal for Adding Historical Problem and Parts -->
<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-add-label">Add Historical Problem and Parts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="historical-problem-form" action="{{ url('/history/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <label for="no_machine" class="form-label">Machine No</label>
                                <select class="form-control" id="no_machine" name="no_machine" required>
                                    <option value="">-- Select Machine --</option>
                                    @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->op_no }}</option>
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
                            <div>
                                <label for="problem" class="form-label">Problem</label>
                                <textarea class="form-control" id="problem" name="problem" required></textarea>
                            </div>
                            <div>
                                <label for="action" class="form-label">Action</label>
                                <textarea class="form-control" id="action" name="action" required></textarea>
                            </div>
                            <div>
                                <label for="finish_time" class="form-label">Finish Time</label>
                                <input type="time" class="form-control" id="finish_time" name="finish_time" required>
                            </div>
                            <div>
                                <label for="pic" class="form-label">PIC</label>
                                <input type="text" class="form-control" id="pic" name="pic">
                            </div>
                            <div>
                                <label for="img" class="form-label">Image</label>
                                <input type="file" class="form-control" id="img" name="img">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div>
                                <label for="shop" class="form-label">Shop</label>
                                <input type="text" class="form-control" id="shop" name="shop" required>
                            </div>
                            <div>
                                <label for="cause" class="form-label">Cause</label>
                                <textarea class="form-control" id="cause" name="cause" required></textarea>
                            </div>
                            <div>
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div>
                                <label for="balance" class="form-label">Balance</label>
                                <input type="number" class="form-control" id="balance" name="balance" readonly>
                            </div>
                            <div>
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks"></textarea>
                            </div>
                            <div>
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Parts Used</h5>
                    <div id="parts-used-container">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="part_no_1" class="form-label">Part No</label>
                                    <select class="form-control part-no" id="part_no_1" name="part_no[]" onchange="updateStockInfo(1)"></select>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="sap_stock_1" class="form-label">SAP Stock</label>
                                            <input type="number" class="form-control" id="sap_stock_1" name="sap_stock[]" readonly>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="stock_type_1" class="form-label">Stock Type</label>
                                    <select class="form-control stock-type" id="stock_type_1" name="stock_type[]" onchange="handleStockTypeChange(1)">
                                        <option value="sap">SAP</option>
                                        <option value="repair">Repair</option>
                                    </select>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="part_qty_1" class="form-label">Quantity</label>
                                            <input type="number" class="form-control part-qty" id="part_qty_1" name="part_qty[]">
                                        </div>
                                        <div class="col-md-6 repair-part-location" id="repair-part-location_1" style="display: none;">
                                            <label for="repair_location_1" class="form-label">Location</label>
                                            <select class="form-control" id="repair_location_1" name="repair_location[]"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="add-part-button">Add Another Part</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="historical-problem-form">Save changes</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('no_machine').addEventListener('change', function() {
    const machineId = this.value;
    fetch(`/get-parts/${machineId}`)
        .then(response => response.json())
        .then(data => {
            const partsUsedContainer = document.getElementById('parts-used-container');
            const partSelects = partsUsedContainer.querySelectorAll('.part-no');
            partSelects.forEach(select => {
                select.innerHTML = '<option value="">-- Select Part --</option>';
                data.forEach(part => {
                    const option = document.createElement('option');
                    option.value = part.part_id;
                    option.textContent = `${part.part_id} - ${part.critical_part}`;
                    option.dataset.sapStock = part.sap_stock;
                    option.dataset.repairStock = part.repair_stock;
                    select.appendChild(option);
                });
            });
        });
});

document.getElementById('add-part-button').addEventListener('click', function() {
    const partCount = document.querySelectorAll('.part-no').length + 1;
    const partDiv = document.createElement('div');
    partDiv.classList.add('mb-3');
    partDiv.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <label for="part_no_${partCount}" class="form-label">Part No</label>
                <select class="form-control part-no" id="part_no_${partCount}" name="part_no[]" onchange="updateStockInfo(${partCount})"></select>
                <div class="row">
                    <div class="col-md-12">
                        <label for="sap_stock_${partCount}" class="form-label">SAP Stock</label>
                        <input type="number" class="form-control" id="sap_stock_${partCount}" name="sap_stock[]" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="stock_type_${partCount}" class="form-label">Stock Type</label>
                <select class="form-control stock-type" id="stock_type_${partCount}" name="stock_type[]" onchange="handleStockTypeChange(${partCount})">
                    <option value="sap">SAP</option>
                    <option value="repair">Repair</option>
                </select>
                <div class="row">
                    <div class="col-md-6">
                        <label for="part_qty_${partCount}" class="form-label">Quantity</label>
                        <input type="number" class="form-control part-qty" id="part_qty_${partCount}" name="part_qty[]">
                    </div>
                    <div class="col-md-6 repair-part-location" id="repair-part-location_${partCount}" style="display: none;">
                        <label for="repair_location_${partCount}" class="form-label">Location</label>
                        <select class="form-control" id="repair_location_${partCount}" name="repair_location[]"></select>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('parts-used-container').appendChild(partDiv);
    const machineId = document.getElementById('no_machine').value;
    if (machineId) {
        fetch(`/get-parts/${machineId}`)
            .then(response => response.json())
            .then(data => {
                const partSelect = document.getElementById(`part_no_${partCount}`);
                partSelect.innerHTML = '<option value="">-- Select Part --</option>';
                data.forEach(part => {
                    const option = document.createElement('option');
                    option.value = part.part_id;
                    option.textContent = `${part.part_id} - ${part.critical_part}`;
                    option.dataset.sapStock = part.sap_stock;
                    option.dataset.repairStock = part.repair_stock;
                    partSelect.appendChild(option);
                });
            });
    }
});

function updateStockInfo(partCount) {
    const partSelect = document.getElementById(`part_no_${partCount}`);
    const selectedOption = partSelect.options[partSelect.selectedIndex];
    const sapStock = selectedOption.dataset.sapStock || 0;
    const repairStock = selectedOption.dataset.repairStock || 0;

    document.getElementById(`sap_stock_${partCount}`).value = sapStock;
    document.getElementById(`repair_stock_${partCount}`).value = repairStock;
}

function handleStockTypeChange(partCount) {
    const stockTypeSelect = document.getElementById(`stock_type_${partCount}`);
    const repairPartLocationDiv = document.getElementById(`repair-part-location_${partCount}`);
    if (stockTypeSelect.value === 'repair') {
        repairPartLocationDiv.style.display = 'block';
        const partNo = document.getElementById(`part_no_${partCount}`).value;
        console.log(`Fetching repair locations for part no: ${partNo}`);
        if (partNo) {
            fetch(`/get-repair-locations/${partNo}`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.error || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Repair locations data:', data);
                    const locationSelect = document.getElementById(`repair_location_${partCount}`);
                    locationSelect.innerHTML = '<option value="">-- Select Location --</option>';
                    data.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.sloc;
                        option.textContent = `${location.sloc} - ${location.repaired_qty} qty`;
                        option.dataset.repairedQty = location.repaired_qty;
                        locationSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching repair locations:', error);
                    alert(`Error fetching repair locations: ${error.message}`);
                });
        }
    } else {
        repairPartLocationDiv.style.display = 'none';
    }
}




function updateRepairStockInfo(partCount) {
    const locationSelect = document.getElementById(`repair_location_${partCount}`);
    const selectedOption = locationSelect.options[locationSelect.selectedIndex];
    const repairedQty = selectedOption.dataset.repairedQty || 0;
    document.getElementById(`repair_stock_${partCount}`).value = repairedQty;
}

document.getElementById('start_time').addEventListener('change', calculateBalance);
document.getElementById('finish_time').addEventListener('change', calculateBalance);

function calculateBalance() {
    const startTime = document.getElementById('start_time').value;
    const finishTime = document.getElementById('finish_time').value;

    if (startTime && finishTime) {
        const start = new Date(`1970-01-01T${startTime}:00`);
        const finish = new Date(`1970-01-01T${finishTime}:00`);
        const diff = (finish - start) / 1000 / 60 / 60; // Difference in hours

        document.getElementById('balance').value = diff;
    }
}

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
                                                    <td>{{ $data->no_machine }}</td>
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
                                                                        <td>{{ $part->part->material }}</td>
                                                                        <td>{{ $part->part->material_description }}</td>
                                                                        <td>{{ $part->qty }}</td>
                                                                        <td>{{ $part->location }}</td>
                                                                        <td>{{ $part->routes }}</td>
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
