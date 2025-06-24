<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php $__currentLoopData = $sitemaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sitemap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <sitemap>
    <loc><?php echo e($sitemap); ?></loc>
    <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
  </sitemap>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</sitemapindex>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/sitemaps/index.blade.php ENDPATH**/ ?>