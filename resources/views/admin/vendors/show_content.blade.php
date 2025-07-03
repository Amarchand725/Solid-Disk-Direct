<table class="table table-flush-spacing">
    @foreach($fields as $name => $field)
        <tr>
            <td class="text-nowrap fw-semibold">{{ $field['label'] ?? ucfirst($name) }}</td>
            <td>
                @if($field['type'] === 'file')
                    @if(!empty($field['value']))
                        <img src="{{ asset('storage/' . $field['value']) }}" width="80" class="zoomable">
                    @else
                        -
                    @endif
                @elseif($name === 'status')
                    <span class="badge bg-label-{{ $model->status ? 'success' : 'danger' }}">
                        {{ $model->status ? 'Active' : 'Deactive' }}
                    </span>
                @elseif($name=='country' && isset($model->getCountry) && !empty($model->getCountry))
                    {{ $model->getCountry->name ?? '-' }}
                @elseif($name=='state' && isset($model->getState) && !empty($model->getState))
                    {{ $model->getState->name ?? '-' }}
                @elseif($name=='city' && isset($model->getCity) && !empty($model->getCity))
                    {{ $model->getCity->name ?? '-' }}
                @else
                    {{ $field['value'] }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
