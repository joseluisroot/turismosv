<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $place->summary }}">
    <title>{{ $place->name }} — TurismoSV</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="place-header"><a href="{{ route('home') }}"><img src="{{ asset('brand/logo-turismosv.svg') }}" alt="TurismoSV"></a><nav><a href="{{ route('home') }}#lugares">Explorar lugares</a>@auth<a href="{{ route('profile') }}">Mi perfil</a>@else<a href="{{ route('login') }}">Ingresar</a>@endauth</nav></header>
<main class="place-page">
    <nav class="breadcrumbs" aria-label="Ruta"><a href="{{ route('home') }}">Inicio</a><span>›</span><a href="{{ route('home') }}#lugares">Lugares</a><span>›</span><span>{{ $place->name }}</span></nav>
    <section class="place-detail-hero">
        <div class="place-detail-visual"><span>{{ $place->category->name }}</span><div><img src="{{ asset('brand/isotipo-turismosv.svg') }}" alt=""><p>Fotografía pendiente<br>de autorización</p></div><small>La imagen oficial será publicada con permiso del lugar o su autor.</small></div>
        <div class="place-detail-copy">
            <p class="eyebrow">{{ $place->municipality }} · {{ $place->department->name }}</p>
            <h1>{{ $place->name }}</h1><p class="place-lead">{{ $place->summary }}</p>
            <div class="detail-rating"><strong>★ {{ $place->rating_average }}</strong><span>{{ $place->reviews_count }} reseñas</span><span>{{ $place->verified_visits_count }} visitas confirmadas</span></div>
            @if($place->verification_status === 'verified')<div class="detail-trust verified">✓ Verificado por TurismoSV</div>@elseif($place->verification_status === 'community_confirmed')<div class="detail-trust">✓ Confirmado por la comunidad</div>@else<div class="detail-trust pending">○ Información en validación</div>@endif
            <div class="detail-actions">@auth<button class="primary-button" type="button" disabled>Registrar visita · Próximamente</button>@else<a class="primary-button" href="{{ route('login') }}">Ingresa para registrar tu visita</a>@endauth<a class="ghost-button" href="#informacion">Ver información</a></div>
        </div>
    </section>
    <section class="place-info-grid" id="informacion">
        <article><p class="eyebrow">Información esencial</p><h2>Planifica tu visita</h2><dl><div><dt>Categoría</dt><dd>{{ $place->category->name }}</dd></div><div><dt>Municipio</dt><dd>{{ $place->municipality }}</dd></div><div><dt>Departamento</dt><dd>{{ $place->department->name }}</dd></div><div><dt>Actualización</dt><dd>{{ $place->updated_at->translatedFormat('d M Y') }}</dd></div></dl><p class="data-notice">Horarios, precios, coordenadas y accesibilidad se incorporarán después de confirmar sus fuentes.</p></article>
        <aside><p class="eyebrow light">Cómo leer esta ficha</p><h2>Confianza con contexto.</h2><p>El nivel mostrado describe el respaldo disponible para esta información; no representa una certificación gubernamental.</p><div class="trust-meter"><span style="width:{{ $place->verification_score }}%"></span></div><small>{{ $place->verification_score }} de 100 puntos de respaldo editorial y comunitario.</small><a href="{{ route('home') }}#confianza">Conoce cómo verificamos →</a></aside>
    </section>
    @if($relatedPlaces->isNotEmpty())<section class="related-section"><p class="eyebrow">Continúa descubriendo</p><h2>Otros lugares que podrían inspirarte</h2><div>@foreach($relatedPlaces as $related)<a href="{{ route('places.show',$related) }}"><small>{{ $related->category->name }} · {{ $related->department->name }}</small><strong>{{ $related->name }}</strong><span>★ {{ $related->rating_average }} →</span></a>@endforeach</div></section>@endif
</main>
</body>
</html>
