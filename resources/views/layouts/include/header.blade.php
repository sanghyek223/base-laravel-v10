<!-- header -->
<div id="dim" class="js-dim"></div>
<header id="header" class="js-header">
    <div class="header-wrap inner-layer">
        <h1 class="header-logo">
            <a href="{{ route('main') }}">
                <img src="/assets/image/common/h1_logo.png" alt="" class="m-hide">
                <img src="/assets/image/common/h1_logo_m.png" alt="" class="m-show">
            </a>
        </h1>

        <nav id="gnb">
            <div class="m-gnb-header">
                <div class="util-wrap inner-layer">
                    <ul class="util-menu">
                        @guest('web')
                            <li><a href="{{ route('login') }}"><img src="/assets/image/common/ic_login_m.png" alt="">LOGIN</a></li>
                            <li><a href="{{ route('auth.signup', ['step' => 'step1']) }}"><img src="/assets/image/common/ic_signup_m.png" alt="">Join</a></li>
                        @else
                            <li><a href="{{ route('mypage') }}"><img src="/assets/image/common/ic_signup_m.png" alt="">마이페이지</a></li>
                            <li><a href="javascript:logout();"><img src="/assets/image/common/ic_login_m.png" alt="">로그아웃</a></li>
                        @endguest
                    </ul>

                    <button type="button" class="btn-menu-close js-btn-menu-close"><span class="hide">메뉴 닫기</span></button>
                </div>
            </div>

            <div class="gnb-wrap">
                <ul class="gnb js-gnb">
                    @foreach($menu['main'] as $key => $val)
                        @if($val['continue']) @continue @endif
                        @if($val['dev'] && !isDev()) @continue @endif

                        <li>
                            <a href="{{ empty($val['url']) ? route($val['route'], $val['param']) : $val['url'] }}" @if($val['blank']) target="_blank" @endif>
                                <span>{{ $val['name'] }}</span>
                            </a>

                            @if(!empty($menu['sub'][$key]))
                                <ul>
                                    @foreach($menu['sub'][$key] ?? [] as $subKey => $subVal)
                                        @if($subVal['continue']) @continue @endif
                                        @if($subVal['dev'] && !isDev()) @continue @endif

                                        <li>
                                            <a href="{{ empty($subVal['url']) ? route($subVal['route'], $subVal['param']) : $subVal['url'] }}" @if($subVal['blank']) target="_blank" @endif>
                                                {{ $subVal['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="btn-wrap mt-0">
                <a href="javascript:void(0);" class="btn btn-line color-type5">ENGLISH</a>
            </div>
        </nav>

        <div class="util-wrap">
            <ul class="util-menu">
                <li><a href="javascript:void(0);"><img src="/assets/image/common/ic_eng.png" alt="">ENG</a></li>

                @guest
                    <li><a href="{{ route('login') }}"><img src="/assets/image/common/ic_login.png" alt="">로그인</a></li>
                    <li><a href="{{ route('auth.signup', ['step' => 'step1']) }}"><img src="/assets/image/common/ic_signup.png" alt="">회원가입</a></li>
                @else
                    <li><a href="{{ route('mypage') }}"><img src="/assets/image/common/ic_signup.png" alt="">마이페이지</a></li>
                    <li><a href="javascript:logout();"><img src="/assets/image/common/ic_login.png" alt="">로그아웃</a></li>

                    @auth('admin')
                        <li><a href="/admin"><img src="/assets/image/common/ic_signup.png" alt="">Admin</a></li>
                    @endif
                @endguest

                <li><a href="javascript:void(0);" class="js-gnb-pop-open"><img src="/assets/image/common/ic_all_menu.png" alt=""></a></li>
            </ul>
        </div>

        <button type="button" class="btn btn-menu-open js-btn-menu-open"><span class="hide">메뉴 열기</span></button>
    </div>

    <div id="gnb-pop" class="inner-layer">
        <p class="gnb-pop-title">SITE MAP</p>

        <div class="gnb-pop">
            <div class="gnb-pop-list">
                @foreach($menu['main'] as $key => $val)
                    @if($val['continue']) @continue @endif
                    @if($val['dev'] && !isDev()) @continue @endif

                    <div>
                        <p class="title">{{ $val['name'] }}</p>

                        @if(!empty($menu['sub'][$key]))
                            <ul>
                                @foreach($menu['sub'][$key] ?? [] as $subKey => $subVal)
                                    @if($subVal['continue']) @continue @endif
                                    @if($subVal['dev'] && !isDev()) @continue @endif

                                    <li>
                                        <a href="{{ empty($subVal['url']) ? route($subVal['route'], $subVal['param']) : $subVal['url'] }}" @if($subVal['blank']) target="_blank" @endif>
                                            {{ $subVal['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="gnb-pop-list gnb-pop-list-blue">
                @guest
                    <div>
                        <p class="title">LOGIN</p>
                        <ul>
                            <li><a href="{{ route('login') }}">로그인</a></li>
                            <li><a href="{{ route('auth.signup', ['step' => 'step1']) }}">회원가입</a></li>
                            <li><a href="{{ route('auth.find') }}">아이디/비밀번호찾기</a></li>
                        </ul>
                    </div>
                @else
                    <div>
                        <p class="title">MY PAGE</p>
                        <ul>
                            <li><a href="javascript:void(0);">개인정보수정</a></li>
                            <li><a href="javascript:void(0);">비밀번호변경</a></li>
                            <li><a href="javascript:void(0);">회비 납부 현황</a></li>
                            <li><a href="javascript:void(0);">학술행사 참석 현황</a></li>
                            <li><a href="javascript:void(0);">회원탈퇴</a></li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>

        <button type="button" class="btn btn-gnb-pop-close js-gnb-pop-close"><span class="hide">메뉴 닫기</span></button>
    </div>
</header>
<!-- //header -->
