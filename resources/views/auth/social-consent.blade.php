@extends('layouts.account')
@section('title','Completar registro')
@section('content')
<p class="eyebrow">Último paso</p><h2>Confirma tu cuenta</h2><p class="form-intro">{{ $registration['name'] }} · {{ $registration['email'] }}</p><p class="social-notice">{{ ucfirst($registration['provider']) }} confirmó esta identidad para iniciar sesión. TurismoSV no recibe ni almacena tu contraseña externa.</p>
<form method="post" action="{{ route('social.complete') }}" class="account-form">@csrf<label class="legal-check"><input type="checkbox" name="terms" value="1" required><span>Acepto los <a href="{{ route('legal.terms') }}" target="_blank">Términos y Condiciones</a> y la <a href="{{ route('legal.privacy') }}" target="_blank">Política de Privacidad</a>.</span></label>@error('terms')<small class="field-error">{{ $message }}</small>@enderror<button class="primary-button form-submit">Crear mi cuenta con {{ ucfirst($registration['provider']) }}</button></form>
@endsection
