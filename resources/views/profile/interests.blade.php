@extends('layouts.account')
@section('title','Mis intereses')
@section('content')
<p class="eyebrow">Tu viaje, a tu manera</p><h2>¿Qué te inspira?</h2><p class="form-intro">Elige hasta seis intereses. Son privados, puedes cambiarlos después y solo los usaremos para ordenar tus recomendaciones.</p>
@if(session('status'))<div class="status-message">{{ session('status') }}</div>@endif
<form method="post" action="{{ route('interests.update') }}" class="interest-form">@csrf @method('PUT')
<div class="interest-selector">@foreach($interests as $interest)<label class="interest-option"><input type="checkbox" name="interests[]" value="{{ $interest->id }}" @checked(in_array($interest->id,old('interests',$user->interests->pluck('id')->all())))><span><b>{{ $interest->icon }}</b><strong>{{ $interest->name }}</strong><small>{{ $interest->description }}</small></span></label>@endforeach</div>
@error('interests')<small class="field-error">{{ $message }}</small>@enderror @error('interests.*')<small class="field-error">{{ $message }}</small>@enderror
<button class="primary-button form-submit" type="submit">Guardar y ver recomendaciones</button><a class="text-button profile-explore" href="{{ route('home') }}">Omitir por ahora</a></form>
@endsection
