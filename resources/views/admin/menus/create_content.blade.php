@foreach($fields as $name => $field)
    @if($name != 'created_at')
        <div class="col-12 mb-3">
            <label class="form-label" for="{{ $name }}">
                {{ $field['label'] ?? ucfirst($name) }}
                @if(isset($field['required']) && $field['required']) 
                    <span class="text-danger">*</span>  <!-- Display * if required -->
                @endif
            </label>

            @if(isset($field['type']) && $field['type'] === 'select' || $name=='menu_group' || $name=='icon')
                @if($name=='icon')
                    <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                        <option value="" selected>Select Icon</option>
                        @foreach (getTabIcons() as $tabIcon)
                            <option value="{{ $tabIcon }}" {{ $model->icon==$tabIcon?'selected':'' }}>
                                <i class="{!! $tabIcon !!}"></i> {{ $tabIcon }}
                            </option>
                        @endforeach
                    </select>
                    <div id="icon-preview" style="margin-top: 10px; font-size: 24px;">
                        @if($model->icon)
                            <i class="{{ $model->icon }}"></i>
                        @endif
                    </div>
                @elseif($name=='menu_group')
                    <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                        <option value="" selected>Select Menu Group</option>
                        @foreach ($menuGroups as $menuGroup)
                            <option value="{{ $menuGroup->id }}">{{ $menuGroup->menu }}</option>
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
                    type="{{ $field['type'] ?? 'text' }}" 
                    id="file-uploader" 
                    name="{{ $name }}" 
                    accept="{{ isset($field['accept']) ? $field['accept'] : '' }}" 
                    class="form-control" 
                    placeholder="{{ $field['placeholder'] ?? '' }}" 
                    value="{{ old($name, $field['value'] ?? '') }}" 
                    autofocus
                />

                <span id="preview">
                    @if(!empty($field['value']))
                        <img src="{{ asset('storage/' . $field['value']) }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                    @endif
                </span>
            @elseif(isset($name) && $name === 'fields')
                <div class="row">
                    <div class="col-sm-4">
                        <input 
                            type="{{ $field['type'] ?? 'text' }}" 
                            id="{{ $name }}" 
                            name="{{ $name }}[]" 
                            class="form-control" 
                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                            value="" 
                            autofocus
                        />
                    </div>
                    <div class="col-sm-3">
                        <select name="types[]" id="type" class="form-control">
                            <option value="" selected>Choose data type</option>
                            @foreach (fieldTypes() as $key=>$fieldType)
                                <option value="{{ $key }}" >{{ $fieldType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select name="input_types[]" id="input_type" class="form-control">
                            <option value="" selected>Choose input type</option>
                            @foreach (inputTypes() as $inputKey=>$inputType)
                                <option value="{{ $inputKey }}" >{{ $inputType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success" id="add-more-btn">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <span id="add-more-content"></span>
            @else
                @if($name=='icon')
                    <input 
                        type="{{ $field['type'] ?? 'text' }}" 
                        id="{{ $name }}" 
                        name="{{ $name }}" 
                        class="form-control" 
                        placeholder="{{ $field['placeholder'] ?? '' }}" 
                        value="{{ old($name, $field['value'] ?? '') }}" 
                        autofocus
                    />
                    <div class="input-group-append">
                        <a href="https://tabler-icons.io/" target="_blank" class="btn btn-success mt-2">Browse Icons</a>
                    </div>
                    <small class="form-text text-muted">
                        Go to <a href="https://tabler-icons.io/" target="_blank">tabler-icons.io</a>, copy the icon name like <code>ti ti-smart-home</code>, and paste it here.
                    </small>
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

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
    $('#add-more-btn').on('click', function(){
        // var html = '';
        // html = '<div class="row mt-2">'+
        //             '<div class="col-sm-3">'+
        //                 '<select name="types[]" id="type" class="form-control">'+
        //                     '<option value="" selected>Choose type</option>';
        //                     @foreach (fieldTypes() as $key=>$fieldType)
        //                         html += '<option value="{{ $key }}" >{{ $fieldType }}</option>';
        //                     @endforeach
        //                 html += '</select>'+
        //             '</div>'+
        //             '<div class="col-sm-3">'+
        //                 '<select name="input_types[]" id="input_type" class="form-control">'+
        //                     '<option value="" selected>Choose input type</option>';
        //                     @foreach (inputTypes() as $inputKey=>$inputType)
        //                         html += '<option value="{{ $inputKey }}" >{{ $inputType }}</option>';
        //                     @endforeach
        //                 html += '</select>'+
        //             '</div>'+
        //             '<div class="col-sm-5">'+
        //                 '<input '+
        //                     'type="text" '+
        //                     'id="fields" '+
        //                     'name="fields[]" '+
        //                     'class="form-control" '+
        //                     'placeholder="Enter field name" '+
        //                 '/>'+
        //             '</div>'+
        //             '<div class="col-sm-1">'+
        //                 '<button type="button" class="btn btn-danger remove-btn" id="remove-btn">'+
        //                     '<i class="fa fa-times"></i>'+
        //                 '</button>'+
        //             '</div>'+
        //         '</div>';
        
        let html = '<div class="row mt-2 dynamic-block">'+
            '<div class="col-sm-4">'+
                '<input type="text" name="fields[]" class="form-control" placeholder="Enter field name"/>'+
            '</div>'+
            '<div class="col-sm-3">'+
                '<select name="types[]" class="form-control">'+
                    '<option value="" selected>Choose type</option>';
                    @foreach (fieldTypes() as $key=>$fieldType)
                        html += '<option value="{{ $key }}">{{ $fieldType }}</option>';
                    @endforeach
        html += '</select>'+
            '</div>'+
            '<div class="col-sm-2">'+
                '<select name="input_types[]" class="form-control">'+
                    '<option value="" selected>Choose input type</option>';
                    @foreach (inputTypes() as $inputKey=>$inputType)
                        html += '<option value="{{ $inputKey }}">{{ $inputType }}</option>';
                    @endforeach
        html += '</select>'+
            '</div>'+
            
            '<div class="col-sm-3 d-flex justify-content-end gap-1">'+
                '<button type="button" class="btn btn-secondary move-up"><i class="fa fa-arrow-up"></i></button>'+
                '<button type="button" class="btn btn-secondary move-down"><i class="fa fa-arrow-down"></i></button>'+
                '<button type="button" class="btn btn-danger remove-btn"><i class="fa fa-times"></i></button>'+
            '</div>'+
        '</div>';

        $('#add-more-content').append(html);
    })

    // Use event delegation for dynamically added remove buttons
    // $(document).on('click', '.remove-btn', function(){
    //     $(this).closest('.row').remove();
    // });

    // Move up
    $(document).on('click', '.move-up', function () {
        const current = $(this).closest('.dynamic-block');
        current.prev('.dynamic-block').before(current);
    });

    // Move down
    $(document).on('click', '.move-down', function () {
        const current = $(this).closest('.dynamic-block');
        current.next('.dynamic-block').after(current);
    });

    // Remove block
    $(document).on('click', '.remove-btn', function () {
        $(this).closest('.dynamic-block').remove();
    });
</script>
<script>
    $(document).ready(function () {
        // If using Select2:
        $('#icon').on('change', function () {
            const selectedIcon = $(this).val();
            $('#icon-preview').html(`<i class="${selectedIcon}"></i>`);
        });
    });
</script>