$(document).on("input", ".numeric", function() {
    this.value = this.value.replace(/\D/g,'');
});

$(document).on('keyup', '.cnic_number', function() {
    var cnic = $(this).val();
    var formattedCnic = formatCnic(cnic);
    $(this).val(formattedCnic);
});

function formatCnic(cnic) {
    cnic = cnic.replace(/\D/g, ''); // Remove non-numeric characters
    if (cnic.length > 5) {
        cnic = cnic.substring(0, 5) + "-" + cnic.substring(5, 12) + "-" + cnic.substring(12, 13);
    } else if (cnic.length > 2) {
        cnic = cnic.substring(0, 5) + "-" + cnic.substring(5);
    }
    return cnic;
}
$(document).on('keyup', '.mobileNumber', function() {
    var mobile = $(this).val();
    var formattedMobile = formatMobileNumber(mobile);
    $(this).val(formattedMobile);
});

function formatMobileNumber(mobile) {
    mobile = mobile.replace(/\D/g, ''); // Remove non-numeric characters
    if (mobile.length > 4) {
        mobile = mobile.substring(0, 4) + "-" + mobile.substring(4, 11);
    }
    return mobile;
}

$(document).on('keyup', '.phoneNumber', function() {
    var phone = $(this).val();
    var formattedPhone = formatPhoneNumber(phone);
    $(this).val(formattedPhone);
});

function formatPhoneNumber(phone) {
    phone = phone.replace(/\D/g, '');
    if (phone.length > 3) {
        var areaCode = phone.substring(0, 3);
        var telephoneNumber = phone.substring(3, 10);
        phone =  "(" + areaCode + ") - " + telephoneNumber;
    }
    return phone;
}

$(document).on('click','i[class^="ti ti-eye"]',function(){
   var getType=$(this).parent().parent().find('input').attr('type');
   if(getType=='text'){
       $(this).attr('class','ti ti-eye-off');
       $(this).parent().parent().find('input').attr('type','password');
   }else{
       $(this).attr('class','ti ti-eye');
       $(this).parent().parent().find('input').attr('type','text');
   }
});

$(document).ready(function() {
    $(document).on('change', '.uploader', function() {
        var name = this.name;
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Create an image element
                var img = $('<img style="width:60px; height:50px">').attr('src', e.target.result);

                // Display the image preview
                $('#preview-'+name).html(img);

                // Add click event handler to the image for zooming
                img.click(function() {
                    $(this).toggleClass('zoomed');
                });
            };

            // Read the image file as a data URL
            reader.readAsDataURL(file);
        } else {
            // Clear the preview area if no file is selected
            $('#preview-'+name).html('');
        }
    });

    // Add click event handler to the image with the zoomable class
    $(document).on('click', '.zoomable', function() {
        // Toggle the 'zoomed' class on click
        $(this).toggleClass('zoomed');
    });
});