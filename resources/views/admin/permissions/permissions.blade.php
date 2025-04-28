<u>
@foreach(SubPermissions($model->label) as $label)
    <li><span class="badge bg-label-primary me-1 my-1"> {{ $label->name }}</span></li>
@endforeach
</u>
