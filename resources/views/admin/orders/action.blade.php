<div class="d-flex align-items-center">
    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        {{-- Show Order Details (new page) --}}
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
                <i class="ti ti-eye me-1"></i> View Details
            </a>
        @endcan

        {{-- Print Invoice --}}
        @can($routeInitialize.'-invoice')
            <a href="{{ route($routeInitialize.'.invoice', $model->id) }}" 
               class="dropdown-item" 
               target="_blank">
                <i class="ti ti-file me-1"></i> Invoice
            </a>
        @endcan

        {{-- Change Status (modal) --}}
        @can($routeInitialize.'-status')
            <button
                data-toggle="tooltip" data-placement="top" title="Change Status {{ $singularLabel }}"
                data-url="{{ route($routeInitialize.'.update', $model->id) }}"
                class="dropdown-item edit-btn change-status-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-current-status="{{ $model->order_status }}"
                data-tracking-id="{{ $model->tracking_id }}"
                data-bs-target="#create-pop-up-modal">
                <i class="ti ti-arrows-exchange me-1"></i> Change Status
            </button>
        @endcan

        {{-- Delete --}}
        @can($routeInitialize.'-delete')
            <a href="javascript:;" 
               class="dropdown-item delete" 
               data-del-url="{{ route($routeInitialize.'.destroy', $model->id) }}">
                <i class="ti ti-trash me-1"></i> Delete
            </a>
        @endcan 
    </div>
</div>
