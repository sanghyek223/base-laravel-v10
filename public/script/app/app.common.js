"use strict";

let script_lang = 'ENG'; // KR or ENG

$(function () {
    if ($('form').length > 0) {
        $('form[method=post]').attr('onsubmit', 'return false;');
    }

    if ($('input:text[datepicker]').length > 0) {
        callDatePicker();
    }

    if ($('input:text[datetimepicker]').length > 0) {
        callDateTimePicker();
    }
})

// ajax Setup
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// window popup
$(document).on('click', '.call-popup', function (e) {
    e.preventDefault();

    const popupHeight = isEmpty($(this).data('height')) ? 700 : $(this).data('height');
    const popupWidth = isEmpty($(this).data('width')) ? 500 : $(this).data('width');
    const popName = isEmpty($(this).data('name')) ? 'popup' : $(this).data('name');
    const popupY = (window.screen.height / 2) - (popupHeight / 2);
    const popupX = (window.screen.width / 2) - (popupWidth / 2);

    window.open($(this).attr('href'), popName, 'status=no, height=' + popupHeight + ', width=' + popupWidth + ', left=' + popupX + ', top=' + popupY);
});

// popup cancel btn
$(document).on('click', '#popup_cancel_btn', function () {
    if (confirm('취소 하시겠습니까?')) {
        window.close();
    }
});

// validator Default
const defaultVaildation = () => {
    // 공백 제거후 빈값 체크
    $.validator.addMethod('isEmpty', function (value, element) {
        return !isEmpty(value);
    });

    // 최소 숫자 0 이상일 경우
    $.validator.addMethod('minInt', function (value, element) {
        return (parseInt(uncomma(value)) > 0);
    });

    // 전화번호 or 휴대폰 (하이픈포함) 정규식 체크
    $.validator.addMethod('telRegExp', function (value, element) {
        const telRegExp = /^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}/;
        return telRegExp.test(value);
    });

    // 개월수 1 ~ 12 만 허용
    $.validator.addMethod('monthCheck', function (value, element) {
        return ((parseInt(value) > 0) && (parseInt(value) <= 12));
    });

    // radio or checkbox 체크 유무
    $.validator.addMethod('checkEmpty', function (value, element) {
        return $('input[name="' + $(element).attr('name') + '"]:checked').length > 0;
    });

    // tinymce 에디터 빈값 체크
    $.validator.addMethod('isTinyEmpty', function (value, element) {
        let tinyVal = tinymce.get($(element).attr('id')).getContent(); // 내용 가져오기
        tinyVal = tinyVal.replace(/<[^>]*>?/g, ""); // html 태그 삭제
        tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

        return !isEmpty(tinyVal);
    });

    $.validator.setDefaults({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        showErrors: function (errorMap, errorList) {
            if (this.numberOfInvalids()) {
                let obj = {};
                obj.case = 'focus';
                obj.focus = errorList[0].element;
                obj.msg = errorList[0].message;

                actionAlert(obj);
            }
        }
    });
}

const callDatePicker = () => {
    let datepicker = {};

    $('input:text[datepicker]').each(function (k, v) {
        datepicker[$(v).attr('id')] = $(v).flatpickr({
            locale: "ko", // 한국어 설정
            enableTime : false,
            enableSeconds:false,
            altFormat: 'Y-m-d',
            dateFormat : "Y-m-d",
        });
    });

    return datepicker;
}

const callDateTimePicker = () => {
    let datetimepicker = {};

    $('input:text[datetimepicker]').each(function (k, v) {
        datetimepicker[$(v).attr('id')] = $(v).flatpickr({
            locale: "ko", // 한국어 설정
            time_24hr: true,
            enableTime : true,
            enableSeconds:true,
            altInput: true,
            altFormat: 'Y-m-d H:i:S',
            dateFormat : "Y-m-d H:i:S",
        });
    });

    return datetimepicker;
}

const encryptAction = (data) => {
    const encKey = "secret phrase";

    switch (true) {
        case typeof data === 'boolean':
            // boolean 일경우 string 으로 변환후 암호화
            return encryptAction(data.toString());

        case Array.isArray(data):
            // 배열인 경우 각 요소를 암호화
            return data.map(val => encryptAction(val));

        case typeof data == 'object':
            // 파일 데이터인 경우 암호화하지 않음
            if (data instanceof Blob || data instanceof File) {
                return data;
            } else {
                // 객체의 각 속성 값을 암호화
                const encryptedObj = {};

                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        encryptedObj[key] = encryptAction(data[key]);
                    }
                }

                return encryptedObj;
            }

        case typeof data == 'number':
        case typeof data == 'string':
            // 문자열 또는 숫자인 경우 암호화
            const iv = CryptoJS.lib.WordArray.random(16);
            return CryptoJS.AES.encrypt(data.toString(), encKey, {iv: iv}).toString();

        default:
            // 다른 타입의 데이터는 암호화하지 않음
            return data;
    }
}

const encryptMultiData = (obj) => {
    const multiFormData = new FormData();
    const processedKeys = new Set();

    obj.forEach((value, key) => {
        if (processedKeys.has(key)) return;

        const hasIndex = key.includes('[');

        if (hasIndex) {
            const parts = key.split('[');
            const baseKey = parts[0];
            const suffix = key.substring(baseKey.length);
            const encryptedKey = encryptAction(baseKey) + suffix;

            const allValues = obj.getAll(key);

            // 파일 포함 여부 확인
            const hasFile = allValues.some(val => val instanceof Blob || val instanceof File);

            if (hasFile) {
                // 파일이면 키 암호화 없이 그대로
                allValues.forEach(val => {
                    multiFormData.append(key, val);
                });
            } else {
                allValues.forEach(val => {
                    const finalValue = (typeof val === 'string') ? encryptAction(val) : val;
                    multiFormData.append(encryptedKey, finalValue);
                });
            }

            processedKeys.add(key);

        } else {
            if (value instanceof Blob || value instanceof File) {
                multiFormData.append(key, value);
            } else {
                multiFormData.append(encryptAction(key), encryptAction(value));
            }
        }
    });

    return multiFormData;
};

const encryptData = (obj) => {
    let newObj = {};

    $.each(obj, function (key, value) {
        newObj[encryptAction(key)] = encryptAction(value);
    });

    return newObj;
}

// ajax
const callAjax = (url, obj, isDebug = false) => {
    callbackAjax(url, obj, function (data, error) {
        (data) ? ajaxSuccessData(data) : ajaxErrorData(error);
    }, isDebug);
}

// multi-part ajax (file 전송시 or 배열값 전송시)
const callMultiAjax = (url, obj, isDebug = false) => {
    callbackMultiAjax(url, obj, function (data, error) {
        (data) ? ajaxSuccessData(data) : ajaxErrorData(error);
    }, isDebug);
}

// callback ajax
const callbackAjax = (url, obj, callback, isDebug = false) => {
    $.ajax({
        type: "POST",
        url: url,
        data: encryptData(obj),
        beforeSend: function () {
            spinnerShow();
        },
        complete: function () {
            spinnerHide();
        },
        success: function (data) {
            if (isDebug) console.log(data);
            callback(data, null);
        },
        error: function (error) {
            if (isDebug) console.log(error);
            callback(null, error);
        }
    });
}

// callback multi-part ajax (file 전송시 or 배열값 전송시)
const callbackMultiAjax = (url, obj, callback, isDebug = false) => {
    $.ajax({
        type: "POST",
        processData: false,
        contentType: false,
        url: url,
        data: encryptMultiData(obj),
        beforeSend: function () {
            spinnerShow();
        },
        complete: function () {
            spinnerHide();
        },
        success: function (data) {
            if (isDebug) console.log(data);
            callback(data, null);
        },
        error: function (error) {
            if (isDebug) console.log(error);
            callback(null, error);
        }
    });
}

// ajax none spinner
const callNoneSpinnerAjax = (url, obj, isDebug = false) => {
    $.ajax({
        type: "POST",
        url: url,
        data: encryptData(obj),
        success: function (data) {
            if (isDebug) console.log(data);
            ajaxSuccessData(data);
        },
        error: function (data) {
            if (isDebug) console.log(data);
            ajaxErrorData(data);
        }
    });
}

// ajax Success
const ajaxSuccessData = (obj) => {
    if (obj.log) {
        console.log(obj.log);
    }

    if (obj.alert) {
        actionAlert(obj.alert);
    }

    if (obj.toast) {
        actionToast(obj.toast);
    }

    if (obj.winClose) {
        if (obj.winClose.reload) {
            opener.location.reload();
        }

        window.close();
    }

    if (obj.parentsReload) {
        if (opener) {
            opener.location.reload();
        }
    }

    if (obj.location) {
        locationUrl(obj.location);
    }

    if (obj.removeCss) {
        removeCss(obj.removeCss);
    }

    if (obj.addCss) {
        addCss(obj.addCss);
    }

    if (obj.removeClass) {
        removeClass(obj.removeClass);
    }

    if (obj.addClass) {
        addClass(obj.addClass);
    }

    if (obj.remove) {
        isRemove(obj.remove);
    }

    if (obj.html) {
        addHtml(obj.html);
    }

    if (obj.before) {
        beforeHtml(obj.before);
    }

    if (obj.after) {
        afterHtml(obj.after);
    }

    if (obj.append) {
        appendHtml(obj.append);
    }

    if (obj.prepend) {
        prependHtml(obj.prepend);
    }

    if (obj.openerHtml) {
        openerAddHtml(obj.openerHtml);
    }

    if (obj.openerBefore) {
        openerBeforeHtml(obj.openerBefore);
    }

    if (obj.openerAfter) {
        openerAfterHtml(obj.openerAfter);
    }

    if (obj.openerAppend) {
        openerAppendHtml(obj.openerAppend);
    }

    if (obj.openerPrepend) {
        openerPrependHtml(obj.openerPrepend);
    }

    if (obj.input) {
        addInput(obj.input);
    }

    if (obj.text) {
        addText(obj.text);
    }

    if (obj.attr) {
        addAttr(obj.attr);
    }

    if (obj.removeAttr) {
        removeAttr(obj.attr);
    }

    if (obj.data) {
        addData(obj.data);
    }

    if (obj.removeData) {
        removeData(obj.data);
    }

    if (obj.trigger) {
        isTrigger(obj.trigger);
    }

    if (obj.focus) {
        isFocus(obj.focus);
    }

    if (obj.prop) {
        isProp(obj.prop);
    }
}

// ajax Error
const ajaxErrorData = (obj) => {
    let json = {};

    if (isEmpty(obj.responseJSON)) {
        console.log(obj);
    } else {
        json.case = true;
        json.msg = isEmpty(obj.responseJSON.msg) ? (obj.status + ' ERROR') : obj.responseJSON.msg;

        if (!isEmpty(obj.responseJSON.redirect)) {
            json.location = {
                'case': obj.responseJSON.redirect,
                'url': obj.responseJSON.url,
            }
        }

        actionAlert(json);
    }

    spinnerHide();
}

// ajax loading spinner Show
const spinnerShow = () => {
    $("#spinner-div").show();
}

// ajax loading spinner Hide
const spinnerHide = () => {
    $("#spinner-div").hide();
}

// add html
const addHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).html(data.html);
    });
}

// before html
const beforeHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).before(data.html);
    });
}

// after html
const afterHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).after(data.html);
    });
}

// append html
const appendHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).append(data.html);
    });
}

// prepend html
const prependHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).prepend(data.html);
    });
}

// opener html
const openerAddHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(window.opener.document).find(data.selector).html(data.html);
    });
}

// opener before html
const openerBeforeHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(window.opener.document).find(data.selector).before(data.html);
    });
}

// opener after html
const openerAfterHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(window.opener.document).find(data.selector).after(data.html);
    });
}

// opener append html
const openerAppendHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(window.opener.document).find(data.selector).append(data.html);
    });
}

// opener prepend html
const openerPrependHtml = (obj) => {
    $.each(obj, function (key, data) {
        $(window.opener.document).find(data.selector).prepend(data.html);
    });
}

// add css
const addCss = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).css(data.css, data.val);
    });
}

// remove css
const removeCss = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).css(data.css, '');
    });
}

// add class
const addClass = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).addClass(data.class);
    });
}

// remove class
const removeClass = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).removeClass(data.class);
    });
}

// add input
const addInput = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).val(data.input);
    });
}

// add Text
const addText = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).text(data.text);
    });
}

// add attr
const addAttr = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).attr(data.attr, data.val);
    });
}

// remove attr
const removeAttr = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).removeAttr(data.attr);
    });
}

// add data
const addData = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).data(data.name, data.data);
    });
}

// remove data
const removeData = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).removeData(data.name);
    });
}

// trigger
const isTrigger = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).trigger(data.event);
    });
}

// prop
const isProp = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).prop(data.event, data.val);
    });
}

// remove
const isRemove = (obj) => {
    $.each(obj, function (key, data) {
        $(data.selector).remove();
    });
}

// focus
const isFocus = (target) => {
    $(target).focus();
}

// location url
const locationUrl = (obj) => {
    switch (obj.case) {
        case 'replace':
            window.location.replace(obj.url);
            break;

        case 'reload':
            window.location.reload();
            break;

        case 'back':
            window.history.back();
            break;

        case 'href':
            location.href = obj.url;
            break;

        case 'blank':
            const openNewWindow = window.open("about:blank");
            openNewWindow.location.href = obj.url;
            break;
    }
}

// alert
const actionAlert = (obj) => {
    alert(obj.msg);

    if (obj.case) {
        delete obj.case;
        delete obj.msg;
        ajaxSuccessData(obj);
    }
}

// toast
const actionToast = (obj) => {
    $.toast({
        heading: '',
        text: obj.msg,
        icon: '',
        position: 'mid-center',
        stack: false,
        hideAfter: 2000, // 3초 후 사라짐
    });;

    if (obj.case) {
        delete obj.case;
        delete obj.msg;
        ajaxSuccessData(obj);
    }
}

// file input check
const fileCheck = (_this, inputTarget = null) => {
    // plupload 체크 제외
    if ($(_this).closest('#plupload').length > 0) {
        return false;
    }

    const str = $(_this).val();
    const fileName = str.split('\\').pop().toLowerCase();

    // 등록 파일 없으면 체크 안함
    if (isEmpty(str)) {
        return false;
    }

    // 1. 파일명에 특수문자 체크
    const pattern = /[\{\}\/?,;:|*~`!^\+<>@\#$%&\\\=\'\"]/gi;
    if (pattern.test(fileName)) {
        // 파일명에 허용된 특수문자 '-', '_', '(', ')', '[', ']', '.'

        switch (script_lang) {
            case 'KR':
                alert('파일명에서 특수 문자를 제거해 주세요.');
                break;

            case 'ENG':
                alert('Please remove special characters from the file name.');
                break;

            default:
                break;
        }

        if (!isEmpty(inputTarget)) {
            $(inputTarget).val('');
        }

        return false;
    }

    // 2. 확장자 체크
    const accept = $(_this).data('accept');
    if (!isEmpty(accept)) {
        const extArr = accept.split('|');
        const ext = str.split('.').pop().toLowerCase();

        if ($.inArray(ext, extArr) == -1) {
            alert(`${accept.replace(/\|/g, ', ')}` + ' only');

            if (!isEmpty(inputTarget)) {
                $(inputTarget).val('');
            }

            return false;
        }
    }

    // 3. 파일 크기 체크 (기본 업로드 크기 20MB data 값으로 업로드 크기 개별 조절)
    const size = $(_this)[0].files[0].size;
    const customSize = $(_this).data('size');
    const maxFileSize = isEmpty(customSize) ? 20 : parseInt(customSize);
    if (size > (maxFileSize * 1024 * 1024)) {

        switch (script_lang) {
            case 'KR':
                alert(`첨부 파일 크기는 ${maxFileSize}MB 내에서 등록할 수 있습니다.`);
                break;

            case 'ENG':
                alert(`The attached file size can be registered within ${maxFileSize}MB.`);
                break;

            default:
                break;
        }

        if (!isEmpty(inputTarget)) {
            $(inputTarget).val('');
        }

        return false;
    }

    if (!isEmpty(inputTarget)) {
        $(inputTarget).val(fileName);
    }

    return true;
}

const fileDelCheck = (delTarget) => {
    if (delTarget.length > 0) {

        switch (script_lang) {
            case 'KR':
                alert('첨부파일 삭제후 업로드 해주세요.');
                break;

            case 'ENG':
                alert('Please upload the attached file after deleting it.');
                break;

            default:
                break;
        }

        return false;
    }

    return true;
}

// null Check
const isEmpty = (str) => {
    if (typeof str === 'string') {
        str = str.replace(/ /g, ''); // 공백 제거
        str = str.replace(/\n/g, ""); // 줄바꿈 제거
    }

    return (typeof str === "undefined" || str === null || str === "") ? true : false;
}

const emailCheck = (email) => {
    const emailRegExp = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegExp.test($.trim(email))) {

        switch (script_lang) {
            case 'KR':
                alert('올바른 이메일 형식이 아닙니다.');
                break;

            case 'ENG':
                alert('Email address seems incorrect. (check @ and .’s)');
                break;

            default:
                break;
        }

        return false;
    }

    return true;
}

const checkPassword = (password) => {
    if (!pwdREGEX(password)) {

        switch (script_lang) {
            case 'KR':
                alert('올바른 비밀번호 형식이 아닙니다.');
                break;

            case 'ENG':
                alert('Invalid password format.');
                break;

            default:
                break;
        }

        return false;
    }

    return true;
}

const rowspanizer = (target, cols = [0]) => {
    $(target).rowspanizer({
        cols :cols,
        vertical_align: "middle"
    });
}

// form Data serialize convert Json
const serializeConvertJson = (obj) => {
    let jsonData = {};

    $(obj).each(function (k, v) {
        jsonData[v.name] = v.value;
    });

    return jsonData;
}

// form Data serialize
const formSerialize = (target) => {
    let formData = serializeConvertJson($(target).serializeArray());
    const targetData = $(target).data();

    Object.entries(targetData).forEach(([key, value]) => {
        formData[key] = value;
    });

    return formData;
}

// form Data
const newFormData = (target) => {
    let formData = new FormData($(target)[0]);
    const targetData = $(target).data();

    Object.entries(targetData).forEach(([key, value]) => {
        formData.append(key, value);
    });

    return formData;
}

// 다음 우편번호 검색
const callPostCode = (callback) => {
    new daum.Postcode({
        oncomplete: function (data) {
            callback(data);
        }
    }).open();
}

// mobile check
const isMobile = () => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// 날짜별 요일
const getYoil = (date) => {
    const week = ['일', '월', '화', '수', '목', '금', '토'];
    return week[new Date(date).getDay()];
}

// add comma
const comma = (str) => {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

// remove comma
const uncomma = (str) => {
    str = String(str);
    return str.replace(/[^\d]+/g, '');
}

const isMaxLength = (str, size) => {
    if (str.length > size) {

        switch (script_lang) {
            case 'KR':
                alert("최대 " + size + "자까지 입력 가능합니다.");
                break;

            case 'ENG':
                alert("Maximum" + size + " can be entered up to a letter.");
                break;

            default:
                break;
        }

        str = str.substring(0, size);
    }

    return str;
}

const isMaxByte = (str, size) => {
    const str_len = str.length;
    let rbyte = 0;
    let rlen = 0;
    let one_char = "";
    let i = 0;

    for (i; i < str_len; i++) {
        one_char = str.charAt(i);

        if (escape(one_char).length > 4) {
            rbyte += 2; // 한글 2Byte
        } else {
            rbyte++; // 영문 등 1Byte
        }

        if (rbyte <= size) {
            rlen = i + 1; // return 할 문자열 갯수
        }
    }

    if (rbyte > size) {

        switch (script_lang) {
            case 'KR':
                alert("최대 " + size + "byte까지 입력 가능합니다.");
                break;

            case 'ENG':
                alert("Maximum" + size + "byte is allowed.");
                break;

            default:
                break;
        }

        str = str.substr(0, rlen);
    }

    return str;
}

// Refresh captcha
const refreshCaptcha = () => {
    $.ajax({
        type: "POST",
        url: '/common/captcha-make',
        data: {},
        success: function (data) {
            $('#captcha').val('');
            $('#captcha_img').attr('src', data);
        },
        error: function (error) {
            ajaxErrorData(error)
        }
    });
}


// captcha check
const captchaCheck = () => {
    const captcha = $('#captcha');

    if (captcha.length > 0 && isEmpty(captcha.val())) {

        switch (script_lang) {
            case 'KR':
                alert("인증 문자가 일치하지 않습니다.");
                break;

            case 'ENG':
                alert("Your response to the CAPTCHA appears to be invalid. Please re-verify that you're not a robot.");
                break;

            default:
                break;
        }

        captcha.focus();
        return false;
    }

    return true;
}

// 아이디 REGX (길이: 4~14자, 영문자 1개 이상 포함, 숫자 또는 특수문자 1개 이상 포함, 영문 + 숫자 + 특수문자)
const uidREGEX = (uid) => {
    const uidRegex = /^(?=.*[a-zA-Z])(?=.*[\d\W])[a-zA-Z\d\W]{4,14}$/;
    return uidRegex.test(uid);
}

// 비밀번호 REGX (4~14자, 소문자 1개 이상 포함, 또는 특수문자 1개 이상 포함, 소문자 + 숫자 + 특수문자, 대문자 사용물가)
const pwdREGEX = (pwd) => {
    const pwdRegex = /^(?=.*[a-z])(?=.*[\d\W])[a-z\d\W]{4,14}$/;
    return pwdRegex.test(pwd);
}

// phone only number Auto Hyphen
$(document).on("keyup", "input[phoneHyphen]", function () {
    const phone = $(this).val().replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/, "$1-$2-$3").replace("--", "-");
    $(this).val(phone);
});

// numberFormat add comma
$(document).on("keyup", "input[priceFormat]", function () {
    const num = uncomma($(this).val()).replace(/[^0-9\s+]/g, "")
    $(this).val(comma(isNaN(num) ? '' : num));
});

// onlyNumber
$(document).on("keyup", "input[onlyNumber]", function () {
    const num = $(this).val().replace(/[^0-9\s+]/g, "");
    $(this).val(isNaN(num) ? '' : num);
});

// number and hyphen
$(document).on("keyup", "input[numberHyphen]", function () {
    const num = $(this).val().replace(/[^0-9-]/g, "");
    $(this).val(num);
});

// 공백입력 방지
$(document).on("keyup", "input[noneSpace]", function () {
    const val = $(this).val().replace(/ /g, "");
    $(this).val(val);
});

// only Korean (공백허용) 한글만 입력
$(document).on("keyup", "input[onlyKo]", function () {
    const ko = $(this).val().replace(/[^가-힣\s+]/gi, "");
    $(this).val(ko);
});

// None Korean (공백허용) 한글 입력 막기
$(document).on("keyup", "input[noneKo]", function () {
    const nonKo = $(this).val().replace(/[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/g, "");
    $(this).val(nonKo);
});

// only Korean Alert (공백허용)
$(document).on("keyup", "input[onlyKoAlert]", function () {
    const val = $(this).val();
    const isOnlyKorean = /^[ㄱ-ㅎㅏ-ㅣ가-힣\s]+$/.test(val); // 한글 + 공백 허용

    if (!isOnlyKorean && val.length > 0) {

        switch (script_lang) {
            case 'KR':
                alert("한글만 입력 가능합니다.");
                break;

            case 'ENG':
                alert("Only enter Korean.");
                break;

            default:
                break;
        }

        $(this).val('');
    }
});

// only English (공백허용)
$(document).on("keyup", "input[onlyEn]", function () {
    const en = $(this).val().replace(/[^a-z\s+]/gi, "");
    $(this).val(en);
});

// only English Alert (공백허용)
$(document).on("keyup", "input[onlyEnAlert]", function () {
    const val = $(this).val();
    const isOnlyEnglishOrSpace = /^[a-zA-Z\s]*$/.test(val);

    if (!isOnlyEnglishOrSpace && val.length > 0) {

        switch (script_lang) {
            case 'KR':
                alert("영문만 입력 가능합니다.");
                break;

            case 'ENG':
                alert("Only enter English.");
                break;

            default:
                break;
        }

        $(this).val('');
    }
});

// English & number (공백허용)
$(document).on("keyup", "input[onlyEnNum]", function () {
    const en = $(this).val().replace(/[^a-z0-9\s+]/gi, "");
    $(this).val(en);
});

// EnglishName (공백, -, _ 허용)
$(document).on("keyup", "input[enName]", function () {
    const en = $(this).val().replace(/[^a-z\s_\-]/gi, "");
    $(this).val(en);
});

// English 첫글자 대문자
$(document).on("keyup", "input[upperCase]", function () {
    const str = $(this).val();
    $(this).val(str.charAt(0).toUpperCase() + str.slice(1));
});
