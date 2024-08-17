<!-- resources/views/partials/journey-modal.blade.php -->
<div class="modal fade" id="journeyModal{{ $data->id }}" tabindex="-1" aria-labelledby="journeyModalLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="journeyModalLabel{{ $data->id }}">Checksheet Journey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($data->logs->isEmpty())
                    <p>No journey logs available for this checksheet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Remark</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->user->name }}</td>
                                    <td>
                                        @if($log->action == 0)
                                            <span class="badge bg-primary">Update</span>
                                        @elseif($log->action == 1)
                                            <span class="badge bg-info">Check</span>
                                        @elseif($log->action == 2)
                                            <span class="badge bg-warning">Waiting Approval</span>
                                        @elseif($log->action == 3)
                                            <span class="badge bg-danger">Remand</span>
                                        @elseif($log->action == 4)
                                            <span class="badge bg-success">Done</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown Status</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->remark }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
