<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes,viewport-fit=cover">
<meta name="format-detection" content="telephone=no, address=no, email=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="Author" content="{{ getAppName() }}">
<meta name="Keywords" content="{{ getAppName() }}">
<meta name="description" content="{{ getAppName() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ getAppName() }}@yield('addTitle')</title>

<link rel="icon" href="{{ asset('assets/image/favicon.ico') }}">

@include('layouts.components.baseStyle')
@include('layouts.components.baseScript')
