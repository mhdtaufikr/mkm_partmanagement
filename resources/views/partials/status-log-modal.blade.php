<!-- resources/views/partials/status-log-modal.blade.php -->

@if($data->logStatus)
    <div class="modal fade" id="statusLogModal{{ $data->id }}" tabindex="-1" aria-labelledby="modal-detail-label-{{ $data->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-detail-label-{{ $data->id }}">Detail of Historical Problem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Machine No:</strong> {{ $data->logStatus->machine->no_machine }}</h6>
                            <h6><strong>Date:</strong> {{ $data->logStatus->date }}</h6>
                            <h6><strong>Shift:</strong> {{ $data->logStatus->shift }}</h6>
                            <h6><strong>Shop:</strong> {{ $data->logStatus->shop }}</h6>
                            <h6><strong>Problem:</strong> {{ $data->logStatus->problem }}</h6>
                            <h6><strong>Cause:</strong> {{ $data->logStatus->cause }}</h6>
                            <h6><strong>Action:</strong> {{ $data->logStatus->action }}</h6>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Start Time:</strong> {{ $data->logStatus->start_time }}</h6>
                            <h6><strong>Finish Time:</strong> {{ $data->logStatus->finish_time }}</h6>
                            <h6><strong>Balance:</strong> {{ $data->logStatus->balance }} Hour</h6>
                            <h6><strong>PIC:</strong> {{ $data->logStatus->pic }}</h6>
                            <h6><strong>Remarks:</strong> {{ $data->logStatus->remarks }}</h6>
                            <h6><strong>Status:</strong> {{ $data->logStatus->status }}</h6>
                        </div>
                    </div>
                    @if($data->logStatus->img)
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <img src="{{ asset($data->logStatus->img) }}" class="img-fluid" alt="Problem Image" style="max-width: 400px; max-height: 300px;">
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
                                @foreach ($data->logStatus->spareParts as $part)
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif
