@extends('admin.layouts.master')
@push('title', $title ?? 'Users')
@section('content')
    <input type="hidden" id="page_url" value="{{ route('logs.views-downloads') }}">
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
                <div class="container-fluid">
                    <table class="datatables-users table border-top dataTable no-footer dtr-column data_table table-responsive" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="width: 1227px;">
                        <thead>
                            <tr>
                                <th>S.No#</th>
                                <th>Action by user</th>
                                <th>Action Type</th>
                                <th>Target Type</th>
                                <th>Target Record</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th style="width: 100px">Created at</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody id="body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- details modal --}}
    <div class="modal fade" id="details-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2" id="modal-label"></h3>
                    </div>

                    <div class="col-12">
                        <span id="show-content"></span>
                    </div>

                    <div class="col-12 mt-3 text-end">
                        <button
                            type="reset"
                            class="btn btn-label-primary btn-reset"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- details modal --}}
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            loadTable();
        });
        function loadTable(){
            var table = $('.data_table').DataTable();
            if ($.fn.DataTable.isDataTable('.data_table')) {
                table.destroy();
            }
            var page_url = $('#page_url').val();
            var table = $('.data_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: page_url+"?loaddata=yes",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'action_type',
                        name: 'action_type'
                    },
                    {
                        data: 'target_type',
                        name: 'target_type'
                    },
                    {
                        data: 'target_id',
                        name: 'target_id'
                    },
                    {
                        data: 'details',
                        name: 'details'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    /*{
                        data: 'action',
                        name: 'action'
                    },*/
                ]
            });
        }
    </script>
@endpush
