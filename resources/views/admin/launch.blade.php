<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Lanzamiento — TurismoSV</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
<x-analytics />
</head>
<body class="admin-dashboard-page">
<header class="place-header"><a href="{{ route('home') }}"><img src="{{ asset('brand/logo-turismosv.svg') }}" alt="TurismoSV"></a><nav><a href="{{ route('admin.dashboard') }}">Panel</a><a href="{{ route('admin.prelaunch.index') }}">Prelanzamiento</a><a href="{{ route('home') }}">Ver sitio</a></nav></header>
<main class="launch-shell">
    <section class="launch-hero {{ $ready ? 'ready' : '' }}">
        <div><p class="eyebrow light">Control de salida</p><h1>{{ $ready ? 'Listo para abrir.' : 'Primero, una salida responsable.' }}</h1><p>La señal verde exige evidencia legal, editorial, técnica y operativa. Google y Facebook pueden habilitarse después sin impedir el registro por correo.</p></div>
        <strong>{{ $requiredCompleted }}/{{ $requiredTotal }}<small>controles obligatorios</small></strong>
    </section>
    @if(session('launch_status'))<div class="status-message">{{ session('launch_status') }}</div>@endif
    <section class="launch-automated"><div><p class="eyebrow">Comprobaciones automáticas</p><h2>{{ $automated->where('passed', false)->count() }} bloqueos detectados</h2><p>Complementa esta pantalla ejecutando <code>php artisan turismosv:production-check</code> directamente en HostGator.</p></div><ul>@foreach($automated as $check)<li class="{{ $check['passed'] ? 'passed' : '' }}"><b>{{ $check['passed'] ? '✓' : '!' }}</b><span>{{ $check['label'] }}</span></li>@endforeach</ul></section>
    <section class="launch-checklist"><div class="section-heading"><div><p class="eyebrow">Evidencia y responsabilidad</p><h2>Lista de lanzamiento</h2></div><p class="section-note">Marca un control solamente después de comprobarlo. Cada cambio conserva responsable y fecha.</p></div>
        @foreach($items->groupBy('area') as $area => $areaItems)
            <div class="launch-area"><h3>{{ $area }}</h3>@foreach($areaItems as $item)<form method="post" action="{{ route('admin.launch.update',$item) }}" class="launch-item {{ $item->is_completed ? 'completed' : '' }}">@csrf @method('PUT')<label><input type="checkbox" name="is_completed" value="1" {{ $item->is_completed ? 'checked' : '' }}><span><b>{{ $item->label }}</b><small>{{ $item->is_required ? 'Obligatorio' : 'Puede completarse después' }}</small></span></label><textarea name="evidence_notes" maxlength="2000" placeholder="Evidencia, resultado de la prueba o referencia…">{{ $item->evidence_notes }}</textarea><footer><small>@if($item->completed_at)Aprobado por {{ $item->completedBy?->name ?? 'administrador' }} · {{ $item->completed_at->translatedFormat('d M Y H:i') }}@else Pendiente de comprobación @endif</small><button class="primary-button small">Guardar</button></footer></form>@endforeach</div>
        @endforeach
    </section>
</main>
</body>
</html>
