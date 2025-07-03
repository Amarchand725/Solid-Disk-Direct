@if(isset($orders) && !empty($orders))
    <div class="mb-2">
        <label>Orders <span class="text-danger">*</span></label>
        <select name="order" id="get-order" class="form-control">
            <option value="">Select order</option>
            @foreach ($orders as $order)
                <option data-create-url="{{ route('orders.getOrder', $order->order_number) }}" value="{{ $order->order_number }}">{{ $order->order_number }}</option>
            @endforeach
        </select>
        <span id="order_error" class="text-danger error"></span>
    </div>
    <br />
    <div id="order-form-container"></div>
@else
    <div class="mb-2">
        <label>Not found any confirmed order.</label>
    </div>
@endif

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>