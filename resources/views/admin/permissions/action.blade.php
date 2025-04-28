<button
    data-toggle="tooltip" data-placement="top" title="Edit {{ $singularLabel }}"
    data-edit-url="{{ route($routeInitialize.'.edit', $model->id) }}"
    data-url="{{ route($routeInitialize.'.update', $model->id) }}"
    class="btn btn-primary btn-sm mb-3 mb-md-0 mx-3 edit-btn"
    tabindex="0" aria-controls="DataTables_Table_0"
    type="button" data-bs-toggle="modal"
    data-bs-target="#create-pop-up-modal">
    <i class="fa fa-edit"></i>
</button>

<a href="javascript:;"
    data-toggle="tooltip" data-placement="top" title="Delete {{ $singularLabel }}"
    class="btn btn-danger btn-sm delete"
    data-del-url="{{ route($routeInitialize.'.destroy', $model->id) }}">
    <i class="fa fa-trash"></i>
</a>

