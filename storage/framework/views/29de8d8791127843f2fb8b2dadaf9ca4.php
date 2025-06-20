<?php echo csrf_field(); ?>
<div class="mb-3">
    <label for="file" class="form-label">Choose Excel File</label>
    
    <input type="file" name="files[]" id="file" class="form-control" accept=".xlsx,.csv" multiple required>
</div>
<div id="import-summary-content" class="mt-3"></div><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/products/import_create_content.blade.php ENDPATH**/ ?>