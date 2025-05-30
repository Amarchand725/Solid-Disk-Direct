<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (document.querySelector('#<?php echo e($id); ?>')) {
            ClassicEditor
                .create(document.querySelector('#<?php echo e($id); ?>'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'insertTable', 'mediaEmbed', 'blockQuote', 'undo', 'redo'
                    ]
                })
                .catch(console.error);
        }
    });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/components/ckeditor.blade.php ENDPATH**/ ?>