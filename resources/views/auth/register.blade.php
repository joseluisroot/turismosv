@extends('layouts.account')
@section('title', 'Crear cuenta')
@section('content')
<p class="eyebrow">Únete a la comunidad</p>
<h2>Crea tu cuenta</h2>
<p class="form-intro">Empieza a construir tu pasaporte de experiencias.</p>
@error('social')<div class="status-error">{{ $message }}</div>@enderror
<div class="social-access"><a href="{{ route('social.redirect','google') }}"><x-social-icon provider="google"/><span>Registrarme con Google</span></a><a href="{{ route('social.redirect','facebook') }}"><x-social-icon provider="facebook"/><span>Registrarme con Facebook</span></a></div><div class="account-divider"><span>o crea una contraseña</span></div>
<form method="post" action="{{ route('register') }}" class="account-form">
    @csrf
    <label>Nombre completo<input name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>@error('name')<small>{{ $message }}</small>@enderror</label>
    <label>Correo electrónico<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small>{{ $message }}</small>@enderror</label>
    <label>Contraseña<input type="password" name="password" autocomplete="new-password" required><span>Al menos 8 caracteres, con letras y números.</span>@error('password')<small>{{ $message }}</small>@enderror</label>
    <label>Confirma tu contraseña<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
    <label class="legal-check"><input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}><span>Acepto los <a href="{{ route('legal.terms') }}" target="_blank">Términos y Condiciones</a> y la <a href="{{ route('legal.privacy') }}" target="_blank">Política de Privacidad</a>.</span></label>
    @error('terms')<small class="field-error">{{ $message }}</small>@enderror
    <button class="primary-button form-submit" type="submit">Crear mi cuenta</button>
</form>
<p class="form-switch">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Ingresa aquí</a></p>
@endsection
