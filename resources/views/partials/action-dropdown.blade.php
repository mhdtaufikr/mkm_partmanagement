<!-- resources/views/partials/action-dropdown.blade.php -->
<div class="dropdown">
    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $data->id }}" data-bs-toggle="dropdown" aria-expanded="false">
        Actions
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $data->id }}">
        <li>
            <a title="Detail" class="dropdown-item" href="checksheet/detail/{{ encrypt($data->id) }}">
                <i class="fas fa-info me-2"></i>Detail
            </a>
        </li>
        @php
        $userRole = auth()->user()->role; // Assuming you have 'role' field in the user model
    @endphp

    @if($userRole == 'Leader' || $userRole == 'IT')
    <li>
        <button title="Delete" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
            <i class="fas fa-trash-alt me-2"></i>Delete
        </button>
    </li>
    @endif


        @if($data->status == 1)
            <li>
                <a href="checksheet/checkher/{{ encrypt($data->id) }}" class="dropdown-item" title="Check">
                    <i class="fas fa-search me-2"></i>Check
                </a>
            </li>
        @elseif($data->status == 0)
            <li>
                <a href="checksheet/fill/{{ encrypt($data->id) }}" class="dropdown-item" title="Fill">
                    <i class="fas fa-pencil-alt me-2"></i>Fill
                </a>
            </li>
        @elseif($data->status == 2)
            <li>
                <a href="checksheet/approve/{{ encrypt($data->id) }}" class="dropdown-item" title="Approve">
                    <i class="fas fa-thumbs-up me-2"></i>Approve
                </a>
            </li>
        @elseif($data->status == 3)
            <li>
                <a href="checksheet/update/{{ encrypt($data->id) }}" class="dropdown-item" title="Update">
                    <i class="fas fa-pencil-alt me-2"></i>Update
                </a>
            </li>
        @else
            <li>
                <a href="checksheet/generate-pdf/{{ encrypt($data->id) }}" class="dropdown-item" title="Generate PDF">
                    <i class="fas fa-file-pdf me-2"></i>Generate PDF
                </a>
            </li>
        @endif

        @if($data->pm_status == 'Not Good' || $data->pm_status == 'Temporary')
        <li><hr class="dropdown-divider"></li>
        <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeStatusModal{{ $data->id }}" title="Change Status">
                <i class="fas fa-exchange-alt me-2"></i>Change Status to OK
            </button>
        </li>
    @endif


        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#journeyModal{{ $data->id }}">
                <i class="fas fa-history me-2"></i>Approval Route
            </a>
        </li>

        @if($data->status_logs->isNotEmpty())
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusLogModal{{ $data->id }}">
                <i class="fas fa-history me-2"></i>View Status Log
            </a>
        </li>
        @endif
    </ul>
</div>
 <!-- Delete Confirmation Modal -->
 <div class="modal fade" id="modal-delete{{ $data->id }}" tabindex="-1" aria-labelledby="modalDeleteLabel{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel{{ $data->id }}">Confirm Deletion {{$data->op_name}} - {{$data->line}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this record {{$data->op_name}} - {{$data->line}}?
            </div>
            <div class="modal-footer">
                <form action="{{ route('checksheet.delete', $data->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
