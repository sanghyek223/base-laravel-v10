<!-- footer -->
<footer id="footer">
    <button type="button" class="btn-top js-btn-top on">
        <img src="/assets/image/common/ic_top.png" alt="">
    </button>

    <div class="footer-wrap inner-layer">
        <div class="footer-con">
            <p>Copyright © The Korean Association of Internal Medicine. All rights reserved.</p>

            <p>
                <span><strong>주소 : </strong>{{ $infoConfig['address'] }}</span>
            </p>

            <p>
                <span><strong>E-mail : </strong><a href="mailto:{{ $infoConfig['email'] }}">{{ $infoConfig['email'] }}</a></span>
                <span><strong>Tel : </strong><a href="tel:{{ $infoConfig['tel'] }}">{{ $infoConfig['tel'] }}</a></a></span>
            </p>
        </div>

        <div class="link-wrap">
            <a href="javascript:void(0);" class="btn color-type1">개인정보 취급 방침</a>
            <a href="javascript:void(0);" class="btn color-type1">이메일 무단 수집거부</a>
        </div>
    </div>
</footer>
<!-- //footer -->
