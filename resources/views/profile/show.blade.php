@extends('layouts.account')
@section('title', 'Mi perfil')
@section('content')
<p class="eyebrow">Pasaporte activo</p><h2>{{ $user->name }}</h2>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
<div class="profile-summary"><span>{{ strtoupper(substr($user->name, 0, 1)) }}</span><div><strong>{{ $user->email }}</strong><small>Correo verificado · Perfil viajero</small></div></div>
<dl class="profile-facts"><div><dt>Rol</dt><dd>Viajero</dd></div><div><dt>Verificadas</dt><dd>{{ $user->verified_check_ins_count }}</dd></div><div><dt>Pendientes</dt><dd>{{ $user->pending_check_ins_count }}</dd></div></dl>
@if($user->checkIns->isNotEmpty())<div class="profile-checkins"><h3>Visitas recientes</h3>@foreach($user->checkIns as $checkIn)<a href="{{ route('places.show',$checkIn->place) }}#check-in"><span>{{ $checkIn->place->name }}<small>{{ $checkIn->visited_on->translatedFormat('d M Y') }}</small></span><strong class="status-{{ $checkIn->status }}">{{ match($checkIn->status){'verified'=>'Verificada','rejected'=>'Rechazada',default=>'Pendiente'} }}</strong></a>@endforeach</div>@else<p class="profile-note">Todavía no has registrado visitas. Explora una ficha de lugar para comenzar.</p>@endif
<a class="primary-button form-submit" href="{{ route('passport.show') }}">Abrir mi pasaporte</a>
<a class="text-button profile-explore" href="{{ route('home') }}#lugares">Explorar lugares</a>
<form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="text-button" type="submit">Cerrar sesión</button></form>
@endsection
