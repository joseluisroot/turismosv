@php
    $analyticsPublicPage = request()->routeIs('home', 'explore', 'rankings.index', 'postcards.index', 'places.show', 'founder-program.show');
    $analyticsId = config('analytics.enabled') && $analyticsPublicPage && !auth()->check()
        && preg_match('/^G-[A-Z0-9]+$/', config('analytics.measurement_id', ''))
        ? config('analytics.measurement_id') : '';
@endphp
<script defer src="{{ asset('js/analytics-consent.js') }}" data-analytics-id="{{ $analyticsId }}" data-cookie-policy="{{ route('legal.cookies') }}"></script>
