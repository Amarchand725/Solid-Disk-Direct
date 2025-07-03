<h5>Customer Order #<?php echo e($order->order_number); ?></h5>
<input type="hidden" name="order_number" value="<?php echo e($order->order_number); ?>">
<p>Customer: <?php echo e($customerName ?? 'N/A'); ?> | Total: <strong><?php echo e(currency()); ?><?php echo e($order->total); ?></strong></p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 50px">Action</th>
            <th style="width: 150px">Part Number</th>
            <th>Description</th>
            <th>Product Condition</th>
            <th>Quantity</th>
            <th style="width: 150px">Unit Price</th>
            <th style="width: 150px">Sub Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="product-row">
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </td>
                <td><?php echo e($item->product->mpn ?? '-'); ?></td>
                <td><?php echo $item->product->short_description ?? '-'; ?></td>
                <td><?php echo e($item->product->condition ?? '-'); ?></td>
                <td>
                    <input type="number" step="1" min="1" name="items[<?php echo e($item->product->id); ?>][quantity]" class="form-control qty-input" value="<?php echo e($item->quantity); ?>">
                    <input type="hidden" name="items[<?php echo e($item->product->id); ?>][condition]" value="<?php echo e($item->product->condition); ?>">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="items[<?php echo e($item->product->id); ?>][unit_price]" class="form-control price-input" value="<?php echo e($item->unit_price); ?>">
                </td>
                <td>
                    <strong><?php echo e(currency()); ?> <span class="row-subtotal"><?php echo e(number_format($item->quantity * $item->unit_price, 2)); ?></span></strong>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


        <tr>
            <td colspan="5"></td>
            <td>SubTotal</td>
            <td>
                <strong><?php echo e(currency()); ?> <span id="sub-total"><?php echo e(number_format($order->subtotal, 2)); ?></span></strong>
                <input type="hidden" name="sub_total_value" value="<?php echo e(number_format($order->subtotal, 2)); ?>">
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td>Tax (%)</td>
            <td><input type="number" step="0.01" name="tax_rate" class="form-control" value="<?php echo e($order->tax); ?>"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td>Shipping Charges</td>
            <td><input type="number" name="shipping_charges" class="form-control" value="<?php echo e($order->shipping_cost); ?>"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td>Grand Total</td>
            <td>
                <strong><?php echo e(currency()); ?> <span id="grand-total"><?php echo e(number_format($order->total, 2)); ?></span></strong>
                <input type="hidden" name="grand_total_value" value="<?php echo e(number_format($order->total, 2)); ?>">
            </td>
        </tr>
    </tbody>
</table>
<br />
<div class="mb-2">
    <label>Payment Method <span class="text-danger">*</span></label>
    <select name="payment_method" id="payment_method" class="form-control">
        <option value="">Select payment method</option>
        <option value="paypal">Paypal</option>
        <option value="payarc">Payarc</option>
        <option value="stripe">Stripe</option>
    </select>
    <span id="payment_method_error" class="text-danger error"></span>
</div>
<br />
<div class="mb-2">
    <label>Vendor <span class="text-danger">*</span></label>
    <select name="vendor" id="vendor" class="form-control">
        <option value="">Select vendor</option>
        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($vendor->id); ?>"><?php echo e($vendor->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <span id="vendor_error" class="text-danger error"></span>
</div>
<br />
<div class="mb-2">
    <label>Warranty Info </label>
    <textarea name="warranty_info" id="" rows="5" class="form-control" placeholder="Enter warranty info"></textarea>
    <span id="warranty_info_error" class="text-danger error"></span>
</div>
<br />
<div class="mb-2">
    <label>COMMENTS OR SPECIAL INSTRUCTIONS </label>
    <textarea name="notes" id="" rows="5" class="form-control" placeholder="Enter comment or special instructions"></textarea>
    <span id="notes_error" class="text-danger error"></span>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    function updateGrandTotal() {
        const taxRate = parseFloat($('input[name="tax_rate"]').val()) || 0;
        const shipping = parseFloat($('input[name="shipping_charges"]').val()) || 0;

        let invoiceSubtotal = 0;

        $('.product-row').each(function () {
            const qty = parseFloat($(this).find('.qty-input').val()) || 0;
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            const rowSubtotal = qty * price;

            $(this).find('.row-subtotal').text(rowSubtotal.toFixed(2));

            invoiceSubtotal += rowSubtotal;
        });

        const taxAmount = invoiceSubtotal * (taxRate / 100);
        const subtotalWithTax = invoiceSubtotal + taxAmount;
        const grandTotal = subtotalWithTax + shipping;

        $('#sub-total').text(invoiceSubtotal.toFixed(2));
        $('#grand-total').text(grandTotal.toFixed(2));

        $('input[name="sub_total_value"]').val(invoiceSubtotal.toFixed(2));
        $('input[name="grand_total_value"]').val(grandTotal.toFixed(2));
    }

    function checkRemoveButtons() {
        const rowCount = $('.product-row').length;
        if (rowCount <= 1) {
            $('.remove-row').prop('disabled', true);
        } else {
            $('.remove-row').prop('disabled', false);
        }
    }

    $(document).on('input', '.qty-input, .price-input, input[name="tax_rate"], input[name="shipping_charges"]', function () {
        updateGrandTotal();
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('.product-row').remove();
        updateGrandTotal();
        checkRemoveButtons();
    });

    // Initialize on load
    updateGrandTotal();
    checkRemoveButtons();
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/get_order_content.blade.php ENDPATH**/ ?>