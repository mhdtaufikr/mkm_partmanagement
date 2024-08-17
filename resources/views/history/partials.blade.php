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
            <img src="{{ asset($data->img) }}" class="img-fluid" alt="Problem Image">
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
