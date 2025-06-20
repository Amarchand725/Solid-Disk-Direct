<?php $__env->startComponent('mail::message'); ?>
<table width="100%" style="text-align: center; margin-bottom: 20px;">
    <tr>
        <td>
            <?php if(isset(settings()->black_logo) && !empty(settings()->black_logo)): ?>
                <img src="<?php echo e(asset('storage').'/'.settings()->black_logo); ?>" style="height: 40px;" alt="<?php echo e(settings()->name); ?>" />
            <?php else: ?>
                <img src="<?php echo e(asset('storage/images/default.png')); ?>" style="height: 40px;" alt="Default" />
            <?php endif; ?>
        </td>
    </tr>
</table>

# Dear <?php echo e($data['contact_name'] ?? 'Support Team'); ?>,

I hope this message finds you well.

I am reaching out to request a quotation for the following product/service:

---

**Product/Service Name:**  
<?php echo e($data['product_name'] ?? 'N/A'); ?>


**Quantity:**  
<?php echo e($data['quantity'] ?? 'N/A'); ?>


---

Please include the following details in your quotation:

- Unit price and total cost  
- Delivery timeline  
- Payment terms  
- Warranty or support details  

---

### Company Details

- **Company Name:** <?php echo e($data['company_name'] ?? 'N/A'); ?>  
- **Contact Person:** <?php echo e($data['contact_person'] ?? 'N/A'); ?>  
- **Phone:** <?php echo e($data['phone'] ?? 'N/A'); ?>  
- **Email:** <?php echo e($data['email'] ?? 'N/A'); ?>  

---

Please let me know if you require any further information to process this request.

Looking forward to your prompt response.

Warm regards,  
**<?php echo e($data['contact_person'] ?? ''); ?>**  
<?php if(!empty($data['job_title'])): ?><?php echo e($data['job_title']); ?><br><?php endif; ?>
<?php echo e($data['company_name'] ?? ''); ?>  
<?php echo e($data['phone'] ?? ''); ?>  
<?php echo e($data['email'] ?? ''); ?>


<?php echo $__env->renderComponent(); ?><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/emails/quote-request.blade.php ENDPATH**/ ?>