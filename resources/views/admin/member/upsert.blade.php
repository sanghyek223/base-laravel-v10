@extends('layouts.popup-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-tit-wrap mt-0">
        <h3 class="sub-tit">회원 정보 수정</h3>
    </div>

    <form id="user-frm" method="post" data-sid="{{ $user->sid ?? 0 }}" data-case="user-update">
        <fieldset>
            <legend class="hide">회웑 정보 수정</legend>

            {{-- @include('auth.signup.form.postform') --}}

            <div class="btn-wrap text-center">
                <a href="javascript:window.close();" class="btn btn-type1 color-type4">Cancel</a>
                <button type="submit" class="btn btn-type1 color-type5">Submit</button>
            </div>
        </fieldset>
    </form>
@endsection

@section('addScript')
    <script>
        const form = '#user-frm';
        const dataUrl = '{{ route('member.data') }}';
    </script>
@endsection
