<table class="table table-flush-spacing">
    @foreach($fields as $name => $field)
        <tr>
            <td class="text-nowrap fw-semibold">{{ $field['label'] ?? ucfirst($name) }}</td>
            <td>
                @if($field['type'] === 'file')
                    @if(!empty($field['value']))
                        <img src="{{ asset('storage/' . $field['value']) }}" width="80">
                    @endif
                @elseif($name === 'status')
                    <span class="badge bg-label-{{ $model->status ? 'success' : 'danger' }}">
                        {{ $model->status ? 'Active' : 'Deactive' }}
                    </span>
                @elseif($name === 'fields')
                    @php $tableFields = json_decode($field['value'], true) @endphp 
                    <table class="table">
                        <tr>
                            <th>Field Name</th>
                            <th>Data Type</th>
                            <th>Input Type</th>
                        </tr>
                        @if(isset($tableFields) && !empty($tableFields))
                            @foreach ($tableFields as $tableKey=>$tableField)
                                <tr>
                                    <td>{{ ucfirst($tableField['field']) ?? '' }}</td>
                                    <td>{{ $tableField['type'] ?? '' }}</td>
                                    <td>{{ $tableField['input_type'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                @else
                    @if($name=='menu_group' && isset($model->hasMenuGroup) && !empty($model->hasMenuGroup))
                        {{ $model->hasMenuGroup->menu ?? '-' }}
                    @elseif($name=='icon')
                        <i class="menu-icon tf-icons {{ $model->icon ?? 'ti ti-smart-home' }}"></i>
                    @else
                        {!! $field['value'] ?? '-' !!}
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
</table>
