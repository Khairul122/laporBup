/**
 * Text Editor Configuration with Image Upload Support
 *
 * This file provides configuration for CKEditor, TinyMCE, or other WYSIWYG editors
 * with image upload functionality
 */

// CKEditor 5 Configuration
function initCKEditor5(elementId) {
    if (typeof ClassicEditor === 'undefined') {
        console.error('CKEditor 5 is not loaded');
        return;
    }

    ClassicEditor
        .create(document.querySelector('#' + elementId), {
            ckfinder: {
                uploadUrl: 'upload_handler.php'
            },
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'outdent',
                    'indent',
                    '|',
                    'imageUpload',
                    'blockQuote',
                    'insertTable',
                    'mediaEmbed',
                    'undo',
                    'redo'
                ]
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:full',
                    'imageStyle:side',
                    'linkImage'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            }
        })
        .then(editor => {
            console.log('CKEditor 5 initialized successfully');
            window.editor = editor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor 5:', error);
        });
}

// CKEditor 4 Configuration
function initCKEditor4(elementId) {
    if (typeof CKEDITOR === 'undefined') {
        console.error('CKEditor 4 is not loaded');
        return;
    }

    CKEDITOR.replace(elementId, {
        filebrowserUploadUrl: 'upload_handler.php',
        filebrowserUploadMethod: 'form',
        height: 400,
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Preview'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize'] }
        ]
    });
}

// TinyMCE Configuration
function initTinyMCE(elementId) {
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE is not loaded');
        return;
    }

    tinymce.init({
        selector: '#' + elementId,
        height: 400,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'image media link | removeformat | help',
        images_upload_url: 'upload_handler.php',
        images_upload_handler: function (blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('upload', blobInfo.blob(), blobInfo.filename());

            fetch('upload_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.uploaded === 1) {
                    success(result.url);
                } else {
                    failure(result.error || 'Upload failed');
                }
            })
            .catch(error => {
                failure('Upload failed: ' + error.message);
            });
        },
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });
}

// Summernote Configuration
function initSummernote(elementId) {
    if (typeof $.fn.summernote === 'undefined') {
        console.error('Summernote is not loaded');
        return;
    }

    $('#' + elementId).summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                uploadImageSummernote(files, elementId);
            }
        }
    });
}

function uploadImageSummernote(files, elementId) {
    const formData = new FormData();
    formData.append('upload', files[0]);

    fetch('upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.uploaded === 1) {
            $('#' + elementId).summernote('insertImage', result.url);
        } else {
            alert('Upload failed: ' + result.error);
        }
    })
    .catch(error => {
        alert('Upload failed: ' + error.message);
    });
}

// Auto-detect and initialize editor
function autoInitEditor(elementId, editorType = 'auto') {
    if (editorType === 'auto') {
        // Try to detect which editor library is loaded
        if (typeof ClassicEditor !== 'undefined') {
            editorType = 'ckeditor5';
        } else if (typeof CKEDITOR !== 'undefined') {
            editorType = 'ckeditor4';
        } else if (typeof tinymce !== 'undefined') {
            editorType = 'tinymce';
        } else if (typeof $.fn.summernote !== 'undefined') {
            editorType = 'summernote';
        } else {
            console.warn('No supported editor library detected');
            return;
        }
    }

    switch(editorType.toLowerCase()) {
        case 'ckeditor5':
            initCKEditor5(elementId);
            break;
        case 'ckeditor4':
            initCKEditor4(elementId);
            break;
        case 'tinymce':
            initTinyMCE(elementId);
            break;
        case 'summernote':
            initSummernote(elementId);
            break;
        default:
            console.error('Unsupported editor type: ' + editorType);
    }
}

// Export functions for global use
window.initCKEditor5 = initCKEditor5;
window.initCKEditor4 = initCKEditor4;
window.initTinyMCE = initTinyMCE;
window.initSummernote = initSummernote;
window.autoInitEditor = autoInitEditor;
