<header id="header" class="js-header">
    <div class="header-wrap inner-layer">
        <h1 class="header-logo">
            <a href="{{ route('main') }}"><img src="/assets/image/common/h1_logo.png"></a>
        </h1>

        <div class="util-menu-wrap">
            <p class="user-info">
                <b>접속계정 :</b> <span style="font-weight: bold; color: #6d6df2; margin-left: 5px;">{{ thisUser()->uid ?? '' }}</span>
            </p>

            <ul class="util-menu">
                <li><a href="{{ env('APP_URL') }}" class="btn btn-util color-type17" target="_blank">{{ env('APP_NAME') }}</a></li>
                <li><a href="javascript:logout();" class="btn btn-util color-type5">Logout</a></li>
            </ul>
        </div>
    </div>

    <nav id="gnb">
        <div class="gnb-wrap inner-layer">
            <ul class="gnb js-gnb">
                @foreach($menu['main'] as $key => $val)
                    @if($val['dev'] && !isDev() /* 개발자메뉴 */)
                        @continue
                    @endif

                    @if($val['continue'] /* 노출 안하는 메뉴 */)
                        @continue
                    @endif

                    <li class="{{ ($main_key ?? '') == $key ? 'on' : '' }}">
                        <a href="javascript:void(0);">{{ $val['name'] }}</a>

                        @if(!empty($menu['sub'][$key]))
                            <ul>
                                @foreach($menu['sub'][$key] ?? [] as $sKey => $sVal)
                                    @if($sVal['dev'] && !isDev() /* 개발자메뉴 */)
                                        @continue
                                    @endif

                                    @if($sVal['continue'] /* 노출 안하는 메뉴 */)
                                        @continue
                                    @endif

                                    <li>
                                        <a href="{{ empty($sVal['route']) ? $sVal['url'] : route($sVal['route'], $sVal['param']) }}">{{ $sVal['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="gnb-bg js-gnb-bg"></div>
    </nav>
</header>
