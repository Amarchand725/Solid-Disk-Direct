<table class="table table-bordered table-striped">
    <tr>
        <th>Name</th>
        <td>
            <span class="text-primary fw-semibold">{{ $model->name }}</span>
        </td>
    </tr>
    <tr>
        <th>Description</th>
        <td>{!! $model->description??'-' !!}</td>
    </tr>
    <tr>
        <th>Created At</th>
        <td>{{ date('d F Y', strtotime($model->created_at)) }}</td>
    </tr>
</table>
