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
                    @if($name=='customer' && isset($model->hasCustomer) && !empty($model->hasCustomer))
                        {{ $model->hasCustomer->name ?? '-' }}
                    @else
                        {{ $field['value'] }}
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
</table>
