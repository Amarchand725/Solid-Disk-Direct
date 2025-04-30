$(document).on('change', '.category', function() {
    var category_id = $(this).val();
    var url = $(this).attr('data-url');
    const selectedId = $(this).val();
    const currentLevel = parseInt($(this).data('level'));

    // Remove all selects after current level (clean children)
    $('.category-select').each(function () {
        if (parseInt($(this).data('level')) > currentLevel) {
            $(this).parent('.sub-category-container').remove();
        }
    });

    // If nothing is selected, no need to load children
    if (!selectedId) return;

    if(category_id != ''){
        $.ajax({
            url: url,   
            data:{category_id:category_id},
            method: 'GET',
            success: function (response) {
                if (response.subCategories && response.subCategories.length > 0) {
                    // Create new select for subcategories
                    const nextLevel = currentLevel + 1;
                    let newSelect = `
                        <div class="col-12 mt-3 sub-category-container">
                            <label class="form-label" for="sub-category">Sub Category</label>
                            <select 
                                class="category-select form-control category" 
                                data-level="${nextLevel}" 
                                data-url="${url}" 
                                data-category-level="sub-category"
                                id="sub-category"
                                name="categories[]"
                            >
                                <option value="">-- Select --</option>`;

                    response.subCategories.forEach(function (sub) {
                        newSelect += `<option value="${sub.id}">${sub.name}</option>`;
                    });

                    newSelect += `
                            </select>
                        </div>`;

                    $('#category-container').append(newSelect);

                    // Initialize select2 on the newly added select box
                    // $('select').each(function () {
                    //     $(this).select2({
                    //         dropdownParent: $(this).parent(),
                    //     });
                    // });

                    $('#category-container select.category-select').last().select2({
                        dropdownParent: $('#category-container .sub-category-container').last(),
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
    }
})
