<!-- resources/views/partials/change-status-modal.blade.php -->
<div class="modal fade" id="changeStatusModal{{ $data->id }}" tabindex="-1" aria-labelledby="changeStatusModalLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel{{ $data->id }}">Add Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('checksheet/change-status') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_pm" value="{{ $data->id }}">
                    <input type="hidden" name="checksheet_id" value="{{ $data->checksheet_id }}">
                    <div class="mb-3">
                        <label for="shift" class="form-label">Shift</label>
                        <select class="form-select" id="shift" name="shift" required>
                            <option value="Day">Day</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input value="{{ date('Y-m-d') }}" type="date" class="form-control" id="date" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
