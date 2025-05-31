@method('PUT')
<div class="col-12 mb-3">
    <label class="form-label" for="exist_permission_group">Exist Permission Group</label>
    <select class="form-select" name="exist_permission_group" id="exist_permission_group">
        <option value="" selected>Select permission group</option>
        @foreach ($models as $model)
            @if($model->id != $group_permission->id)
                <option value="{{ $model->label }}">{{ ucwords($model->label) }}</option>
            @endif
        @endforeach
    </select>
    <span id="exist_permission_group_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-3">
    <label class="form-label" for="new_permission_group">Permission Group</label>
    <input type="text" id="new_permission_group" value="{{ $group_permission->label }}" name="new_permission_group" class="form-control" placeholder="Enter new permission group" autofocus />
    <span id="new_permission_group_error" class="text-danger error"></span>
</div>
@if(count($otherPermissions) > 0)
    @foreach($otherPermissions as $otherPermission)
        <div class="col-12 mb-3">
            <label class="form-label" for="custom_permission">Custom Permission </label>
            <input type="text" id="custom_permission" name="custom_permissions[]" value="{{ $otherPermission }}" class="form-control" placeholder="Enter custom permision name" autofocus />
            <span id="custom_permission_error" class="text-danger error"></span>
        </div>
    @endforeach
@else
    <div class="col-12 mb-3">
        <label class="form-label" for="custom_permission">Custom Permission </label>
        <input type="text" id="custom_permission" name="custom_permissions[]" class="form-control" placeholder="Enter custom permision name" autofocus />
        <span id="custom_permission_error" class="text-danger error"></span>
    </div>
@endif
<div class="col-12 mb-2">
    <div class="card-body border-top p-9">
        <label class="form-label" for="permissions">Check Permissions </label>
        <div class="col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="all" id="checkAll"/>
                <label class="form-check-label" for="checkAll"> <strong>All</strong> </label>
            </div>
        </div>

        @foreach (subPermissionFields() as $sub_permission_field)
            <div class="col-lg-3 mt-2">
                <div class="form-check">
                    @if (in_array($sub_permission_field, $permissions))
                        <input class="form-check-input" name="permissions[]" type="checkbox" value="{{ $sub_permission_field }}" id="{{ $sub_permission_field }}" checked/>
                    @else
                        <input class="form-check-input" name="permissions[]" type="checkbox" value="{{ $sub_permission_field }}" id="{{ $sub_permission_field }}"/>
                    @endif
                    <label class="form-check-label" for="{{ $sub_permission_field }}"> <strong>{{ Str::ucfirst($sub_permission_field) }}</strong></label>
                </div>
            </div>
        @endforeach
        <span id="permissions_error" class="text-danger error"></span>
    </div>
</div>

<script>
    $("#checkAll").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
