@method('PUT')
@foreach($fields as $name => $field)
    @if($name != 'created_at')
        <div class="col-12 mb-3">
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
                @if($name=='country' && !empty(countries()))
                    <select id="country" name="{{ $name }}" class="form-control country" data-url="{{ route('get-states') }}">
                        <option value="" selected>Select {{ $name }}</option>
                        @foreach(countries() as $country) 
                            <option value="{{ $country->name }}" {{ old($name, $field['value']) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                @elseif($name=='state')
                    <select id="state" name="{{ $name }}" class="form-control" data-url="{{ route('get-cities') }}">
                        <option value="" selected>Select {{ $name }}</option>
                        @if(isset($states) && !empty($states))
                            @foreach($states as $state) 
                                <option value="{{ $state->name }}" {{ old($name, $field['value']) == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                @elseif($name=='city')
                    <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                        <option value="" selected>Select {{ $name }}</option>
                        @if(isset($cities) && !empty($cities))
                            @foreach($cities as $city) 
                                <option value="{{ $city->name }}" {{ old($name, $field['value']) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                @else
                    <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                        @foreach($field['options'] ?? [] as $key => $option)  <!-- Safely handle 'options' -->
                            <option value="{{ $key }}" {{ $model->status == $key ? 'selected' : '' }}>
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
                    class="form-control uploader" 
                    autofocus
                />

                <span id="preview-{{ $name }}">
                    @if(!empty($field['value']))
                        <img src="{{ asset('storage/' . $field['value']) }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                    @endif
                </span>
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

    $('#country').on('change', function(){
        var url = $(this).attr('data-url');
        var country = $(this).val();
        $('#city').html('');
        $.ajax({
            url: url,
            data:{country:country},
            method: 'GET',
            success: function (response) {
                if(response){
                    let options = '<option value="">Select State</option>';
                    response.forEach(state => {
                        options += `<option value="${state.name}">${state.name}</option>`;
                    });
                    $('#state').html(options);
                }
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    // Handle permission error
                    toastr.error('You do not have permission to access this resource.');
                } else {
                    // Handle other errors
                    toastr.error('An error occurred. Please try again later.');
                }
            }    
        });
    })
    $('#state').on('change', function(){
        var url = $(this).attr('data-url');
        var state = $(this).val();
        $.ajax({
            url: url,
            data:{state:state},
            method: 'GET',
            success: function (response) {
                if(response){
                    let options = '<option value="">Select City</option>';
                    response.forEach(state => {
                        options += `<option value="${state.name}">${state.name}</option>`;
                    });
                    $('#city').html(options);
                }
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    // Handle permission error
                    toastr.error('You do not have permission to access this resource.');
                } else {
                    // Handle other errors
                    toastr.error('An error occurred. Please try again later.');
                }
            }    
        });
    })
</script>