<?php echo method_field('PUT'); ?>

<div class="mb-4 p-3 border rounded bg-light shadow-sm">
  <div class="mb-2">
    <span class="text-muted fw-semibold">Order No#:</span>
    <span class="fw-bold text-dark"><?php echo e($model->order_number); ?></span>
  </div>
</div>

<div class="mb-3">
    <label class="form-label">Order Status <span class="text-danger">*</span></label>
    <select name="order_status" id="order_status" class="form-select" required>
    <?php $__currentLoopData = orderStatus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($status); ?>" <?php echo e($model->order_status==$status?'selected':''); ?>><?php echo e($data['label']); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <span id="order_status_error" class="text-danger error"></span>
</div>

<div class="mb-3">
    <label class="form-label">Shipping Method </label>
    <select name="shipping_method" id="shipping_method" style="width: 300px;">
        <option value="" selected>Select shipping Method</option>
        <?php $__currentLoopData = shippingMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$shippingMethod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php echo e($model->shipping_method==$shippingMethod?'selected':''); ?>><?php echo e($shippingMethod); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <input type="text" name="tracking_number" id="tracking_number" value="<?php echo e($model->tracking_number); ?>" placeholder="Enter order tracking ID" class="form-control" />
    <span id="tracking_number_error" class="text-danger error"></span>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/edit_content.blade.php ENDPATH**/ ?>