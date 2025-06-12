@extends('admin.layouts.app')

@section('title', $title.' -  ' . appName())
@section('content')
<input type="hidden" id="page_url" value="{{ route($routeInitialize.'.index') }}">
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> {{ $title }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Users List Table -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="container">
                        <table class="dt-row-grouping table dataTable dtr-column data_table">
                            <thead>
                                <tr>
                                    @foreach($columnsConfig as $columnName)
                                        <th>{{ $columnName['name'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create-pop-up-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h3 class="mb-2" id="modal-label"></h3>
                </div>
                <form method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework" action="" id="create-form" data-modal-id="create-pop-up-modal">
                    @csrf
                    @method('PUT')

                    <span id="edit-content">
                        <div class="mb-3">
                            <label class="form-label">Order Status</label>
                            <select name="status" id="order-status-select" class="form-select" required>
                            @foreach(orderStatus() as $status => $label)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tracking ID</label>
                            <input type="text" name="tracking_id" id="tracking-id-input" placeholder="Enter order tracking ID" class="form-control" />
                        </div>
                    </span>
                    <div class="col-12 mt-3 action-btn">
                        <div class="demo-inline-spacing sub-btn">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                        <div class="demo-inline-spacing loading-btn" style="display: none;">
                            <button class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                            <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                            Loading...
                            </button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<x-modals />
<!--/ Modals -->
@endsection
@push('js')
<script>
    //datatable
    $(document).ready(function(){
        var page_url = $('#page_url').val();
        var columns =     {!! json_encode($columnsConfig) !!}  // Get columns dynamically from controller
        initializeDataTable(page_url, columns);
    })

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.change-status-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const url = this.dataset.statusUrl;
                const currentStatus = this.dataset.currentStatus;
                const trackingId = this.dataset.trackingId || '';

                // Set action and fields
                document.getElementById('change-status-form').action = url;
                document.getElementById('order-status-select').value = currentStatus;
                document.getElementById('tracking-id-input').value = trackingId;
            });
        });
    });
</script>
@endpush
