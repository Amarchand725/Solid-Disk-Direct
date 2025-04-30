if (typeof isRequestInProgress === 'undefined') {
    var isRequestInProgress = false;
}

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

function setupImagePreview(inputId, previewContainerId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewContainerId);

    if (!input || !preview) return;

    input.addEventListener('change', function (event) {
        preview.innerHTML = ''; // Clear previous

        const files = Array.from(event.target.files);

        files.forEach((file) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';
                wrapper.style.marginRight = '5px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '6px';
                img.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';

                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '&times;';
                Object.assign(removeBtn.style, {
                    position: 'absolute',
                    top: '-5px',
                    right: '-5px',
                    cursor: 'pointer',
                    background: 'red',
                    color: 'white',
                    borderRadius: '50%',
                    width: '20px',
                    height: '20px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '14px',
                });

                removeBtn.onclick = function () {
                    wrapper.remove();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    });
}

