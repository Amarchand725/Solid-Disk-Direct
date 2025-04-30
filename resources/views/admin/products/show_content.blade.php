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
                @elseif($field['type'] === 'checkbox')
                    <span class="badge bg-label-{{ $model->status ? 'success' : 'danger' }}">
                        {{ $model->status ? 'Yes' : 'No' }}
                    </span>
                @else
                    @if($name=='brand' && isset($model->hasBrand) && !empty($model->hasBrand))
                        {{ $model->hasBrand->name ?? '-' }}
                    @elseif($name=='category' && isset($model->categories) && !empty($model->categories))                        
                    {!! $model->categories->pluck('name')->implode('<span class="highlight-arrow"> &rarr; </span>') !!}
                    @elseif($name=='unit' && isset($model->hasUnit) && !empty($model->hasUnit))
                        {{ $model->hasUnit->name ?? '-' }}
                    @elseif($name=='tax_type' && isset($model->hasTaxType) && !empty($model->hasTaxType))
                        {{ $model->hasTaxType->name ?? '-' }}
                    @elseif($name=='condition' && isset($model->hasProductCondition) && !empty($model->hasProductCondition))
                        {{ $model->hasProductCondition->name ?? '-' }}
                    @elseif($name=='unit_price' || $name=='discount_price')
                        {{ currency() }}{!! $field['value'] ?? '-' !!}
                    @else
                        {!! $field['value'] ?? '-' !!}
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    @if(isset($model->hasProductImages) && !empty($model->hasProductImages))
        <tr>
            <td class="text-nowrap fw-semibold" colspan="2">Additional Images</td>
        </tr>
        <tr>
            <td colspan="2">
                @if(isset($model->hasProductImages) && !empty($model->hasProductImages))
                    <div id="existing-images" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach ($model->hasProductImages as $productImage)
                            <div class="preview-wrapper" data-id="{{ $productImage->id }}">
                                <img src="{{ asset('storage/'.$productImage->image) }}" width="80" height="80" class="preview-image zoomable" alt="">
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
    @endif
</table>
