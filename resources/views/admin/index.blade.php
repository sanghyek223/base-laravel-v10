@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        <div class="main-contents">
            <h3 class="main-tit">
                {{ env('APP_NAME') }}
                <span>관리자 페이지 입니다.</span>
            </h3>
        </div>
    </section>
@endsection

@section('addScript')
@endsection
