<div class="col-12 col-md-12">
    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" value="{{ old('description', $model->name) }}" class="form-control" placeholder="Enter provider name" />
    <div class="fv-plugins-message-container invalid-feedback"></div>
    <span id="name_error" class="text-danger error"></span>
</div>
<div class="col-12 col-md-12 mt-2">
    <label class="form-label" for="description">Description ( <small>Optional</small> )</label>
    <textarea class="form-control" rows="5" name="details" placeholder="Enter description">{{ old('description', $model->descripiton) }}</textarea>
</div>
<div class="col-12 col-md-12 mt-2">
    <label class="form-label" for="status">Status</label>
    <select class="form-control" name="status" id="status">
        <option value="1" {{ $model->status==1?'selected':'' }}>Active</option>
        <option value="0" {{ $model->status==0?'selected':'' }}>De-Active</option>
    </select>
</div>
<script>
    $('.form-select').select2();
</script>