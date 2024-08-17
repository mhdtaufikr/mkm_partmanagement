 <!-- Modal for Daily Report -->
 <div class="modal fade" id="modal-detail-{{ $data->data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detail-label-{{ $data->data->id }}">Detail of Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Machine No:</strong> {{ $data->data->machine->op_no }}</h6>
                        <h6><strong>Date:</strong> {{ $data->data->date }}</h6>
                        <h6><strong>Shift:</strong> {{ $data->data->shift }}</h6>
                        <h6><strong>Shop:</strong> {{ $data->data->shop }}</h6>
                        <h6><strong>Problem:</strong> {{ $data->data->problem }}</h6>
                        <h6><strong>Cause:</strong> {{ $data->data->cause }}</h6>
                        <h6><strong>Action:</strong> {{ $data->data->action }}</h6>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Start Time:</strong> {{ $data->data->start_time }}</h6>
                        <h6><strong>Finish Time:</strong> {{ $data->data->finish_time }}</h6>
                        <h6><strong>Balance:</strong> {{ $data->data->balance }} Hour</h6>
                        <h6><strong>PIC:</strong> {{ $data->data->pic }}</h6>
                        <h6><strong>Remarks:</strong> {{ $data->data->remarks }}</h6>
                        <h6><strong>Status:</strong> {{ $data->data->status }}</h6>
                    </div>
                </div>
                @if($data->data->img)
                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <img src="{{ asset( $data->data->img) }}" class="img-fluid" alt="Problem Image">
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
                            @foreach ($data->data->spareParts as $part)
                            <tr>
                                <td>{{ $part->part->material ?? null}}</td>
                                <td>{{ $part->part->material_description  ?? null}}</td>
                                <td>{{ $part->qty  ?? null}}</td>
                                <td>{{ $part->location  ?? null}}</td>
                                <td>{{ $part->routes ?? null }}</td>
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
