@extends('layouts.account')
@section('title', 'Ingresar')
@section('content')
<p class="eyebrow">Bienvenido de nuevo</p>
<h2>Ingresa a TurismoSV</h2>
<p class="form-intro">Continúa descubriendo lugares y experiencias.</p>
<form method="post" action="{{ route('login') }}" class="account-form">
    @csrf
    <label>Correo electrónico<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<small>{{ $message }}</small>@enderror</label>
    <label>Contraseña<input type="password" name="password" autocomplete="current-password" required>@error('password')<small>{{ $message }}</small>@enderror</label>
    <label class="legal-check"><input type="checkbox" name="remember" value="1"><span>Mantener mi sesión iniciada</span></label>
    <button class="primary-button form-submit" type="submit">Ingresar</button>
</form>
<p class="form-switch">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
@endsection
