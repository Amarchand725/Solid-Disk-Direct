<?php if(!empty($image)): ?>
    <img src="<?php echo e(asset('storage/' . $image)); ?>" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
<?php else: ?>
    <img src="<?php echo e(asset('admin/default.png')); ?>" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
<?php endif; ?><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/layouts/show_image.blade.php ENDPATH**/ ?>