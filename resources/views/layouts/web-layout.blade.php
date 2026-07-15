<!doctype html>
<html lang="ko" class="root-text-sm">
<head>
    @include('layouts.components.baseHead')
</head>
<body>

@include('layouts.include.header')

<section id="container" @empty($main_key) class="main" @endempty>
    @if(!empty($main_key))
        @include('layouts.include.sub-visual')
        @include('layouts.include.sub-menu-wrap')
    @endif

    @yield('contents')
</section>

@include('layouts.include.footer')

@include('components.spinner')

{{--addScript--}}
@yield('addScript')
</body>
</html>
