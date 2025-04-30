@method('PUT')

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
                        @if(isset($field['type']) && $field['type'] == 'file' && empty($field['value']))
                            <span class="text-danger">*</span>  <!-- Display * if file type and value is empty -->
                        @elseif($field['type'] != 'file')
                            <span class="text-danger">*</span>  <!-- Display * if required and not file type -->
                        @endif
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
                            <option value="" selected>Select {{ $name }}</option>
                            @foreach($parent_categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ collect(old($name, $model->categories->pluck('id')->toArray()))->contains($category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <span id="category-container">
                            @php $counter = 0 @endphp 
                            @foreach ($categoriesData as $parentCategories)
                                @php $counter++ @endphp 
                                <div class="col-12 mt-3 sub-category-container">
                                    <label class="form-label" for="sub-category-{{ $counter }}">Sub Category</label>
                                    <select 
                                        id="sub-category-{{ $counter }}" 
                                        name="categories[]" 
                                        class="category-select form-control category" 
                                        data-level="{{ $counter }}" 
                                        data-url="{{ route('categories.sub-categories') }}" 
                                        data-category-level="sub-category"
                                    >
                                        <option value="" selected>Select {{ $name }}</option>
                                        @foreach($parentCategories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}"
                                                {{ collect(old($name, $model->categories->pluck('id')->toArray()))->contains($parentCategory->id) ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </span>
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
                    <textarea id="{{ $name }}" name="{{ $name }}" class="form-control" placeholder="{{ $field['placeholder'] ?? '' }}">{{ old($name, $field['value'] ?? '') }}</textarea>
                @elseif(isset($field['type']) && $field['type'] === 'file')
                    <input 
                        type="{{ $field['type'] ?? 'file' }}" 
                        id="file-uploader" 
                        name="{{ $name }}" 
                        accept="{{ isset($field['accept']) ? $field['accept'] : '' }}"
                        class="form-control" 
                        autofocus
                    />

                    <span id="preview">
                        @if(!empty($field['value']))
                            <img src="{{ asset('storage/' . $field['value']) }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                        @endif
                    </span>
                @elseif (isset($field['type']) && $field['type'] === 'checkbox')
                    <input 
                        type="{{ $field['type'] }}" 
                        id="{{ $name }}" 
                        name="{{ $name }}" 
                        value="1" @if($field['value']==1) checked @endif 
                    />
                @else
                    @if($name=='unit_price')
                        <input 
                            type="{{ $field['type'] ?? 'text' }}" 
                            id="{{ $name }}" 
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
        @if(isset($model->hasProductImages) && !empty($model->hasProductImages))
            <div id="existing-images" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;">
                @foreach ($model->hasProductImages as $productImage)
                    <div class="preview-wrapper" data-id="{{ $productImage->id }}">
                        <img src="{{ asset('storage/'.$productImage->image) }}" width="80" height="80" class="preview-image" alt="">
                        <span class="remove-existing deleteImage" data-del-url="{{ route('products.remove.image', $productImage->id) }}" data-id="{{ $productImage->id }}">&#10006;</span>
                    </div>
                @endforeach
            </div>
        @endif
                
        <div id="image-preview" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;"></div>
        <span id="images_error" class="text-danger error"></span>
    </div>
</div>
<script src="{{ asset('admin') }}/custom/multi-categories.js"></script>
<script>
    CKEDITOR.replace('short_description');
    CKEDITOR.replace('full_description');

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    document.getElementById('images').addEventListener('change', function(event) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = ''; // Clear previous

        const files = Array.from(event.target.files);

        files.forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '6px';
                img.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';

                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '&times;';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '-5px';
                removeBtn.style.right = '-5px';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.background = 'red';
                removeBtn.style.color = 'white';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '20px';
                removeBtn.style.height = '20px';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';
                removeBtn.style.fontSize = '14px';

                removeBtn.onclick = function() {
                    wrapper.remove();
                    // Optional: remove file from input (see below)
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    });
</script>