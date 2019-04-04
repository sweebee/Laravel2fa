@extends('2fa::layout')
@section('content')
<form method="post">
    {{ csrf_field() }}
    @if($enabled)
        <p>Je hebt 2-stapsverificatie al ingeschakeld.</p>
        <a class="btn btn-primary" href="#" onclick="window.history.go(-1); return false;">Terug</a>
    @else
    <div class="text-center">
        <img class="img-thumbnail mb-4" src="{{ $qr }}">
    </div>
    <div class="form-group">
        <label for="2faCode">Verificatie code</label>
        <input type="text" id="2faCode" class="form-control" name="2fa_code">
    </div>
    <button class="btn btn-primary">Valideren</button>
    @endif
</form>
@endsection
