@extends('2fa::layout')
@section('content')
<form method="post" style="margin: 0">
    {{ csrf_field() }}
    @if($enabled)
        <p>{{ trans('2fa::base.already_enabled') }}</p>
        <a class="btn btn-primary" href="#" onclick="window.history.go(-1); return false;">{{ trans('2fa::base.back') }}</a>
    @else
        <div class="row">
            <div class="col-sm-8">
                <p class="mt-2">{{ trans('2fa::base.setup_description') }}</p>
            </div>
            <div class="col-sm-4 text-center">
                <img class="img-thumbnail mb-4" src="{{ $qr }}">
            </div>
        </div>

        @include('2fa::includes.form', ['btnText' => trans('2fa::base.enable')])
    @endif
</form>
@endsection
