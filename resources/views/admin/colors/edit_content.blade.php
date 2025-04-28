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
                <select id="{{ $name }}" name="{{ $name }}" class="form-control">
                    @foreach($field['options'] ?? [] as $key => $option)  <!-- Safely handle 'options' -->
                        <option value="{{ $key }}" {{ $model->status == $key ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
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
    $('#file-uploader').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Create an image element
                var img = $('<img style="width:30%; height:20%">').attr('src', e.target.result);

                // Display the image preview
                $('#preview').html(img);

                // Add click event handler to the image for zooming
                img.click(function() {
                    $(this).toggleClass('zoomed');
                });
            };

            // Read the image file as a data URL
            reader.readAsDataURL(file);
        } else {
            // Clear the preview area if no file is selected
            $('#preview').html('');
        }
    });
</script>