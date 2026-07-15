<script>
    $(function(e){
        popupRolling();

        $(window).resize(function(e){
            popupRolling();
        });
    });

    function popupRolling(){
        if ($('.js-popup-rolling .popup-contents').length === 0) {
            $('.popup-rolling-wrap').closest('.popup-wrap').remove();
        }

        if($('.js-popup-rolling .popup-contents').length > 1){
            if($('.js-popup-rolling').hasClass('n3')){
                var $setting = '',
                    $num1 = 3,
                    $num2 = 1;
            }else{
                var $setting = 'unslick',
                    $num1 = '',
                    $num2 = '';
            }

            $('.js-popup-rolling').not('.slick-initialized').slick({
                dots: false,
                arrows: true,
                autoplay: true,
                autoplaySpeed: 3000,
                speed: 1000,
                infinite: false,
                adaptiveHeight: true,
                slidesToShow: 3,
                slidesToScroll: 1,
                responsive: [{
                    breakpoint: 9999,
                    settings: $setting,
                    slidesToShow: $num1,
                    slideToScroll: $num1,
                },
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },{
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                        }
                    }]
            });
        }
    }

    function setCookie24(name, value, expiredays) {
        var todayDate = new Date();

        todayDate.setDate(todayDate.getDate() + expiredays);

        document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
    }

    $(document).on('click', '.js-main-pop-close', function () {
        $(this).closest('.popup-contents').remove();
        popupRolling();
    });

    $(document).on('click', '.btn-pop-today-close', function () {
        const rolling = $(this).closest('.popup-contents');

        setCookie24(rolling.attr('id'), 'done', 1);

        rolling.remove();

        popupRolling();
    });
</script>