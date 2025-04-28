@extends('admin.layouts.master')
@push('title', $title)

@push('styles')
@endpush

@section('content')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="flex-grow-1">
                <div class="card mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-header">
                                <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span>
                                    {{ $title ?? 'Log Details' }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-item-center mt-4">
                                <div class="dt-buttons btn-group flex-wrap">
                                    <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-primary mx-3"
                                        data-toggle="tooltip" data-placement="top" title="List of Pre-Employees"
                                        tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                        <span>
                                            <i class="ti ti-arrow-left me-0 me-sm-1 ti-xs"></i>
                                            <span class="d-none d-sm-inline-block">Back</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md mb-4 mb-md-2">
                        <div class="accordion accordion-b mt-3" id="accordionExample">
                            <!--Manager-->
                            <div class="card accordion-item mb-4">
                                <h2 class="accordion-header py-2 fw-bold" id="headingThree">
                                    <button type="button" class="accordion-button show" data-bs-toggle="collapse"
                                        data-bs-target="#managerDetail" aria-expanded="false" aria-controls="managerDetail">
                                        <h5 class="m-0 fw-bold text-dark">Log Details</h5>
                                    </button>
                                </h2>
                                <div id="managerDetail" class="accordion-collapse show" aria-labelledby="headingThree"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="datatable mb-3">
                                            <div class="table-responsive custom-scrollbar table-view-responsive">
                                                <table class="table table-striped table-responsive custom-table ">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Action By</th>
                                                            <th scope="col">Action Type</th>
                                                            <th scope="col">Action Model</th>
                                                            <th scope="col">Remarks</th>
                                                            <th scope="col">IP</th>
                                                            <th scope="col">Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                @if(!empty($model->hasActionUser->name))
                                                                    {{ $model->hasActionUser->name }} ( {{ getRole($model->hasActionUser->id) }} )
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {!! actionLabel($model->action) !!}
                                                            </td>
                                                            <td>{{ $className }}</td>
                                                            <td>{{ $model->description }}</td>
                                                            <td>{{ $model->ip_address }}</td>
                                                            <td>
                                                                @if(!empty($model->created_at))
                                                                    {{ newDateFormat($model->created_at) ?? '-' }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"><strong>Details Data</strong></td>
                                                        </tr>
                                                        @if($model->action=='update') <!-- For update -->
                                                            <tr>
                                                                <th colspan="2">Columns</th>
                                                                <th colspan="2">Old Data</th>
                                                                <th colspan="2">New Data</th>
                                                            </tr>
                                                            @php $data = json_decode($model->changed_fields, true); @endphp
                                                            @foreach ($data as $key => $item)
                                                                <tr>
                                                                    <th colspan="2"><strong>{{ $key }}</strong></th>
                                                                    <td colspan="2">
                                                                        @if($key=='status')
                                                                            {!! statusBadge($item['old']) !!}
                                                                        @elseif($key == 'updated_at')
                                                                            {{ newDateFormat($item['old']) ?? '-' }}
                                                                        @elseif($key=='password')
                                                                            {{ '-' }}
                                                                        @else
                                                                            {{ $item['old'] }}
                                                                        @endif
                                                                    </td>
                                                                    <td colspan="2">
                                                                        @if($key=='status')
                                                                            {!! statusBadge($item['new']) !!}
                                                                        @elseif($key == 'updated_at')
                                                                            {{ newDateFormat($item['new']) ?? '-' }}
                                                                        @elseif($key=='password')
                                                                            {{ '-' }}
                                                                        @else
                                                                            {{ $item['new'] }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @elseif($model->action=='show_column') <!-- For show specific column data -->
                                                            <tr>
                                                                <th colspan="2">Columns</th>
                                                                <th colspan="4">Viewed </th>
                                                            </tr>
                                                            @php
                                                                $columnData = json_decode($model->extra_details, true);
                                                            @endphp
                                                            <tr>
                                                                <th colspan="2"><strong>{{ Str::upper($columnData['column_name']) ?? '-' }}</strong></th>
                                                                <td colspan="4">{{ $columnData['column_value'] ?? '-' }}</td>
                                                            </tr>
                                                        @elseif($model->action=='downloaded-document') <!-- For downloading document or file -->
                                                            <tr>
                                                                <th colspan="2">Columns</th>
                                                                <th colspan="4">Document </th>
                                                            </tr>
                                                            @php
                                                                $columnData = json_decode($model->extra_details, true);
                                                            @endphp
                                                            <tr>
                                                                <th colspan="2"><strong>{{ Str::upper($columnData['column_name']) ?? '-' }}</strong></th>
                                                                <td colspan="4">
                                                                    @if(!empty($columnData['column_value']))
                                                                        <a href="{{ asset($columnData['document_path'].'/'.$columnData['column_value']) }}"
                                                                            download class="btn btn-info"
                                                                            title="{{ $columnData['column_value'] }}"
                                                                            style="display: flex; align-items: center; gap: 5px;">
                                                                            <i class="fa fa-download"></i>
                                                                            Download
                                                                        </a>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <th colspan="2">Columns</th>
                                                                <th colspan="4">Data</th>
                                                            </tr>
                                                            @if(isset($modelData) && !empty($modelData))
                                                                @php
                                                                    $recordArray = $modelData->toArray();
                                                                    $excludeKeys = ['id', 'slug', 'updated_at', 'deleted_at'];

                                                                    if ($className == 'User') {
                                                                        $excludeKeys[] = 'email_verified_at'; // Add the key dynamically to the array
                                                                        $excludeKeys[] = 'password'; // Add the key dynamically to the array
                                                                        $excludeKeys[] = 'remember_token'; // Add the key dynamically to the array
                                                                    }

                                                                    $filteredData = array_diff_key($recordArray, array_flip($excludeKeys));
                                                                @endphp
                                                                @foreach ($filteredData as $key => $item)
                                                                    <tr>
                                                                        <th colspan="2"><strong>{{ $key }}</strong></th>
                                                                        <td colspan="4">
                                                                            @if($key=='status')
                                                                                {!! statusBadge($item) !!}
                                                                            @elseif($key=='created_at')
                                                                                {{ newDateFormat($item) }}
                                                                            @else
                                                                                {{ $item }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="6">Record not found</td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
