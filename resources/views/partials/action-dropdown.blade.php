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
        <li>
            <button title="Delete" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-delete{{ $data->id }}">
                <i class="fas fa-trash-alt me-2"></i>Delete
            </button>
        </li>
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
