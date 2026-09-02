<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') — TurismoSV</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<x-social-meta />
<x-analytics />
</head>
<body class="account-page">
    <header class="account-header"><a href="{{ route('home') }}"><img src="{{ asset('brand/logo-turismosv.svg') }}" alt="TurismoSV — El Salvador lo descubres tú"></a></header>
    <main class="account-shell">
        <section class="account-story">
            <p class="eyebrow light">Tu pasaporte comienza aquí</p>
            <h1>Descubre, confirma y comparte El Salvador.</h1>
            <p>Tu perfil reunirá lugares visitados, reseñas y sellos de experiencias verificadas.</p>
            <img src="{{ asset('brand/isotipo-turismosv.svg') }}" alt="" aria-hidden="true">
        </section>
        <section class="account-card">@yield('content')</section>
    </main>
</body>
</html>
