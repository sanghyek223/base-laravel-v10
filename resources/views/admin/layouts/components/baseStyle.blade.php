{{-- base css --}}
<link rel="stylesheet" href="{{ asset('assets_admin/css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets_admin/css/slick.css') }}">

<link rel="stylesheet" href="{{ asset('css/spinner.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/flatpickr/css/flatpickr.min.css') }}">

<style>
    select {border: 1px solid #ccc;}
    form select {height: 30px;}
</style>

{{-- addCss --}}
@yield('addStyle')
