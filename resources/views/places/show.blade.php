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
    @if(session('report_status'))<div class="status-message">{{ session('report_status') }}</div>@endif @error('report')<div class="status-error">{{ $message }}</div>@enderror
    <nav class="breadcrumbs" aria-label="Ruta"><a href="{{ route('home') }}">Inicio</a><span>›</span><a href="{{ route('home') }}#lugares">Lugares</a><span>›</span><span>{{ $place->name }}</span></nav>
    <section class="place-detail-hero">
        @if($place->photos->isNotEmpty())<div class="place-detail-visual has-photo" style="background-image:linear-gradient(180deg,rgba(16,45,56,.08),rgba(16,45,56,.72)),url('{{ route('photos.show',$place->photos->first()->public_id) }}')"><span>{{ $place->category->name }}</span><div></div><small>Foto comunitaria de {{ $place->photos->first()->user->name }} · Uso autorizado</small></div>@else<div class="place-detail-visual"><span>{{ $place->category->name }}</span><div><img src="{{ asset('brand/isotipo-turismosv.svg') }}" alt=""><p>Fotografía pendiente<br>de autorización</p></div><small>La imagen oficial será publicada con permiso del lugar o su autor.</small></div>@endif
        <div class="place-detail-copy">
            <p class="eyebrow">{{ $place->municipality }} · {{ $place->department->name }}</p>
            <h1>{{ $place->name }}</h1><p class="place-lead">{{ $place->summary }}</p>
            <div class="detail-rating"><strong>★ {{ $place->rating_average }}</strong><span>{{ $place->reviews_count }} {{ $place->reviews_count === 1 ? 'reseña' : 'reseñas' }}</span><span>{{ $place->verified_visits_count }} visitas confirmadas</span></div>
            @if($place->verification_status === 'verified')<div class="detail-trust verified">✓ Verificado por TurismoSV</div>@elseif($place->verification_status === 'community_confirmed')<div class="detail-trust">✓ Confirmado por la comunidad</div>@else<div class="detail-trust pending">○ Información en validación</div>@endif
            <div class="detail-actions">@auth<a class="primary-button" href="#check-in">Registrar mi visita</a>@else<a class="primary-button" href="{{ route('login') }}">Ingresa para registrar tu visita</a>@endauth<a class="ghost-button" href="#informacion">Ver información</a></div>
        </div>
    </section>
    <section class="place-info-grid" id="informacion">
        <article><p class="eyebrow">Información esencial</p><h2>Planifica tu visita</h2><dl><div><dt>Categoría</dt><dd>{{ $place->category->name }}</dd></div><div><dt>Municipio</dt><dd>{{ $place->municipality }}</dd></div><div><dt>Departamento</dt><dd>{{ $place->department->name }}</dd></div><div><dt>Actualización</dt><dd>{{ $place->updated_at->translatedFormat('d M Y') }}</dd></div></dl><p class="data-notice">Horarios, precios, coordenadas y accesibilidad se incorporarán después de confirmar sus fuentes.</p></article>
        <aside><p class="eyebrow light">Cómo leer esta ficha</p><h2>Confianza con contexto.</h2><p>El nivel mostrado describe el respaldo disponible para esta información; no representa una certificación gubernamental.</p><div class="trust-meter"><span style="width:{{ $place->verification_score }}%"></span></div><small>{{ $place->verification_score }} de 100 puntos de respaldo editorial y comunitario.</small><a href="{{ route('home') }}#confianza">Conoce cómo verificamos →</a></aside>
    </section>
    <section class="checkin-section" id="check-in">
        <div><p class="eyebrow">Visitas con respaldo</p><h2>Registra tu paso por {{ $place->name }}</h2><p>Una declaración inicia el proceso, pero no se convierte en visita verificada hasta que exista evidencia aprobada. Más adelante podrás usar ubicación consentida, QR del lugar o revisión manual.</p><ul><li>La ubicación nunca será pública por defecto.</li><li>Una visita pendiente no entrega sellos ni puntos.</li><li>Los intentos duplicados o falsos pueden ser rechazados.</li></ul></div>
        <aside class="checkin-card">
            @if(session('checkin_status'))<div class="status-message">{{ session('checkin_status') }}</div>@endif
            @auth
                @if(auth()->user()->hasVerifiedEmail())
                    @if($userCheckIn)<div class="checkin-current"><span class="status-{{ $userCheckIn->status }}">{{ match($userCheckIn->status){'verified'=>'✓ Verificada','rejected'=>'× Rechazada',default=>'○ Pendiente'} }}</span><strong>Última visita: {{ $userCheckIn->visited_on->translatedFormat('d M Y') }}</strong><small>Método actual: declaración personal.</small></div>@endif
                    <form method="post" action="{{ route('checkins.store',$place) }}" class="checkin-form">@csrf
                        <label>Fecha de visita<input type="date" name="visited_on" min="{{ now()->subDays(30)->toDateString() }}" max="{{ now()->toDateString() }}" value="{{ old('visited_on',now()->toDateString()) }}" required>@error('visited_on')<small>{{ $message }}</small>@enderror</label>
                        <label>Nota privada para revisión <span>(opcional)</span><textarea name="note" rows="3" maxlength="500" placeholder="Por ejemplo: actividad realizada o referencia útil para confirmar la visita.">{{ old('note') }}</textarea>@error('note')<small>{{ $message }}</small>@enderror</label>
                        <label class="legal-check"><input type="checkbox" name="verification_consent" value="1" required><span>Declaro que realicé esta visita y acepto las <a href="{{ route('legal.terms') }}#verificacion-visitas" target="_blank" rel="noopener">reglas de verificación de visitas</a>.</span></label>@error('verification_consent')<small>{{ $message }}</small>@enderror
                        <button class="primary-button" type="submit">Solicitar verificación</button>
                    </form>
                @else<a class="primary-button" href="{{ route('verification.notice') }}">Verifica tu correo primero</a>@endif
            @else<p>Necesitas una cuenta verificada para registrar una visita.</p><a class="primary-button" href="{{ route('login') }}">Ingresar</a>@endauth
        </aside>
    </section>
    <section class="community-photos" id="fotos"><div><p class="eyebrow">Miradas de la comunidad</p><h2>Fotografías de {{ $place->name }}</h2><p>Solo publicamos imágenes aprobadas después de revisar autoría, pertinencia y seguridad.</p>
    @if($place->photos->isNotEmpty())<div class="photo-gallery">@foreach($place->photos as $photo)<figure><img src="{{ route('photos.show',$photo->public_id) }}" alt="{{ $photo->alt_text ?: 'Fotografía comunitaria de '.$place->name }}" loading="lazy"><figcaption><span>Por {{ $photo->user->name }} · Uso autorizado</span>@auth @if(auth()->id()!==$photo->user_id)<form method="post" action="{{ route('reports.photos.store',$photo) }}" class="inline-report">@csrf<input type="hidden" name="reason" value="inappropriate"><button type="submit">Reportar</button></form>@endif @endauth</figcaption></figure>@endforeach</div>@else<div class="photo-empty">Todavía no hay fotografías comunitarias aprobadas.</div>@endif</div>
    <aside class="photo-contribution-card">@if(session('photo_status'))<div class="status-message">{{ session('photo_status') }}</div>@endif
    @auth @if(auth()->user()->hasVerifiedEmail())<h3>Comparte una fotografía</h3><p>Formatos JPG, PNG o WebP; máximo 5 MB. La imagen no será pública mientras esté pendiente.</p><form method="post" action="{{ route('photos.store',$place) }}" enctype="multipart/form-data" class="photo-form">@csrf<label>Seleccionar imagen<input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>@error('photo')<small>{{ $message }}</small>@enderror</label><label>Descripción accesible <span>(opcional)</span><input type="text" name="alt_text" maxlength="160" value="{{ old('alt_text') }}" placeholder="Describe brevemente lo que aparece">@error('alt_text')<small>{{ $message }}</small>@enderror</label><label class="legal-check"><input type="checkbox" name="photo_rights" value="1" required><span>Confirmo que tomé esta fotografía o que tengo autorización suficiente para compartirla.</span></label>@error('photo_rights')<small>{{ $message }}</small>@enderror<label class="legal-check"><input type="checkbox" name="photo_license" value="1" required><span>Concedo a TurismoSV una licencia no exclusiva para almacenarla, moderarla y exhibirla conforme a los <a href="{{ route('legal.terms') }}#fotografias" target="_blank" rel="noopener">términos de fotografías</a>.</span></label>@error('photo_license')<small>{{ $message }}</small>@enderror<button class="primary-button" type="submit">Enviar a moderación</button></form>
    @if($userPhotos->isNotEmpty())<div class="photo-submissions"><strong>Mis envíos recientes</strong>@foreach($userPhotos as $photo)<span>{{ $photo->created_at->translatedFormat('d M Y') }} <b class="status-{{ $photo->status }}">{{ match($photo->status){'approved'=>'Aprobada','rejected'=>'Rechazada',default=>'Pendiente'} }}</b></span>@endforeach</div>@endif
    @else<a class="primary-button" href="{{ route('verification.notice') }}">Verificar correo para participar</a>@endif @else<h3>Comparte tu mirada</h3><p>Necesitas una cuenta verificada para enviar fotografías.</p><a class="primary-button" href="{{ route('login') }}">Ingresar</a>@endauth</aside></section>
    <section class="reviews-section" id="resenas">
        <div class="reviews-heading"><div><p class="eyebrow">Experiencias de la comunidad</p><h2>Reseñas y calificaciones</h2></div><div class="reviews-score"><strong>{{ $place->rating_average ? '★ '.$place->rating_average : 'Sin nota' }}</strong><span>{{ $place->reviews_count }} {{ $place->reviews_count === 1 ? 'reseña publicada' : 'reseñas publicadas' }}</span></div></div>
        @if(session('review_status'))<div class="status-message">{{ session('review_status') }}</div>@endif
        <div class="reviews-layout">
            <div class="reviews-list">
                @forelse($place->reviews as $review)
                    <article class="review-card"><header><span>{{ strtoupper(mb_substr($review->user->name,0,1)) }}</span><div><strong>{{ $review->user->name }}</strong><small>{{ $review->created_at->translatedFormat('d M Y') }} @if($review->visited_at)· Visitó en {{ $review->visited_at->translatedFormat('M Y') }}@endif</small></div><b>★ {{ $review->rating }}</b></header><h3>{{ $review->title }}</h3><p>{{ $review->body }}</p><footer><span>@if($review->is_visit_verified)✓ Visita verificada @else Reseña de usuario verificado @endif</span>@auth @if(auth()->id()!==$review->user_id)<form method="post" action="{{ route('reports.reviews.store',$review) }}" class="inline-report">@csrf<input type="hidden" name="reason" value="false_information"><button type="submit">Reportar</button></form>@endif @endauth</footer></article>
                @empty
                    <div class="empty-reviews"><strong>Sé la primera persona en compartir una experiencia.</strong><p>Las reseñas reales de la comunidad comenzarán a construir la calificación de este lugar.</p></div>
                @endforelse
            </div>
            <aside class="review-form-card">
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                        <p class="eyebrow">{{ $userReview ? 'Actualiza tu aporte' : 'Comparte tu experiencia' }}</p><h3>{{ $userReview ? 'Tu reseña de '.$place->name : '¿Cómo fue tu visita?' }}</h3>
                        <form method="post" action="{{ route('reviews.store',$place) }}" class="review-form" data-review-form>@csrf
                            <label>Calificación<select name="rating" required><option value="">Selecciona</option>@for($rating=5;$rating>=1;$rating--)<option value="{{ $rating }}" @selected(old('rating',$userReview?->rating)==$rating)>{{ str_repeat('★',$rating) }} · {{ $rating }}</option>@endfor</select>@error('rating')<small>{{ $message }}</small>@enderror</label>
                            <label>Título<input name="title" maxlength="120" value="{{ old('title',$userReview?->title) }}" placeholder="Resume tu experiencia" required>@error('title')<small>{{ $message }}</small>@enderror</label>
                            <label>Cuéntanos más<textarea name="body" rows="5" maxlength="2000" placeholder="Servicio, ambiente, recomendaciones y detalles útiles…" required>{{ old('body',$userReview?->body) }}</textarea>@error('body')<small>{{ $message }}</small>@enderror</label>
                            <label>Fecha de visita <span>(opcional)</span><input type="date" name="visited_at" max="{{ now()->toDateString() }}" value="{{ old('visited_at',$userReview?->visited_at?->toDateString()) }}">@error('visited_at')<small>{{ $message }}</small>@enderror</label>
                            <label class="legal-check"><input type="checkbox" name="community_rules" value="1" data-review-confirmation required @checked(old('community_rules'))><span>Confirmo que esta reseña refleja mi experiencia honesta y cumple las <a href="{{ route('legal.terms') }}#contenido-comunitario" target="_blank" rel="noopener">normas de la comunidad y condiciones aplicables</a>.</span></label>@error('community_rules')<small>{{ $message }}</small>@enderror
                            <button class="primary-button" type="submit" data-review-submit @disabled(!old('community_rules'))>{{ $userReview ? 'Actualizar reseña' : 'Publicar reseña' }}</button>
                        </form>
                    @else<p class="eyebrow">Protegemos la confianza</p><h3>Verifica tu correo para publicar.</h3><a class="primary-button" href="{{ route('verification.notice') }}">Verificar correo</a>@endif
                @else<p class="eyebrow">Participa</p><h3>Ingresa para escribir una reseña.</h3><p>Las cuentas verificadas ayudan a reducir contenido falso.</p><a class="primary-button" href="{{ route('login') }}">Ingresar</a>@endauth
            </aside>
        </div>
    </section>
    @if($relatedPlaces->isNotEmpty())<section class="related-section"><p class="eyebrow">Continúa descubriendo</p><h2>Otros lugares que podrían inspirarte</h2><div>@foreach($relatedPlaces as $related)<a href="{{ route('places.show',$related) }}"><small>{{ $related->category->name }} · {{ $related->department->name }}</small><strong>{{ $related->name }}</strong><span>★ {{ $related->rating_average }} →</span></a>@endforeach</div></section>@endif
</main>
</body>
</html>
