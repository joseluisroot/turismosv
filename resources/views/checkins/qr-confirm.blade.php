@extends('layouts.account')
@section('title','Confirmar visita')
@section('content')
<p class="eyebrow">QR autorizado · {{ $code->place->category->name }}</p><h2>Confirma tu visita a {{ $code->place->name }}</h2>
<p class="form-intro">El código identifica material físico asignado a este lugar. La visita se registrará para hoy y quedará vinculada a tu cuenta.</p>
<div class="qr-place-summary"><img src="{{ asset('brand/isotipo-turismosv.svg') }}" alt=""><div><strong>{{ $code->place->name }}</strong><small>{{ $code->place->municipality }}, {{ $code->place->department->name }}</small><span>Vigente hasta {{ $code->expires_at?->translatedFormat('d M Y') ?? 'su desactivación' }}</span></div></div>
@auth
    @if(auth()->user()->hasVerifiedEmail())<form method="post" action="{{ route('qr.confirm',['publicId'=>$code->public_id,'secret'=>$secret]) }}">@csrf<button class="primary-button form-submit" type="submit">Confirmar visita de hoy</button></form><p class="qr-privacy">Al confirmar aceptas las reglas de verificación. Este QR no recopila tu ubicación.</p>@else<a class="primary-button form-submit" href="{{ route('verification.notice') }}">Verificar correo</a>@endif
@else<a class="primary-button form-submit" href="{{ route('login') }}">Ingresar para confirmar</a>@endauth
@endsection
