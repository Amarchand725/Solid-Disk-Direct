<div class="row">
    @foreach($fields as $name => $field)
        @if($name != 'created_at')
            @if($name=='brand' || $name=='category' || $name=='thumbnail' || isset($field['type']) && $field['type'] === 'textarea')
                <div class="col-12 mb-3">
            @else
                <div class="col-6 mb-3">
            @endif
                <label class="form-label" for="{{ $name }}">
                    {{ $field['label'] ?? ucfirst($name) }}
                    @if(isset($field['required']) && $field['required']) 
                        <span class="text-danger">*</span>  <!-- Display * if required -->
                    @endif
                </label>

                @if(isset($field['type']) && $field['type'] === 'select')
                    @if($name=='brand' && isset($brands) && !empty($brands))
                        <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                            <option value="" selected>Select {{ $name }}</option>
                            @foreach($brands as $brand) 
                                <option value="{{ $brand->id }}" {{ old($name, $field['value']) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($name=='category' && isset($parent_categories) && !empty($parent_categories))
                        <select id="{{ $name }}" name="categories[]" data-url="{{ route('categories.sub-categories') }}" data-category-level="parent" data-level="0" class="form-control category category-select">
                            <option value="" selected>Select parent category</option>
                            @foreach($parent_categories as $key => $parent_category)
                                <option value="{{ $parent_category->id }}">
                                    {{ $parent_category->name }}
                                </option>
                            @endforeach
                        </select>
                        <span id="category-container"></span>
                    @elseif($name=='unit' && isset($units) && !empty($units))
                        <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                            <option value="" selected>Select {{ $name }}</option>
                            @foreach($units as $unit) 
                                <option value="{{ $unit->id }}" {{ old($name, $field['value']) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($name=='tax_type' && isset($tax_types) && !empty($tax_types))
                        <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                            <option value="" selected>Select {{ $name }}</option>
                            @foreach($tax_types as $tax_type) 
                                <option value="{{ $tax_type->id }}" {{ old($name, $field['value']) == $tax_type->id ? 'selected' : '' }}>
                                    {{ $tax_type->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($name=='condition' && isset($product_conditions) && !empty($product_conditions))
                        <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                            <option value="" selected>Select {{ $name }}</option>
                            @foreach($product_conditions as $condition) 
                                <option value="{{ $condition->id }}" {{ old($name, $field['value']) == $condition->id ? 'selected' : '' }}>
                                    {{ $condition->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($name=='tags' && isset($tags) && !empty($tags))
                        <select id="tags" name="tags[]" multiple class="form-control tags"></select>
                    @else
                        <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                            @foreach($field['options'] ?? [] as $key => $option)  <!-- Safely handle 'options' -->
                                <option value="{{ $key }}" {{ old($name, $field['value']) == $key ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                @elseif(isset($field['type']) && $field['type'] === 'textarea')
                    <textarea id="{{ $name }}" name="{{ $name }}" class="form-control summernote" placeholder="{{ $field['placeholder'] ?? '' }}">{{ old($name, $field['value'] ?? '') }}</textarea>
                @elseif(isset($field['type']) && $field['type'] === 'file')
                    <input 
                        type="{{ $field['type'] ?? 'text' }}" 
                        id="file-uploader" 
                        name="{{ $name }}" 
                        accept="{{ isset($field['accept']) ? $field['accept'] : '' }}" 
                        class="form-control uploader" 
                        placeholder="{{ $field['placeholder'] ?? '' }}" 
                        value="{{ old($name, $field['value'] ?? '') }}" 
                        autofocus
                    />

                    <span id="preview-{{ $name }}">
                        @if(!empty($field['value']))
                            <img src="{{ asset('storage/' . $field['value']) }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                        @endif
                    </span>
                @elseif (isset($field['type']) && $field['type'] === 'checkbox')
                    <input 
                        type="{{ $field['type'] }}" 
                        id="{{ $name }}" 
                        name="{{ $name }}" 
                        value="1" 
                    />
                @else
                    @if($name=='unit_price')
                        <input 
                            type="{{ $field['type'] ?? 'text' }}" 
                            id="{{ $name }}" 
                            step="0.01"
                            name="{{ $name }}" 
                            class="form-control numeric" 
                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                            value="{{ old($name, $field['value'] ?? '') }}" 
                            autofocus
                        />
                    @else
                        <input 
                            type="{{ $field['type'] ?? 'text' }}" 
                            id="{{ $name }}" 
                            name="{{ $name }}" 
                            class="form-control" 
                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                            value="{{ old($name, $field['value'] ?? '') }}" 
                            autofocus
                        />
                    @endif
                @endif

                <span id="{{ $name }}_error" class="text-danger error"></span>
            </div>
        @endif
    @endforeach

    <div class="col-12 mb-3">
        <label class="form-label" for="images">
            Additional Images
        </label>
        <input type="file" accept="image/*" class="form-control" multiple name="images[]" id="images">
        <div id="image-preview" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;"></div>
        <span id="images_error" class="text-danger error"></span>
    </div>
</div>
<script src="{{ asset('admin') }}/custom/product.js"></script> 
<script>
    $('#short_description').summernote({
        height: 200
    });
    
    $('#full_description').summernote({
        height: 200
    });

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    setupImagePreview('images', 'image-preview');
</script>