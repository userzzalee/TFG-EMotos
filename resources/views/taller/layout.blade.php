<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taller – Alyx</title>
    <style>[x-cloak]{display:none!important;}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color:#0a0a0a; color:#e5e7eb;">

@include('layouts.navigation')

<div class="pt-[75px] min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-48 shrink-0 border-r border-white/10 px-4 pt-4 pb-2 flex flex-col justify-start gap-3">

        @auth
            @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                {{-- Mecánico --}}
                <a href="{{ route('taller.nuevas-citas') }}"
                   class="sidebar-link {{ request()->routeIs('taller.nuevas-citas') ? 'active' : '' }}">
                    Nuevas citas
                </a>
                <a href="{{ route('taller.trabajo-pendiente') }}"
                   class="sidebar-link {{ request()->routeIs('taller.trabajo-pendiente') || request()->routeIs('taller.detalle-cita') ? 'active' : '' }}">
                    Trabajo pendiente
                </a>
                <a href="{{ route('taller.historial') }}"
                   class="sidebar-link {{ request()->routeIs('taller.historial') ? 'active' : '' }}">
                    Historial
                </a>
            @else
                {{-- Usuario --}}
                <a href="{{ route('taller.crear') }}"
                   class="sidebar-link {{ request()->routeIs('taller.crear') ? 'active' : '' }}">
                    Hacer cita
                </a>
                <a href="{{ route('taller.mis-citas') }}"
                   class="sidebar-link {{ request()->routeIs('taller.mis-citas') ? 'active' : '' }}">
                    Mis citas
                </a>
            @endif
        @endauth
    </aside>

    {{-- Contenido --}}
    <main class="flex-1 px-6 py-8 pt-10 max-w-5xl">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded border border-[#f0c36d]/40 bg-[#f0c36d]/10 text-[#f0c36d] text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded border border-red-500/40 bg-red-500/10 text-red-400 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<style>
    .sidebar-link {
        display: block;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
        text-decoration: none;
        transition: color .15s, background .15s, border-color .15s;
        border: 1px solid rgba(255,255,255,.08);
        background: rgba(255,255,255,.03);
    }
    .sidebar-link:hover { color: #f0c36d; background: rgba(240,195,109,.1); border-color: rgba(240,195,109,.2); }
    .sidebar-link.active { color: #f0c36d; background: rgba(240,195,109,.15); border-color: rgba(240,195,109,.3); }
</style>

</body>
</html>
