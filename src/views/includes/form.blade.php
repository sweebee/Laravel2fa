<form method="post" style="margin: 0">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="2faCode">Code</label>
        <input id="2faCode" class="form-control @if($errors->first(config('2fa.code_input_name'))) is-invalid @endif" type="text" name="{{ config('2fa.code_input_name') }}">
        @if($errors->first(config('2fa.code_input_name')))
            <div class="invalid-feedback">
                Je hebt een ongeldige code ingevuld.
            </div>
        @endif
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="{{ config('2fa.remember_input_name') }}" id="2faRemember" class="form-check-input">
            <label for="2faRemember" class="form-check-label">
                Niet opnieuw vragen op deze computer
            </label>
        </div>
    </div>
    <button class="btn btn-dark">{{ $btnText ?? 'Valideren' }}</button>
</form>
