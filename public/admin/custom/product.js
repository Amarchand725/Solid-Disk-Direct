CKEDITOR.replace('short_description');
CKEDITOR.replace('full_description');
$('select').each(function () {
    $(this).select2({
        dropdownParent: $(this).parent(),
    });
});
$('#file-uploader').change(function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();

        reader.onload = function(e) {
            // Create an image element
            var img = $('<img style="width:60px; height:50px">').attr('src', e.target.result);

            // Display the image preview
            $('#preview').html(img);

            // Add click event handler to the image for zooming
            img.click(function() {
                $(this).toggleClass('zoomed');
            });
        };

        // Read the image file as a data URL
        reader.readAsDataURL(file);
    } else {
        // Clear the preview area if no file is selected
        $('#preview').html('');
    }
});

document.getElementById('images').addEventListener('change', function(event) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = ''; // Clear previous

    const files = Array.from(event.target.files);

    files.forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.width = '80px';
            wrapper.style.height = '80px';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '6px';
            img.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';

            const removeBtn = document.createElement('span');
            removeBtn.innerHTML = '&times;';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '-5px';
            removeBtn.style.right = '-5px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.background = 'red';
            removeBtn.style.color = 'white';
            removeBtn.style.borderRadius = '50%';
            removeBtn.style.width = '20px';
            removeBtn.style.height = '20px';
            removeBtn.style.display = 'flex';
            removeBtn.style.alignItems = 'center';
            removeBtn.style.justifyContent = 'center';
            removeBtn.style.fontSize = '14px';

            removeBtn.onclick = function() {
                wrapper.remove();
                // Optional: remove file from input (see below)
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
});

$('.category').on('change', function(){
    var category_id = $(this).val();
    var url = $(this).attr('data-url');
    $('#sub_categories').html('');
    $.ajax({
        url: url,
        data:{category_id:category_id},
        method: 'GET',
        success: function (response) {
            if(response != ''){
                let options = `
                <div class="col-12 mt-3">
                    <label class="form-label" for="sub-category">Sub Category</label>
                    <select class="form-control category" name="categories[]">
                        <option value="">Select State</option>
                        ${response.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')}
                    </select>
                </div>
                `;

                $('#sub_categories').append(options);

                // Initialize select2 on the newly added select box
                $('select').each(function () {
                    $(this).select2({
                        dropdownParent: $(this).parent(),
                    });
                });
            }
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                // Handle permission error
                toastr.error('You do not have permission to access this resource.');
            } else {
                // Handle other errors
                toastr.error('An error occurred. Please try again later.');
            }
        }    
    });
})