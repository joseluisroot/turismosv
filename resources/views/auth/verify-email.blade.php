@extends('layouts.account')
@section('title', 'Verifica tu correo')
@section('content')
<p class="eyebrow">Un paso de confianza</p><h2>Verifica tu correo</h2>
<p class="form-intro">Enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>. Ábrelo para activar tu perfil y proteger la credibilidad de la comunidad.</p>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
<form method="post" action="{{ route('verification.send') }}">@csrf<button class="primary-button form-submit" type="submit">Reenviar enlace</button></form>
<form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="text-button" type="submit">Cerrar sesión</button></form>
@endsection
