<div class="d-flex align-items-center">
    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        @can($routeInitialize.'-show')
            <a href="#"
                class="dropdown-item show"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#details-modal"
                data-toggle="tooltip"
                data-placement="top"
                title="{{ $singularLabel }} Details"
                data-show-url="{{ route($routeInitialize.'.show', $model->id) }}"
                >
                View Details
            </a>
        @endcan
        @can($routeInitialize.'-edit')
            <button
                data-toggle="tooltip" data-placement="top" title="Edit {{ $singularLabel }}"
                data-edit-url="{{ route($routeInitialize.'.edit', $model->id) }}"
                data-url="{{ route($routeInitialize.'.update', $model->id) }}"
                class="dropdown-item edit-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-for-file">
                Edit
            </button>
        @endcan
        @can($routeInitialize.'-delete')
            <a href="javascript:;" class="dropdown-item delete" data-del-url="{{ route($routeInitialize.'.destroy', $model->id) }}">Delete</a>
        @endcan
    </div>
</div>
