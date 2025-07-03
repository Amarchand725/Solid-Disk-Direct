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
                title="{{ $singularLabel }} Invoice"
                data-show-url="{{ route($routeInitialize.'.show', $model->id) }}"
                >
                <i class="ti ti-printer me-1"></i> Print Invoice
            </a>
        @endcan

        {{-- Change Status (modal) --}}
        @can($routeInitialize.'-status')
            <button
                data-toggle="tooltip" data-placement="top" title="Change {{ $singularLabel }} Status"
                data-edit-url="{{ route($routeInitialize.'.edit', $model->id) }}"
                data-url="{{ route($routeInitialize.'.update', $model->id) }}"
                class="dropdown-item edit-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal">
                <i class="ti ti-arrows-exchange me-1"></i> Change Status
            </button>
        @endcan

        @can('purchase_orders-create')
            <button
                data-toggle="tooltip" data-placement="top" title="Create Purchase Order"
                data-create-url="{{ route('orders.getOrder', $model->order_number) }}"
                data-url="{{ route('purchase_orders.store') }}"
                class="dropdown-item purchase-order-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-large-for-file">
                <i class="ti ti-file-check"></i> Purchase Order
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
