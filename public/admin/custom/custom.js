$('.form-select').select2();

//image preview & zoom out
$('#file-uploader').change(function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();

        reader.onload = function(e) {
            // Create an image element
            var img = $('<img style="width:30%; height:20%">').attr('src', e.target.result);

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

// Add click event handler to the image with the zoomable class
$(document).on('click', '.zoomable', function() {
    // Toggle the 'zoomed' class on click
    $(this).toggleClass('zoomed');
});
