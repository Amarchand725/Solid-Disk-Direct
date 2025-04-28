@method('PUT')
<input type="hidden" name="pre_menu" value="{{ $model->menu }}">
@foreach($fields as $name => $field)
    @if($name != 'created_at' && $name != 'fields')
        <div class="col-12 mb-3">
            <label class="form-label" for="{{ $name }}">
                {{ $field['label'] ?? ucfirst($name) }}
                @if(isset($field['required']) && $field['required'])
                    <span class="text-danger">*</span>  <!-- Display * if required and not file type -->
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
                            <option value="{{ $menuGroup->id }}" {{ $model->menu_group==$menuGroup->id?'selected':'' }}>{{ $menuGroup->menu }}</option>
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
            @else
                @if($name=='menu')
                    <input 
                        type="{{ $field['type'] ?? 'text' }}" 
                        class="form-control" 
                        placeholder="{{ $field['placeholder'] ?? '' }}" 
                        value="{{ old($name, $field['value'] ?? '') }}" 
                        disabled
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

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
    $('#add-more-btn').on('click', function(){
        var html = '';
        html = '<div class="row mt-2">'+
                    '<div class="col-sm-3">'+
                        '<select name="types[]" id="type" class="form-control">'+
                            '<option value="" selected>Choose type</option>';
                            @foreach (fieldTypes() as $key=>$fieldType)
                                html += '<option value="{{ $key }}" >{{ $fieldType }}</option>';
                            @endforeach
                        html += '</select>'+
                    '</div>'+
                    '<div class="col-sm-3">'+
                        '<select name="input_types[]" id="input_type" class="form-control">'+
                            '<option value="" selected>Choose input type</option>';
                            @foreach (inputTypes() as $inputKey=>$inputType)
                                html += '<option value="{{ $inputKey }}" >{{ $inputType }}</option>';
                            @endforeach
                        html += '</select>'+
                    '</div>'+
                    '<div class="col-sm-5">'+
                        '<input '+
                            'type="text" '+
                            'id="fields" '+
                            'name="fields[]" '+
                            'class="form-control" '+
                            'placeholder="Enter field name" '+
                        '/>'+
                    '</div>'+
                    '<div class="col-sm-1">'+
                        '<button type="button" class="btn btn-danger remove-btn" id="remove-btn">'+
                            '<i class="fa fa-times"></i>'+
                        '</button>'+
                    '</div>'+
                '</div>';
        $('#add-more-content').append(html);
    })

    // Use event delegation for dynamically added remove buttons
    $(document).on('click', '.remove-btn', function(){
        $(this).closest('.row').remove();
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