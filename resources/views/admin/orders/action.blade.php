<div class="d-flex align-items-center">
    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        {{-- Show Order Details (new page) --}}
        @can($routeInitialize.'-show')
            <a href="{{ route($routeInitialize.'.show', $model->id) }}" 
               class="dropdown-item" target="_blank">
                <i class="ti ti-eye me-1"></i> View Details
            </a>
        @endcan

        {{-- Change Status (modal) --}}
        @can($routeInitialize.'-status')
            <a href="javascript:;" 
               class="dropdown-item change-status-btn" 
               data-status-url="{{ route($routeInitialize.'.changeStatus', $model->id) }}"
               data-current-status="{{ $model->order_status }}"
               data-bs-toggle="modal" 
               data-bs-target="#change-status-modal">
                <i class="ti ti-arrows-exchange me-1"></i> Change Status
            </a>
        @endcan

        {{-- Print Invoice --}}
        @can($routeInitialize.'-invoice')
            <a href="{{ route($routeInitialize.'.invoice', $model->id) }}" 
               class="dropdown-item" 
               target="_blank">
                <i class="ti ti-printer me-1"></i> Print Invoice
            </a>
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
