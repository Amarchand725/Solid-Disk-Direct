@method('PUT')

<div class="mb-4 p-3 border rounded bg-light shadow-sm">
  <div class="mb-2">
    <span class="text-muted fw-semibold">Order No#:</span>
    <span class="fw-bold text-dark">{{ $model->order_number }}</span>
  </div>
</div>

<div class="mb-3">
    <label class="form-label">Order Status <span class="text-danger">*</span></label>
    <select name="order_status" id="order_status" class="form-select" required>
    @foreach(orderStatus() as $status => $data)
        <option value="{{ $status }}" {{ $model->order_status==$status?'selected':'' }}>{{ $data['label'] }}</option>
    @endforeach
    </select>
    <span id="order_status_error" class="text-danger error"></span>
</div>

<div class="mb-3">
    <label class="form-label">Shipping Method </label>
    <select name="shipping_method" id="shipping_method" style="width: 300px;">
        <option value="" selected>Select shipping Method</option>
        @foreach (shippingMethods() as $key=>$shippingMethod)
            <option value="{{ $key }}" {{ $model->shipping_method==$shippingMethod?'selected':'' }}>{{ $shippingMethod }}</option>
        @endforeach
    </select>
    <span id="shipping_method_error" class="text-danger error"></span>
</div>

<div class="mb-3">
    <label class="form-label">Custom Shipping</label>
    <input type="text" name="custom_shipping_method" id="custom_shipping_method" value="" placeholder="Enter custom shipping method" class="form-control" />
    <span id="custom_shipping_method_error" class="text-danger error"></span>
</div>

<div class="mb-3">
    <label class="form-label">Tracking ID <span class="text-danger">*</span></label>
    <input type="text" name="tracking_number" id="tracking_number" value="{{ $model->tracking_number }}" placeholder="Enter order tracking ID" class="form-control" />
    <span id="tracking_number_error" class="text-danger error"></span>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
