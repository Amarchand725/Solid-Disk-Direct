@if(count($menusWithoutPermissions) > 0)
    <div class="col-12 mb-3">
        <label class="form-label" for="new_permissions">
            <strong><u>New Permission Group(s):</u></strong>
        </label>
        <br />
        <span id="" class="text-danger">
            @foreach ($menusWithoutPermissions as $menusWithoutPermission)
                {{ ucfirst(Str::kebab(Str::plural($menusWithoutPermission))) }},
            @endforeach
        </span>
        <hr>
    </div>
@endif
<div class="col-12 mb-3">
    <label class="form-label" for="exist_permission_group">Exist Permission Group</label>
    <select class="form-select" name="exist_permission_group" id="exist_permission_group">
        <option value="" selected>Select permission group</option>
        @foreach ($models as $model)
            <option value="{{ $model->label }}">{{ ucwords($model->label) }}</option>
        @endforeach
    </select>
    <span id="exist_permission_group_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-3">
    <label class="form-label" for="new_permission_group">New Permission Group</label>
    <input type="text" id="new_permission_group" name="new_permission_group" class="form-control" placeholder="Enter new permission group" autofocus />
    <span id="new_permission_group_error" class="text-danger error"></span>
</div>

<div class="col-12 mb-3">
    <label class="form-label" for="custom_permission">Custom Permission </label>
    <input type="text" id="custom_permission" name="custom_permission" class="form-control" placeholder="Enter custom permision name" autofocus />
    <span id="custom_permission_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-2">
    <div class="card-body border-top p-9">
        <label class="form-label" for="permissions">Check Permissions </label>
        <!-- Default checkbox -->
        <div class="col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="all" id="checkAll"/>
                <label class="form-check-label" for="checkAll"> <strong>All</strong> </label>
            </div>
        </div>
        @foreach (subPermissionFields() as $key=>$sub_permission_field)
            <div class="col-lg-3 mt-2">
                <div class="form-check">
                    <input class="form-check-input" name="permissions[]" type="checkbox" value="{{ $key }}" id="{{ $key }}"/>
                    <label class="form-check-label" for="{{ $key }}"> <strong>{{ $sub_permission_field }}</strong></label>
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