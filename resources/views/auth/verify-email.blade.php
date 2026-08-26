@extends('layouts.account')
@section('title', 'Verifica tu correo')
@section('content')
<p class="eyebrow">Un paso de confianza</p><h2>Verifica tu correo</h2>
<p class="form-intro">Enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>. Ábrelo para activar tu perfil y proteger la credibilidad de la comunidad.</p>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
@if($localVerificationUrl)
<div class="local-verification">
    <strong>Modo de desarrollo local</strong>
    <p>Los correos no salen hacia Gmail mientras el sistema utiliza el controlador de registro.</p>
    <a class="primary-button form-submit" href="{{ $localVerificationUrl }}">Verificar correo en desarrollo</a>
</div>
@endif
<form method="post" action="{{ route('verification.send') }}">@csrf<button class="primary-button form-submit" type="submit">Reenviar enlace</button></form>
<form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="text-button" type="submit">Cerrar sesión</button></form>
@endsection
