$("form.submitBtnWithFileUpload").on('submit', function (e) {
    e.preventDefault();
    var thi = $(this);
    var url = $(this).attr('action');
    var method = $(this).attr('method');
    var modal_id = $(this).attr('data-modal-id');
    // Get the form data
    var formElement = $('#' + modal_id).find('#create-form');

    prepareFormDataBeforeSubmit(formElement);
    var formData = new FormData(formElement[0]);

    thi.find('.sub-btn').hide();
    thi.find('.loading-btn').show();

    $.ajax({
        url: url,
        type: method,
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function (response) {
            thi.find('.sub-btn').show();
            thi.find('.loading-btn').hide();

            if (response.success == true) {
                toastr.success(response.message, 'Success', { timeOut: 1000 });

                if (response.route !== undefined) {
                    window.location.href = response.route;
                }else{
                    var oTable = $('.data_table').dataTable();
                    oTable.fnDraw(false);

                    $('#' + modal_id).modal('hide');
                    $('#' + modal_id).removeClass('show');
                    $('#' + modal_id).parents('.card').find('.offcanvas-backdrop').removeClass('show');
                }
            } else if (response.error) {
                // $('#' + modal_id).modal('hide');
                toastr.error(response.error);
            } else if (response.error == false) {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            thi.find('.sub-btn').show();
            thi.find('.loading-btn').hide();
            // Parse the JSON response to get the error messages
            var errors = JSON.parse(xhr.responseText);
            
            // Reset the form errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();
            $('.error').empty();

            // Loop through the errors and display them
            $.each(errors.errors, function (key, value) {
                const message = value[0];

                // Show a Toastr alert
                toastr.error(message);
                
                $('#' + key).addClass('is-invalid'); // Add the is-invalid class to the input element
                $('#' + key + '_error').text(value[0]); // Add the error message to the error element
            });
        }
    });
});
function prepareFormDataBeforeSubmit(formElement) {
    $(formElement).find('.summernote').each(function () {
        var content = $(this).summernote('code');
        $(this).val(content);
    });
}

$('.submitBtn').click(function (e) {
    e.preventDefault(); // Prevent the form from submitting normally
    var thi = $(this);

    var url = $(this).closest('form').attr('action');
    var method = $(this).closest('form').attr('method');

    var formId = $(this).closest('form').attr('id');
    var modal_id = $(this).closest('form').attr('data-modal-id');

    var formData = $("#"+formId).serialize();

    // Check if the description variable exists in the serialized form data
    var fieldExists = formData.indexOf('description=') > -1;

    // if (fieldExists) {
    //     //Get editor value.
    //     var editorData = CKEDITOR.instances.description.getData();
    //     // Combine the editor data with the serialized form data
    //     formData = formData + '&description=' + encodeURIComponent(editorData);
    // }

    if (fieldExists) {
        // Get Summernote content (assumes #description is the textarea enhanced with Summernote)
        var editorData = $('#description').summernote('code');
    
        // Update/replace the existing `description` field in formData
        formData = formData.replace(/description=[^&]*/, 'description=' + encodeURIComponent(editorData));
    }

    thi.parents('.action-btn').find('.sub-btn').hide();
    thi.parents('.action-btn').find('.loading-btn').show();

    // Send the AJAX request
    // Add the CSRF token to the request headers
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: url,
        method: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            thi.parents('.action-btn').find('.sub-btn').show();
            thi.parents('.action-btn').find('.loading-btn').hide();

            if (response.success == true) {
                toastr.success(response.message, 'Success', { timeOut: 1000 });

                if (response.route !== undefined) {
                    window.location.href = response.route;
                }else{
                    var oTable = $('.data_table').dataTable();
                    oTable.fnDraw(false);

                    $('#' + modal_id).modal('hide');
                    $('#' + modal_id).removeClass('show');
                    $('#' + modal_id).parents('.card').find('.offcanvas-backdrop').removeClass('show');
                }
            } else if (response.error) {
                var html = '<div class="alert alert-danger">'+response.error+'</div>';
                $("#errorMessage").html(html).show();
                $('.error').text('');
                toastr.error(response.error);
            }
        },
        error: function (xhr) {
            thi.parents('.action-btn').find('.sub-btn').show();
            thi.parents('.action-btn').find('.loading-btn').hide();
            // Parse the JSON response to get the error messages
            var errors = JSON.parse(xhr.responseText);
            // Reset the form errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();
            $('.error').empty();

            // Loop through the errors and display them
            $.each(errors.errors, function (key, value) {
                $('#' + key).addClass('is-invalid'); // Add the is-invalid class to the input element
                $('#' + key + '_error').text(value[0]); // Add the error message to the error element
            });
        }
    });
});

$(document).on('click', '.show', function () {
    var targeted_modal = $(this).attr('data-bs-target');
    var modal_label = $(this).attr('title');

    $(targeted_modal).find('#modal-label').html(modal_label);
    var html = '<div class="d-block w-100">' +
        '<div class="d-block w-100">' +
        '<div class="d-flex justify-content-center align-items-center" style="height: 20vw;>' +
        '<div class="demo-inline-spacing">' +
        '<div class="spinner-border spinner-border-lg text-primary" role="status">' +
        '<span class="visually-hidden">Loading...</span>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
    $(targeted_modal).find('#show-content').html(html);
    var show_url = $(this).attr('data-show-url');
    $.ajax({
        url: show_url,
        method: 'GET',
        success: function (response) {
            $(targeted_modal).find('#show-content').html(response);
        }
    });
});

$(document).on('click', '.delete', function () {
    var delete_url = $(this).attr('data-del-url');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: delete_url,
                type: 'DELETE',
                success: function (response) {
                    if (response.status==true) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });
                        var oTable = $('.data_table').dataTable();
                        oTable.fnDraw(false);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.error || 'An unexpected error occurred.',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = "An unexpected error occurred. Please try again.";
                    
                    if (xhr.status === 403 && xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || "You do not have permission to perform this action.";
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
            
                    Swal.fire({
                        title: 'Permission Denied!',
                        text: errorMessage,
                        icon: 'error'
                    });
                } 
            });
        }
    })
});

//Open modal for adding
$('#add-btn, .add-btn').on('click', function () {
    var targeted_modal = $(this).attr('data-bs-target');
    var store_url = $(this).attr('data-url');
    var modal_label = $(this).attr('title');
    var content_url = $(this).attr('data-create-url');
    loadForm(targeted_modal, store_url, modal_label, content_url);
});

//Open modal for editing
$(document).on('click', '.edit-btn', function () {
    var targeted_modal = $(this).attr('data-bs-target');
    var store_url = $(this).attr('data-url');
    var modal_label = $(this).attr('title');
    var content_url = $(this).attr('data-edit-url');
    loadForm(targeted_modal, store_url, modal_label, content_url);
});

function loadForm(targeted_modal, store_url, modal_label, content_url){
    $(targeted_modal).find('#modal-label').html(modal_label);
    $(targeted_modal).find("#create-form").attr("action", store_url);
    $(targeted_modal).find("#create-form").attr("method", 'POST');

    $.ajax({
        url: content_url,
        method: 'GET',
        beforeSend: function () {
            // Show a loading spinner or text before the response is loaded
            $(targeted_modal).find('#edit-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        },
        success: function (response) {
            $(targeted_modal).find('#edit-content').html(response);
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                // Handle permission error
                $(targeted_modal).find('#edit-content').html('<div class="alert alert-danger text-center">You do not have permission to access this resource.</div>');
            } else {
                // Handle other errors
                $(targeted_modal).find('#edit-content').html('<div class="alert alert-danger text-center">An error occurred. Please try again later.</div>');
            }
        }    
    });
}

//calling datatable
function initializeDataTable(pageUrl, columns) {
    tableClass = '.data_table';
    if ($.fn.DataTable.isDataTable(tableClass)) {
        $(tableClass).DataTable().destroy();
    }

    $(tableClass).DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: pageUrl + "?loaddata=yes",
        columns: columns
    });
}

$('#country').on('change', function(){
    var url = $(this).attr('data-url');
    var country = $(this).val();
    $('#city').html('');
    $.ajax({
        url: url,
        data:{country:country},
        method: 'GET',
        success: function (response) {
            if(response){
                let options = '<option value="">Select State</option>';
                response.forEach(state => {
                    options += `<option value="${state.name}">${state.name}</option>`;
                });
                $('#state').html(options);
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
$('#state').on('change', function(){
    var url = $(this).attr('data-url');
    var state = $(this).val();
    $.ajax({
        url: url,
        data:{state:state},
        method: 'GET',
        success: function (response) {
            if(response){
                let options = '<option value="">Select City</option>';
                response.forEach(state => {
                    options += `<option value="${state.name}">${state.name}</option>`;
                });
                $('#city').html(options);
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

$(document).on('click', '.deleteImage', function () {
    var delete_url = $(this).attr('data-del-url');
    var thi = $(this);
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: delete_url,
                type: 'DELETE',
                success: function (response) {
                    if (response.status==true) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });
                        thi.parents('.preview-wrapper').remove();
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.error || 'An unexpected error occurred.',
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = "An unexpected error occurred. Please try again.";
                    
                    if (xhr.status === 403 && xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || "You do not have permission to perform this action.";
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
            
                    Swal.fire({
                        title: 'Permission Denied!',
                        text: errorMessage,
                        icon: 'error'
                    });
                } 
            });
        }
    })
});