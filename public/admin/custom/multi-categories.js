let isRequestInProgress = false;
$(document).on('change', '.category', function () {
    if (isRequestInProgress) return; // 🚫 Skip if request is running

    var $this = $(this);
    var category_id = $this.val();
    var url = $this.attr('data-url');
    const currentLevel = parseInt($this.data('level'));
    
    // Remove all selects after current level
    $('.category-select').each(function () {
        if (parseInt($(this).data('level')) > currentLevel) {
            $(this).parent('.sub-category-container').remove();
        }
    });

    // If nothing is selected, stop here
    if (!category_id) return;

    // Start request
    isRequestInProgress = true;

    $.ajax({
        url: url,
        data: { category_id: category_id },
        method: 'GET',
        success: function (response) {
            if (response.subCategories && response.subCategories.length > 0) {
                const nextLevel = currentLevel + 1;
                let newSelect = `
                    <div class="col-12 mt-3 sub-category-container">
                        <label class="form-label" for="sub-category-${nextLevel}">Sub Category</label>
                        <select 
                            class="category-select form-control category" 
                            data-level="${nextLevel}" 
                            data-url="${url}" 
                            data-category-level="sub-category"
                            name="categories[]"
                            id="sub-category-${nextLevel}"
                        >
                            <option value="">-- Select --</option>`;

                response.subCategories.forEach(function (sub) {
                    newSelect += `<option value="${sub.id}">${sub.name}</option>`;
                });

                newSelect += `
                        </select>
                    </div>`;

                // Append only the new dropdown
                $('#category-container').append(newSelect);

                // Initialize select2 on the new dropdown only
                $(`#sub-category-${nextLevel}`).select2({
                    dropdownParent: $(`#sub-category-${nextLevel}`).parent()
                });
            }
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                toastr.error('You do not have permission to access this resource.');
            } else {
                toastr.error('An error occurred. Please try again later.');
            }
        },
        complete: function () {
            isRequestInProgress = false; // ✅ Unlock after request ends
        }
    });
});
