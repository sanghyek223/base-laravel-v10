<script>
    function setCookie24(name, value, expiredays) {
        var todayDate = new Date();

        todayDate.setDate(todayDate.getDate() + expiredays);

        document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
    }

    $(document).on('click', '.js-main-pop-close', function () {
        $(this).closest('.popup-wrap').remove();
    });

    $(document).on('click', '.btn-pop-today-close', function () {
        const layer = $(this).closest('.popup-wrap');

        setCookie24(layer.attr('id'), 'done', 1);

        layer.remove();
    });
</script>