<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Descubre lugares verificados, experiencias auténticas y rutas para recorrer El Salvador.">
    <title>TurismoSV — Descubre El Salvador con confianza</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="TurismoSV, inicio">
            <img src="{{ asset('brand/logo-turismosv.svg') }}" alt="TurismoSV — El Salvador lo descubres tú">
        </a>
        <nav class="desktop-nav" aria-label="Navegación principal">
            <a href="#descubrir">Descubrir</a>
            <a href="#pasaporte">Pasaporte</a>
            <a href="#confianza">Cómo verificamos</a>
        </nav>
        <div class="header-actions">
            <button class="ghost-button" type="button">Ingresar</button>
            <button class="primary-button small" type="button">Crear cuenta</button>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">El Salvador se vive, se descubre y se comparte</p>
                <h1>Tu próximo lugar favorito está más cerca de lo que imaginas.</h1>
                <p class="hero-intro">Explora destinos con información comprobable, reseñas auténticas y una comunidad que recorre el país contigo.</p>
                <form class="search-box" action="#descubrir" method="get">
                    <label class="sr-only" for="search">Buscar lugares</label>
                    <span aria-hidden="true">⌕</span>
                    <input id="search" name="q" type="search" placeholder="Busca una playa, pueblo o experiencia">
                    <button type="submit">Explorar</button>
                </form>
                <div class="trust-line">
                    <span class="avatar-stack" aria-hidden="true"><i>JL</i><i>MR</i><i>AC</i></span>
                    <p><strong>Información viva</strong><br>confirmada por viajeros y lugares participantes</p>
                </div>
            </div>

            <div class="passport-card" id="pasaporte">
                <div class="passport-topline"><span>PASAPORTE TURÍSTICO</span><span>SV · 001</span></div>
                <div class="passport-content">
                    <div class="country-seal"><span>14</span><small>departamentos</small></div>
                    <p>COLECCIONA EXPERIENCIAS</p>
                    <h2>El Salvador<br>es tuyo por descubrir.</h2>
                    <div class="stamp-row" aria-label="Ejemplos de sellos turísticos">
                        <span>PLAYA<small>01</small></span><span>MONTAÑA<small>02</small></span><span>PUEBLOS<small>03</small></span>
                    </div>
                </div>
                <div class="passport-footer"><span>VISITA</span><span>CONFIRMA</span><span>COLECCIONA</span></div>
            </div>
        </section>

        <section class="stats" aria-label="Estado del catálogo de demostración">
            <div><strong>{{ $stats['places'] }}</strong><span>lugares iniciales</span></div>
            <div><strong>{{ $stats['verified'] }}</strong><span>verificados</span></div>
            <div><strong>{{ $stats['departments'] }}</strong><span>departamentos</span></div>
            <p>Contenido de demostración para validar la experiencia inicial.</p>
        </section>

        <section class="section" id="descubrir">
            <div class="section-heading">
                <div><p class="eyebrow">Empieza por lo que te inspira</p><h2>¿Qué quieres descubrir?</h2></div>
                <a href="#lugares">Ver todos los lugares <span>→</span></a>
            </div>
            <div class="category-grid">
                @foreach ($categories as $category)
                    <article class="category-card">
                        <span class="category-icon" aria-hidden="true">{{ $category->icon }}</span>
                        <div><h3>{{ $category->name }}</h3><p>{{ $category->description }}</p><small>{{ $category->places_count }} lugares iniciales</small></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section places-section" id="lugares">
            <div class="section-heading">
                <div><p class="eyebrow">Selección inicial</p><h2>Lugares que cuentan nuestra historia</h2></div>
                <p class="section-note">Las imágenes se incorporarán únicamente con autorización.</p>
            </div>
            <div class="places-grid">
                @foreach ($featuredPlaces as $index => $place)
                    <article class="place-card tone-{{ ($index % 4) + 1 }}">
                        <div class="place-visual">
                            <span class="place-category">{{ $place->category->name }}</span>
                            <span class="photo-pending">Fotografía<br>pendiente de autorización</span>
                            <span class="place-number">0{{ $loop->iteration }}</span>
                        </div>
                        <div class="place-content">
                            <div class="place-meta"><span>{{ $place->municipality }}, {{ $place->department->name }}</span><span class="rating">★ {{ $place->rating_average }}</span></div>
                            <h3>{{ $place->name }}</h3>
                            <p>{{ $place->summary }}</p>
                            <div class="place-proof">
                                @if ($place->verification_status === 'verified')
                                    <span class="verified">✓ Verificado por TurismoSV</span>
                                @elseif ($place->verification_status === 'community_confirmed')
                                    <span>✓ Confirmado por la comunidad</span>
                                @else
                                    <span>○ Información en validación</span>
                                @endif
                                <small>{{ $place->verified_visits_count }} visitas · {{ $place->reviews_count }} reseñas</small>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="verification-section" id="confianza">
            <div><p class="eyebrow light">Confianza visible</p><h2>No basta con decir que un lugar es bueno. Mostramos por qué puedes confiar.</h2></div>
            <ol class="verification-steps">
                <li><span>01</span><div><strong>Datos con respaldo</strong><p>Indicamos quién confirmó la información y cuándo fue actualizada.</p></div></li>
                <li><span>02</span><div><strong>Visitas verificadas</strong><p>Las experiencias comprobadas tienen más peso dentro de la comunidad.</p></div></li>
                <li><span>03</span><div><strong>Independencia</strong><p>Un patrocinio nunca compra estrellas ni modifica el ranking orgánico.</p></div></li>
            </ol>
        </section>

        <section class="founder-cta">
            <div><p class="eyebrow">Comercios y destinos</p><h2>Forma parte del catálogo fundador.</h2><p>Verifica tu lugar, comparte información autorizada y ayúdanos a construir una referencia turística para todo El Salvador.</p></div>
            <button class="primary-button" type="button">Quiero participar <span>→</span></button>
        </section>
    </main>

    <footer>
        <a class="brand footer-brand" href="{{ route('home') }}" aria-label="TurismoSV, inicio"><img src="{{ asset('brand/isotipo-turismosv.svg') }}" alt=""><span>Turismo<span>SV</span></span></a>
        <p>Un catálogo vivo para descubrir El Salvador con confianza.</p>
        <nav aria-label="Información legal"><a href="#">Privacidad</a><a href="#">Términos</a><a href="#">Cookies</a><a href="#">Normas de la comunidad</a></nav>
    </footer>
</body>
</html>
