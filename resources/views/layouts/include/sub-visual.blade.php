<article class="sub-visual">
    <div class="sub-visual-con inner-layer">
        <div class="sub-visual-text">
            <h2 class="sub-visual-tit">{{ $menu['main'][$main_key]['name'] }}</h2>

            <ul class="breadcrumb">
                <li class="home"><img src="/assets/image/sub/img_breadcrumb.png" alt=""></li>
                <li>{{ $menu['main'][$main_key]['name'] }}</li>

                @if(!empty($sub_key) && !empty($menu['sub'][$main_key][$sub_key]))
                    <li class="font-suit">&gt;</li>
                    <li>{{ $menu['sub'][$main_key][$sub_key]['name'] }}</li>
                @endif
            </ul>
        </div>
    </div>
</article>