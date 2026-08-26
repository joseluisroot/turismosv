@extends('layouts.account')
@section('title', 'Mi perfil')
@section('content')
<p class="eyebrow">Pasaporte activo</p><h2>{{ $user->name }}</h2>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
@error('social')<div class="status-error">{{ $message }}</div>@enderror
<div class="profile-summary"><span>{{ strtoupper(substr($user->name, 0, 1)) }}</span><div><strong>{{ $user->email }}</strong><small>Correo verificado · Perfil viajero</small></div></div>
<dl class="profile-facts"><div><dt>Rol</dt><dd>Viajero</dd></div><div><dt>Verificadas</dt><dd>{{ $user->verified_check_ins_count }}</dd></div><div><dt>Pendientes</dt><dd>{{ $user->pending_check_ins_count }}</dd></div></dl>
@if($user->checkIns->isNotEmpty())<div class="profile-checkins"><h3>Visitas recientes</h3>@foreach($user->checkIns as $checkIn)<a href="{{ route('places.show',$checkIn->place) }}#check-in"><span>{{ $checkIn->place->name }}<small>{{ $checkIn->visited_on->translatedFormat('d M Y') }}</small></span><strong class="status-{{ $checkIn->status }}">{{ match($checkIn->status){'verified'=>'Verificada','rejected'=>'Rechazada',default=>'Pendiente'} }}</strong></a>@endforeach</div>@else<p class="profile-note">Todavía no has registrado visitas. Explora una ficha de lugar para comenzar.</p>@endif
<a class="primary-button form-submit" href="{{ route('passport.show') }}">Abrir mi pasaporte</a>
@if($user->role==='admin')<a class="primary-button form-submit admin-entry" href="{{ route('admin.dashboard') }}">Abrir administración</a>@endif
<a class="text-button profile-explore" href="{{ route('interests.edit') }}">Editar mis intereses</a>
<a class="text-button profile-explore" href="{{ route('merchant.index') }}">Mis comercios y solicitudes</a>
<section class="social-connections"><p class="eyebrow">Acceso a mi cuenta</p><h3>Cuentas conectadas</h3><p>Conectar un proveedor permite ingresar sin compartir con TurismoSV tu contraseña externa.</p><div>@foreach(['google'=>'Google','facebook'=>'Facebook'] as $provider=>$label)@php($connected=$user->socialAccounts->firstWhere('provider',$provider))@if($connected)<span><b>{{ $label }}</b><small>Conectada {{ $connected->connected_at->diffForHumans() }}</small></span>@else<a href="{{ route('social.redirect',$provider) }}">Conectar {{ $label }}</a>@endif @endforeach</div></section>
<section class="public-profile-settings"><p class="eyebrow">Privacidad y comunidad</p><h3>Mi perfil público</h3><p>Comparte tus avances sin mostrar correo, visitas pendientes ni ubicaciones precisas. Está desactivado hasta que tú lo autorices.</p>
@if($user->is_profile_public)<a class="public-profile-link" href="{{ route('travelers.public',$user->public_profile_id) }}" target="_blank" rel="noopener">Ver mi perfil público →</a>@endif
<form method="post" action="{{ route('profile.public.update') }}" class="public-profile-form">@csrf @method('PUT')
<label class="toggle-row"><input type="checkbox" name="is_profile_public" value="1" @checked($user->is_profile_public)><span><strong>Activar perfil público</strong><small>Puedes desactivarlo cuando quieras; el enlace dejará de funcionar.</small></span></label>
<fieldset><legend>Nombre visible</legend><label><input type="radio" name="public_name_mode" value="alias" @checked($user->public_name_mode!=='real')> Usar alias</label><label><input type="radio" name="public_name_mode" value="real" @checked($user->public_name_mode==='real')> Mostrar mi nombre de registro</label></fieldset>
<label>Alias público<input type="text" name="public_alias" maxlength="40" value="{{ old('public_alias',$user->public_alias) }}" placeholder="Ej. AventureroSV">@error('public_alias')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="toggle-row"><input type="checkbox" name="show_public_achievements" value="1" @checked($user->show_public_achievements)><span><strong>Mostrar logros</strong><small>Publica únicamente los logros ya obtenidos.</small></span></label>
<label class="toggle-row"><input type="checkbox" name="show_public_stamps" value="1" @checked($user->show_public_stamps)><span><strong>Mostrar sellos</strong><small>Revela los lugares verificados y la fecha de cada sello.</small></span></label>
<label class="legal-check"><input type="checkbox" name="public_consent" value="1"><span>Comprendo qué datos serán públicos y autorizo mostrarlos conforme a la <a href="{{ route('legal.privacy') }}">Política de Privacidad</a>.</span></label>@error('public_consent')<small class="field-error">{{ $message }}</small>@enderror
<button class="primary-button" type="submit">Guardar privacidad</button></form></section>
<a class="text-button profile-explore" href="{{ route('home') }}#lugares">Explorar lugares</a>
<form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="text-button" type="submit">Cerrar sesión</button></form>
@endsection
