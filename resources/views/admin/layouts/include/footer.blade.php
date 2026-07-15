<footer id="footer">
    <button type="button" class="btn-top js-btn-top">
        <img src="/assets_admin/image/common/ic_top.png" alt="">
        TOP
    </button>

    <div class="footer-wrap inner-layer">
        <div class="footer-con">
            <span class="footer-logo">Secretariat of {{ env('APP_NAME') }}</span>
            @php($siteInfo = config('site.app.info'))

            {{ $siteInfo['address'] }}

            <ul>
                <li><strong>E-Mail</strong>. <a href="mailto:{{ $siteInfo['email'] }}" target="_blank">{{ $siteInfo['email'] }}</a></li>
                <li><strong>TEL</strong>. <a href="tel:{{ $siteInfo['tel'] }}" target="_blank">{{ $siteInfo['tel'] }}</a></li>
                <li><strong>FAX</strong>. {{ $siteInfo['fax'] }}</li>
            </ul>
        </div>
    </div>

    <p class="copy">Copyright © {{ env('APP_NAME') }}. All Rights Reserved.</p>
</footer>
