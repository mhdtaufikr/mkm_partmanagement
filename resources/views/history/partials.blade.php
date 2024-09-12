<div class="row mb-3">
    <div class="col-md-6">
        <h6><strong>Machine No:</strong> {{ $data->machine->op_no }}</h6>
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
            <img src="{{ asset($data->img) }}" class="img-fluid" alt="Problem Image" style="max-width: 400px; max-height: 300px;">
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
                    <td>{{ $part->part->material ?? 'N/A' }}</td>
                    <td>{{ $part->part->material_description ?? 'N/A' }}</td>
                    <td>{{ $part->qty ?? 'N/A' }}</td>
                    <td>{{ $part->location ?? 'N/A' }}</td>
                    <td>{{ $part->routes ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Parent Record -->
@if($parent)
    <hr>
    <h5 class="mb-3">Parent Report (Open)</h5>
    <div class="row mb-3">
        <div class="col-md-6">
            <h6><strong>Machine No:</strong> {{ $parent->machine->op_no }}</h6>
            <h6><strong>Date:</strong> {{ $parent->date }}</h6>
            <h6><strong>Shift:</strong> {{ $parent->shift }}</h6>
            <h6><strong>Shop:</strong> {{ $parent->shop }}</h6>
            <h6><strong>Problem:</strong> {{ $parent->problem }}</h6>
            <h6><strong>Cause:</strong> {{ $parent->cause }}</h6>
            <h6><strong>Action:</strong> {{ $parent->action }}</h6>
        </div>
        <div class="col-md-6">
            <h6><strong>Start Time:</strong> {{ $parent->start_time }}</h6>
            <h6><strong>Finish Time:</strong> {{ $parent->finish_time }}</h6>
            <h6><strong>Balance:</strong> {{ $parent->balance }} Hour</h6>
            <h6><strong>PIC:</strong> {{ $parent->pic }}</h6>
            <h6><strong>Remarks:</strong> {{ $parent->remarks }}</h6>
            <h6><strong>Status:</strong> {{ $parent->status }}</h6>
        </div>
    </div>

    @if($parent->img)
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <img src="{{ asset($parent->img) }}" class="img-fluid" alt="Parent Problem Image" style="max-width: 400px; max-height: 300px;">
            </div>
        </div>
    @endif

    <hr>
    <h5 class="mb-3">Spare Parts Used (Parent)</h5>
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
                @foreach ($parent->spareParts as $part)
                    <tr>
                        <td>{{ $part->part->material ?? 'N/A' }}</td>
                        <td>{{ $part->part->material_description ?? 'N/A' }}</td>
                        <td>{{ $part->qty ?? 'N/A' }}</td>
                        <td>{{ $part->location ?? 'N/A' }}</td>
                        <td>{{ $part->routes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- Child Record -->
@if($latestChild)
    <hr>
    <h5 class="mb-3">Child Report (Latest)</h5>
    <div class="row mb-3">
        <div class="col-md-6">
            <h6><strong>Machine No:</strong> {{ $latestChild->machine->op_no }}</h6>
            <h6><strong>Date:</strong> {{ $latestChild->date }}</h6>
            <h6><strong>Shift:</strong> {{ $latestChild->shift }}</h6>
            <h6><strong>Shop:</strong> {{ $latestChild->shop }}</h6>
            <h6><strong>Problem:</strong> {{ $latestChild->problem }}</h6>
            <h6><strong>Cause:</strong> {{ $latestChild->cause }}</h6>
            <h6><strong>Action:</strong> {{ $latestChild->action }}</h6>
        </div>
        <div class="col-md-6">
            <h6><strong>Start Time:</strong> {{ $latestChild->start_time }}</h6>
            <h6><strong>Finish Time:</strong> {{ $latestChild->finish_time }}</h6>
            <h6><strong>Balance:</strong> {{ $latestChild->balance }} Hour</h6>
            <h6><strong>PIC:</strong> {{ $latestChild->pic }}</h6>
            <h6><strong>Remarks:</strong> {{ $latestChild->remarks }}</h6>
            <h6><strong>Status:</strong> {{ $latestChild->status }}</h6>
        </div>
    </div>

    @if($latestChild->img)
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <img src="{{ asset($latestChild->img) }}" class="img-fluid" alt="Child Problem Image" style="max-width: 400px; max-height: 300px;">
            </div>
        </div>
    @endif

    <hr>
    <h5 class="mb-3">Spare Parts Used (Child)</h5>
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
                @foreach ($latestChild->spareParts as $part)
                    <tr>
                        <td>{{ $part->part->material ?? 'N/A' }}</td>
                        <td>{{ $part->part->material_description ?? 'N/A' }}</td>
                        <td>{{ $part->qty ?? 'N/A' }}</td>
                        <td>{{ $part->location ?? 'N/A' }}</td>
                        <td>{{ $part->routes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
