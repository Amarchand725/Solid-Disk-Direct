<table class="table table-flush-spacing" style="table-layout: fixed; width: 100%;">
    @foreach($fields as $name => $field)
        <tr>
            <td class="text-nowrap fw-semibold" style="width: 100px;">{{ $field['label'] ?? ucfirst($name) }}</td>
            <td style="max-width: 200px; word-break: break-word; white-space: normal; overflow-wrap: break-word; overflow: hidden;">
                @if($field['type'] === 'file')
                    @if(!empty($field['value']))
                        <img src="{{ asset('storage/' . $field['value']) }}" width="80">
                    @else
                        -
                    @endif
                @elseif($name === 'status')
                    <span class="badge bg-label-{{ $model->status ? 'success' : 'danger' }}">
                        {{ $model->status ? 'Active' : 'Deactive' }}
                    </span>
                @elseif($field['type'] === 'checkbox')
                    <span class="badge bg-label-{{ $model->status ? 'success' : 'danger' }}">
                        {{ $model->status ? 'Yes' : 'No' }}
                    </span>
                @else
                    @if($name=='parent' && isset($model->parents) && !empty($model->parents))                        
                        {!! $model->parents->pluck('name')->implode('<span class="highlight-arrow"> &rarr; </span>') !!}
                    @else
                        {!! $field['value'] !!}
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
</table>