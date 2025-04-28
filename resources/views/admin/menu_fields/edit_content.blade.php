@method('PUT')
<input type="hidden" name="pre_menu" value="{{ $menu->menu }}">
<div class="row">
    <!-- Vertical Icons Wizard -->
    <div class="col-12 mb-4">
      <small class="text-light fw-semibold">Menu Fields</small>
      <div class="bs-stepper vertical wizard-vertical-icons-example mt-2">
        <div class="bs-stepper-header">
            @foreach($fields as $name => $field)
                <div class="step" data-target="#{{ $field['name'] }}">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="ti ti-file-description"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ $field['label'] ?? '' }}</span>
                            <span class="bs-stepper-subtitle">Setup {{ $field['label'] ?? '' }} </span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>
            @endforeach
        </div>
        <div class="bs-stepper-content">
            @foreach($fields as $name => $field)
                <div id="{{ $field['name'] }}" class="content">
                    <div class="content-header mb-3">
                        <h6 class="mb-0">{{ $field['label'] ?? ucfirst($name) }}</h6>
                        <small>Enter {{ $field['label'] ?? $name }} Settings.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-12">
                            {{-- Data Type dropdown --}}
                            <div class="mb-3">
                                <label>Data Type</label>
                                <select name="fields[{{ $field['name'] }}][type]" class="form-select w-full">
                                    @foreach (fieldTypes() as $key=>$fieldType)
                                        <option value="{{ $key }}" {{ $field['type']==$key ? 'selected' : '' }}>{{ $fieldType }}</option>
                                    @endforeach
                                    {{-- Add more as needed --}}
                                </select>
                            </div>

                            {{-- Input Type dropdown --}}
                            <div class="mb-3">
                                <label>Input Type</label>
                                <select name="fields[{{ $field['name'] }}][input_type]" class="form-select w-full">
                                    @foreach (inputTypes() as $key=>$inputType)
                                        <option value="{{ $key }}" {{ $field['input_type']==$key ? 'selected' : '' }}>{{ $inputType }}</option>
                                    @endforeach
                                    {{-- Add more as needed --}}
                                </select>
                            </div>

                            {{-- Label input --}}
                            <div class="mb-3">
                                <label>Label</label>
                                <input type="text" name="fields[{{ $field['name'] }}][label]" value="{{ $field['label'] }}" class="form-control w-full">
                            </div>

                            {{-- Placeholder input --}}
                            <div class="mb-3">
                                <label>Placeholder</label>
                                <input type="text" name="fields[{{ $field['name'] }}][placeholder]" value="{{ $field['placeholder'] }}" class="form-control w-full">
                            </div>

                            {{-- Checkboxes --}}
                            @foreach (['required', 'index_visible', 'create_visible', 'edit_visible', 'show_visible'] as $attr)
                                <div class="mb-2">
                                    <label>
                                        <input type="checkbox" name="fields[{{ $field['name'] }}][{{ $attr }}]" value="1" {{ !empty($field[$attr]) ? 'checked' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $attr)) }}
                                    </label>
                                </div>
                            @endforeach

                            {{-- Extra textarea --}}
                            <div class="mb-3">
                                <label>Extra (JSON)</label>
                                <textarea name="fields[{{ $field['name'] }}][extra]" placeholder="{'validation':'max:255'}" class="form-control w-full" rows="3">{{ $field['extra'] }}</textarea>
                            </div>
                        </div>
                        {{-- <div class="col-12 d-flex justify-content-between">
                            <button class="btn btn-label-secondary btn-prev" type="button">
                                <i class="ti ti-arrow-left me-sm-1"></i>
                                <span class="align-middle d-sm-inline-block d-none">Previous</span>
                            </button>
                            <button class="btn btn-primary btn-next" type="button">
                                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                <i class="ti ti-arrow-right"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>
            @endforeach
        </div>
      </div>
    </div>
    <!-- /Vertical Icons Wizard -->
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    window.stepper = new Stepper(document.querySelector('.wizard-vertical-icons-example'), {
        linear: false,
        animation: true
    });

    // Bind step navigation buttons
    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', function () {
            window.stepper.next();
        });
    });

    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', function () {
            window.stepper.previous();
        });
    });
</script>