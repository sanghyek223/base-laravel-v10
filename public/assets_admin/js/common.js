$(function (e) {
	$width = $(window).innerWidth(),
    wWidth = windowWidth();

	$(document).ready(function (e) {
		btnTop();
        mainVisual();
        speakersRolling();
        boardRolling();
        sponsorRolling();
        subMenu();
        popup();

		if(wWidth < 1025){		
		}else{		
		}
		
		resEvt();
	});

	// resize
	function resEvt() {	      
		if (wWidth < 1025) {
			mGnb();		
			subConHeight();
			mTabMenu();

			if($('.js-dim').hasClass('mobile')){
				$('.js-dim').show();
				$('html, body').addClass('ovh');
			}     
			
		} else {	
            gnb();	
			tabMenu();
			if($('.js-dim').hasClass('mobile')){
				$('.js-dim').hide();
				$('html, body').removeClass('ovh');
			}
            $('.js-gnb > li > ul, .js-sub-menu-list ul').removeAttr('style');
            // $('.js-gnb > li').removeClass('on');
			$('.js-tab-menu, .js-tabcon-menu').removeAttr('style');		
			$('.js-btn-tab-menu, .js-btn-tabcon-menu').removeClass('on');
		}

		if(wWidth < 769){
			touchHelp();
		}
	}

	$(window).resize(function (e) {
		$width = $(window).innerWidth(),
		wWidth = windowWidth();
		resEvt();
	});

	$(window).scroll(function(e){
		if($(this).scrollTop() > 200){
			$('.js-btn-top').addClass('on');
		}else{
			$('.js-btn-top').removeClass('on');
		}
	});
});

function Mobile() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

function windowWidth() {
	if ($(document).innerHeight() > $(window).innerHeight()) {
		if (Mobile()) {
			return $(window).innerWidth();
		} else {
			return $(window).innerWidth() + 17;
		}
	} else {
		return $(window).innerWidth();
	}
}

function subConHeight(){
    $(document).ready(function(e){
        var subConHeight = $(window).outerHeight() - $('.js-header').outerHeight() - $('#footer').outerHeight();

        if ($('.sub-contents').closest('#popup-wrap').length <= 0) {
            setTimeout(function(e){
                $('.sub-contents').css('min-height', subConHeight);
            },100);
        }
    });
}

function gnb() {
    var max_h = 0;
    $('.js-gnb > li').each(function(e){
        var h = parseInt($(this).children('ul').outerHeight());
        if(max_h < h){
            max_h = h;
        }
    });
    $('.js-gnb > li > ul').height(max_h);
    $('.js-gnb > li > ul').css('height','100px');
    $('.js-gnb > li > a').off('click');
	$('.js-gnb > li').on('mouseenter',function(e){
        $('.js-gnb-bg, .js-gnb > li > ul').css('height',max_h);
        $('.js-gnb > li > ul').show();
    });
    $('.js-gnb').on('mouseleave', function(e){
        $('.js-gnb > li > ul').hide();
        $('.js-gnb-bg').css('height','');
    });
}

function mGnb(){
    $('.js-gnb > li').off('mouseenter');
    $('.js-gnb').off('mouseleave');
    $('.js-gnb > li > a').off().on('click',function(e){
        if($(this).next('ul').length){
            $(this).parent('li').toggleClass('on');
            $('.js-gnb > li > a').not(this).parent('li').removeClass('on');
            $(this).next('ul').stop().slideToggle();
            $('.js-gnb > li > a').not(this).next('ul').stop().slideUp();
            return false;
        }
    });
    
    $('.js-btn-menu-open').on('click',function(e){
        $('html, body').addClass('ovh');
        $('.js-dim').addClass('mobile').stop().fadeIn(100);
        $('#gnb').stop().animate({'right':0},400); 
    });
    $('.js-btn-menu-close, .js-dim').on('click',function(e){
        $('html, body').removeClass('ovh');
        $('.js-dim').removeClass('mobile').stop().fadeOut(100);
        $('#gnb').stop().animate({'right':'-100%'},400);
    });
}

function mainVisual(){
    if($('.js-main-visual .main-visual-con').length > 1){
        $('.js-main-visual').not('.slick-initialized').slick({
            dots: true,
            arrows: false,
			autoplay: true,
			autoplaySpeed: 3000,
			speed: 1000,
			infinite: true,
            fade: true,          
		});
    }
}

function speakersRolling(){
    if($('.js-speakers-rolling').length){
        $('.js-speakers-rolling').not('.slick-initialized').slick({
            dots: false,
            arrows: true,
            prevArrow: $('.btn-speakers-prev'),
            nextArrow: $('.btn-speakers-next'),
            autoplay: true,
            autoplaySpeed: 3000,
            speed: 1000,
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            cssEase: 'linear',
            responsive: [
                {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                }
                },
                {
                    breakpoint: 1024,
                    settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                    }
                }
            ]
        });
    }
}

function boardRolling(){
    if($('.js-board-rolling').length){
        $('.js-board-rolling ').not('.slick-initialized').slick({
            dots: false,
            arrows: false,
            autoplay: true,
            autoplaySpeed: 3000,
            speed: 1000,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            cssEase: 'linear',
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                    }
                }
            ]
        });
    }
}

function sponsorRolling(){
    if($('.js-sponsor-rolling').length){    
        $('.js-sponsor-rolling').not('.slick-initialized').slick({
            dots: false,
            arrows: true,
            prevArrow: $('.btn-sponsor-prev'),
            nextArrow: $('.btn-sponsor-next'),
            autoplay: true,
            autoplaySpeed: 3000,
            speed: 1000,
            infinite: true,
            slidesToShow: 6,
            slidesToScroll: 1,
            cssEase: 'linear',
            responsive: [{
                breakpoint: 1420,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                    }
            }]
        });
    }
}

function subMenu(){
	$('.js-btn-sub-menu').off().on('click', function (e) {
		$(this).next('ul').stop().slideToggle();
		$(this).toggleClass('on');
		$('.js-btn-sub-menu').not(this).removeClass('on').next('ul').stop().slideUp();
		return false;
	});
	$('body').off().on('click', function (e) {
		if ($('.js-sub-menu-list').has(e.target).length == 0) {
			$('.js-btn-sub-menu').removeClass('on');
			$('.js-btn-sub-menu:visible +  ul').stop().slideUp();
		}
	});
}

function tabMenu(){
	$('.js-tab-menu').each(function(e){
		var cnt = $(this).children('li').length;
		$(this).addClass('n'+cnt+'');
	});
	$('.js-tab-menu li, .js-tabcon-menu li').off('click');
	
	tabConMenu();
}

function mTabMenu(){
	var activeTab = $('.js-tab-menu li.on > a').html();
	$('.js-btn-tab-menu').html(activeTab);
	$('.js-btn-tab-menu').off().on('click',function(e){
		$(this).toggleClass('on');
		$(this).next('ul').stop().slideToggle();
		return false;
	});
	$('.js-tab-menu li').off().on('click',function(e){		
		var currentTab = $(this).html();
		$('.js-btn-tab-menu').html(currentTab);

		$(this).addClass('on');
		$(this).siblings().removeClass('on');

		$(this).parent('ul').stop().slideUp();
		$('.js-btn-tab-menu').removeClass('on');
	});

	var activeTabCon = $('.js-tabcon-menu li.on > a').html();	
	$('.js-btn-tabcon-menu').html(activeTabCon);
	$('.js-btn-tabcon-menu').off().on('click',function(e){
		$(this).toggleClass('on');
		$(this).next('ul').stop().slideToggle();
		return false;
	});
	$('.js-tabcon-menu li').off().on('click',function(e){		
		var currentTab = $(this).html();
		$('.js-btn-tabcon-menu').html(currentTab);

		$(this).addClass('on');
		$(this).siblings().removeClass('on');

		$(this).parent('ul').stop().slideUp();
		$('.js-btn-tabcon-menu').removeClass('on');
	});
	tabConMenu();
}

// tab menu type2
function tabConMenu(){
	$('.js-tabcon-menu li').on('click',function(e){	
		var cnt = $(this).index();
		$(this).addClass('on');
		$(this).siblings().removeClass('on');
		$('.js-tab-con').hide().eq(cnt).stop().fadeIn();
		return false;
	});
}

function quickMenu(){
	var currentPosition = parseInt($('.js-quick-menu').css('top')); 
	$(window).scroll(function() { 		
		$('.js-quick-menu').show();
		var position = $(window).scrollTop();
		
		if($(window).scrollTop() + $(window).height() > $(document).height() - 200){ 
			$('.js-quick-menu').stop().animate({'top':position + currentPosition - 200 + "px"},800); 
		}else{
			$('.js-quick-menu').stop().animate({'top':position + currentPosition + "px"},800); 
		}
	});	
}

function btnTop(){
	$('.js-btn-top').on('click',function(e){
	  $('html, body').stop().animate({'scrollTop':0},400);
		return false;
	});
}

function touchHelp(){
	$('.scroll-x').each(function(e){
		if($(this).height() < 180){
			$(this).addClass('small');
		}
		$(this).scroll(function(e){
			$(this).removeClass('touch-help');
		});
	});
}

function popup(){
    $('.js-pop-open').on('click',function(e){
        var popCnt = $(this).attr('href');
        $('html, body').addClass('ovh');
        if($(popCnt).hasClass('full')){            
            $(popCnt).css('display','block');
        }else{
            $(popCnt).css('display','flex');
        }
        return false;
    });
    $('.js-pop-close').on('click',function(e){
        $('html, body').removeClass('ovh');
        $(this).parents('.popup-wrap').css('display','none');
        return false;
    });
    $('.popup-wrap:not(#main-popup)').off().on('click', function (e){
		if ($('.popup-contents').has(e.target).length == 0){            
            $('html, body').removeClass('ovh');
			$('.popup-wrap').css('display','none');
		}
	});
    // if($('#main-popup').is(':visible')){
    //     $('html, body').addClass('ovh');   
    // }else{
    //     $('html, body').removeClass('ovh');
    // }
    var popImgLength = $('.js-pop-img-wrap > li').length;
    $('.js-pop-img-wrap').addClass('n'+popImgLength);
}