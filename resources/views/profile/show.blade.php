@extends('layouts.account')
@section('title', 'Mi perfil')
@section('content')
<p class="eyebrow">Pasaporte activo</p><h2>{{ auth()->user()->name }}</h2>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
<div class="profile-summary"><span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->email }}</strong><small>Correo verificado · Perfil viajero</small></div></div>
<dl class="profile-facts"><div><dt>Rol</dt><dd>Viajero</dd></div><div><dt>Experiencias</dt><dd>0</dd></div><div><dt>Reseñas</dt><dd>0</dd></div></dl>
<p class="profile-note">Pronto podrás coleccionar check-ins, escribir reseñas y completar tu perfil público.</p>
<a class="primary-button form-submit" href="{{ route('home') }}">Explorar lugares</a>
<form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="text-button" type="submit">Cerrar sesión</button></form>
@endsection
