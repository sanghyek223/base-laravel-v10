@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    index
@endsection

@section('addScript')
    @isset($layerPopups)
        @include('popup.board.layer-popup')
        @include('popup.board.layer-popup-script')
    @endisset

    @isset($rollingPopups)
        @include('popup.board.rolling-popup')
        @include('popup.board.rolling-popup-script')
    @endisset
@endsection
