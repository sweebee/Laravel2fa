@extends('2fa::layout')

@section('content')
<form method="post">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="2faCode">2-staps verificatie code</label>
        <input id="2faCode" class="form-control @if($errors->first(config('2fa.code_input_name'))) is-invalid @endif" type="text" name="2fa_code">
        @if($errors->first(config('2fa.code_input_name')))
            <div class="invalid-feedback">
                {{ $errors->first(config('2fa.code_input_name')) }}
            </div>
        @endif
    </div>
    <button class="btn btn-primary">Valideren</button>
</form>
@endsection
