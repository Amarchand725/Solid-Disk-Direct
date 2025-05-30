$('.summernote').summernote({
    placeholder: 'Write something...',
    tabsize: 2,
    height: 300,
    toolbar: [
        ['style', ['style', 'bold', 'italic', 'underline', 'clear']], 
        ['font', ['strikethrough', 'superscript', 'subscript', 'fontsize', 'fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link', 'picture', 'video', 'table']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ],
});