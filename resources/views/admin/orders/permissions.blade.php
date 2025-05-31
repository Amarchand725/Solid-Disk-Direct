<td>
    <ul class="d-flex flex-wrap list-unstyled m-0" style="max-width: 500px; overflow-x: auto;">
        @foreach(SubPermissions($model->label) as $label)
            <li class="me-1 my-1">
                <span class="badge bg-label-primary">{{ $label->name }}</span>
            </li>
        @endforeach
    </ul>
</td>
