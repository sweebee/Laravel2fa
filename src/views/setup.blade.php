@extends('2fa::layout')
@section('content')
<form method="post" style="margin: 0">
    {{ csrf_field() }}
    @if($enabled)
        <p>Je hebt 2-stapsverificatie al ingeschakeld.</p>
        <a class="btn btn-primary" href="#" onclick="window.history.go(-1); return false;">Terug</a>
    @else
        <div class="row">
            <div class="col-sm-8">
                <p class="mt-2">Scan de QR code met behup van de Google Authenticator en vul vervolgens de code in om de 2-staps verificatie in te schakelen.</p>
            </div>
            <div class="col-sm-4 text-center">
                <img class="img-thumbnail mb-4" src="{{ $qr }}">
            </div>
        </div>

        @include('2fa::includes.form', ['btnText' => 'Inschakelen'])
    @endif
</form>
@endsection
