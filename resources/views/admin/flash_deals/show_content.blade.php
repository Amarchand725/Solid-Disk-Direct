<table class="table table-flush-spacing">
    @foreach($fields as $name => $field)
        <tr>
            <td class="text-nowrap fw-semibold">{{ $field['label'] ?? ucfirst($name) }}</td>
            <td>
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
                @else
                    @if($name=='background_color' || $name=='text_color')
                        <div style="width: 30px; height: 30px; background-color: {{ $field['value'] }}; border: 1px solid #ccc; border-radius: 4px;"></div>
                    @else
                        {{ $field['value'] }}
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
</table>
