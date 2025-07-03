<?php if(isset($orders) && !empty($orders)): ?>
    <div class="mb-2">
        <label>Orders <span class="text-danger">*</span></label>
        <select name="order" id="get-order" class="form-control">
            <option value="">Select order</option>
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option data-create-url="<?php echo e(route('orders.getOrder', $order->order_number)); ?>" value="<?php echo e($order->order_number); ?>"><?php echo e($order->order_number); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <span id="order_error" class="text-danger error"></span>
    </div>
    <br />
    <div id="order-form-container"></div>
<?php else: ?>
    <div class="mb-2">
        <label>Not found any confirmed order.</label>
    </div>
<?php endif; ?>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/purchase_orders/create_content.blade.php ENDPATH**/ ?>