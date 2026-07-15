let errorMessageShown = false; // 에러 메시지를 표시했는지 여부를 나타내는 변수
let previousUploadedFilesCount = 0 // plupload 새로운 파일 추가 여부 판단위한 변수
let pluploadFile = [];

const tinymce_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/common/tinyUpload');

    xhr.upload.onprogress = (e) => {
        progress(e.loaded / e.total * 100);
    };

    xhr.onload = () => {
        if (xhr.status === 403) {
            reject({message: 'HTTP Error: ' + xhr.status, remove: true});
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300) {
            reject('HTTP Error: ' + xhr.status);
            return;
        }

        const json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location != 'string') {
            reject('Invalid JSON: ' + xhr.responseText);
            return;
        }

        resolve(json.location);
    };

    xhr.onerror = () => {
        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };

    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    formData.append('_token', $('meta[name=csrf-token]').attr('content'));

    xhr.send(formData);
});

tinymce.init({
    promotion: false,
    selector: '.tinymce', // 에디터 사용 클래스
    language: 'ko_KR',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',

    paste_webkit_styles: 'all',  // 웹킷 스타일 유지

    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    image_class_list: [
        {title: 'img-responsive', value: 'img-responsive'},
    ],
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    height: 600,
    images_upload_handler: tinymce_image_upload_handler,
    file_picker_callback: function (cb, value, meta) {
        let input = document.createElement('input');

        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.onchange = function () {
            const file = this.files[0];
            const reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = function () {
                const id = 'blobid' + moment().valueOf();
                let blobCache = tinymce.activeEditor.editorUpload.blobCache;

                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);

                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), {title: file.name});
            };
        };

        input.click();
    }
});

const pluploadInit = (addOption = {}) => {
    let defaultOption = {
        runtimes: 'html5,flash',
        flash_swf_url: '/script/Moxie.swf',
        silverlight_xap_url: '/script/Moxie.xap',
        url: '/common/plUpload',
        dragdrop: true,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
        },
        init: {
            PostInit: function (up) {
                $(up.getOption('container')).find('.plupload_button.plupload_start').hide();
            },

            // 파일이 추가될 때 호출되는 이벤트 핸들러 추가
            FilesAdded: function(up, files) {
                // addOption에서 파일 제한 설정 확인
                const extensionRestrictions = addOption.mime_types?.[0]?.extensions;

                if (!extensionRestrictions) {
                    return; // 제한이 없으면 모든 파일 허용
                }

                let invalidFiles = [];
                const validExtensions = extensionRestrictions.split(',');

                files.forEach(function(file) {
                    let extension = file.name.split('.').pop().toLowerCase();
                    if (!validExtensions.includes(extension)) {
                        invalidFiles.push(file);
                    }
                });

                if (invalidFiles.length > 0) {
                    invalidFiles.forEach(function(file) {
                        up.removeFile(file);
                    });

                    alert(`다음 확장자만 업로드 가능합니다: .${validExtensions.join(', .')}`);
                }
            },

            Error: function (up, err) {
                // 에러 메시지를 한 번만 표시하도록 처리
                if (!errorMessageShown) {
                    up.stop();
                    up.splice();
                    errorMessageShown = true;
                    alert('파일 업로드 에러 - ' + err.message);
                    location.reload();
                }
            },

            FileUploaded: function (up, file, info) {
                pluploadFile.push(JSON.parse(info.response));
            }
        }
    }

    // 사용자 정의 옵션과 기본 옵션을 병합하여 plupload 초기화
    $('#plupload').pluploadQueue($.extend(true, {}, defaultOption, addOption));
}
