@props([
    'title' => 'TurismoSV — Descubre El Salvador',
    'description' => 'Explora destinos, descubre experiencias y colecciona recuerdos con tu pasaporte turístico de El Salvador.',
    'url' => null,
    'image' => null,
    'imageAlt' => 'TurismoSV. El Salvador lo descubres tú.',
    'imageType' => null,
    'type' => 'website',
])
@php
    $socialImage = $image ?: asset('brand/compartir-turismosv.png');
@endphp
<meta property="og:site_name" content="TurismoSV">
<meta property="og:locale" content="es_SV">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $url ?: url()->current() }}">
<meta property="og:image" content="{{ $socialImage }}">
<meta property="og:image:alt" content="{{ $imageAlt }}">
@if(!$image)
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@elseif($imageType)
<meta property="og:image:type" content="{{ $imageType }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $socialImage }}">
<meta name="twitter:image:alt" content="{{ $imageAlt }}">
