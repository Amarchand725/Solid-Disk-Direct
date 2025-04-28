@extends('admin.layouts.app')

@section('title', $title.' -  ' . appName())

@section('content')
@if (request()->is($routeInitialize.'/trashed'))
    <input type="hidden" id="page_url" value="{{ route($routeInitialize.'.trashed') }}">
@else
    <input type="hidden" id="page_url" value="{{ route($routeInitialize.'.index') }}">
@endif
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> {{ $title }}</h4>
                    </div>
                </div>
                @if (request()->is($routeInitialize.'/trashed'))
                    @can($routeInitialize.'-list')
                        <div class="col-md-8">
                            <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                                <a data-toggle="tooltip" data-placement="top" title="Show All Records" href="{{ route($routeInitialize.'.index') }}" class="btn btn-success btn-primary mx-3">
                                    <span>
                                        <i class="ti ti-eye me-0 me-sm-1 ti-xs"></i>
                                        <span class="d-none d-sm-inline-block">View All Records</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    @endcan
                @else
                    @canany([$routeInitialize.'-create', $routeInitialize.'-trashed', $routeInitialize.'-import'])
                        <div class="col-md-8">
                            <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                                <button id="refresh-record" class="btn btn-success mx-2" title="Refresh Records"><i class="ti ti-refresh me-0 ti-xs"></i></button>
                                @can($routeInitialize.'-trashed')
                                    <a data-toggle="tooltip" data-placement="top" title="All Trashed Records" href="{{ route($routeInitialize.'.trashed') }}" class="btn btn-label-danger mx-2">
                                        <span>
                                            <i class="ti ti-trash me-0 me-sm-1 ti-xs"></i>
                                            <span class="d-none d-sm-inline-block">All Trashed Records </span>
                                        </span>
                                    </a>
                                @endcan
                                @can($routeInitialize.'-import')
                                    <button
                                        id="add-btn"
                                        data-toggle="tooltip" data-placement="top" title="Import {{ $singularLabel }}s"
                                        data-url="{{ route($routeInitialize.'.import.store') }}"
                                        data-create-url="{{ route($routeInitialize.'.import.create') }}"
                                        class="btn btn-success add-btn mb-3 mb-md-0 mx-2"
                                        tabindex="0" aria-controls="DataTables_Table_0"
                                        type="button" data-bs-toggle="modal"
                                        data-bs-target="#create-pop-up-modal-for-file">
                                        <span>
                                            <i class="ti ti-file me-0 me-sm-1 ti-xs"></i>
                                            <span class="d-none d-sm-inline-block"> Import {{ $singularLabel }}s </span>
                                        </span>
                                    </button>
                                @endcan
                                @can($routeInitialize.'-create')
                                    <button
                                        id="add-btn"
                                        data-toggle="tooltip" data-placement="top" title="Add {{ $singularLabel }}"
                                        data-url="{{ route($routeInitialize.'.store') }}"
                                        data-create-url="{{ route($routeInitialize.'.create') }}"
                                        class="btn btn-primary add-btn mb-3 mb-md-0 mx-2"
                                        tabindex="0" aria-controls="DataTables_Table_0"
                                        type="button" data-bs-toggle="modal"
                                        data-bs-target="#create-pop-up-modal-for-file">
                                        <span>
                                            <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                            <span class="d-none d-sm-inline-block"> Add {{ $singularLabel }} </span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endcan
                @endif
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
    $('#refresh-record').on('click', function(){
        var page_url = $('#page_url').val();
        var columns =     {!! json_encode($columnsConfig) !!}  // Get columns dynamically from controller
        initializeDataTable(page_url, columns);
    })
</script>
@endpush
