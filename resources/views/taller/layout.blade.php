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

<div class="pt-[60px] min-h-screen flex flex-col md:flex-row">

    {{-- Toggle sidebar móvil --}}
    <div x-data="{ sideOpen: false }" class="md:hidden">
        <button @click="sideOpen = !sideOpen"
                class="flex items-center gap-2 w-full px-4 py-3 border-b border-white/10 text-[11px] uppercase tracking-widest text-white/50 hover:text-[#f0c36d] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Navegación del taller
        </button>

        <div x-show="sideOpen" x-cloak
             class="border-b border-white/10 px-4 py-3 flex flex-col gap-2 bg-[#0a0a0a]">
            @auth
                @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                    <a href="{{ route('taller.nuevas-citas') }}" @click="sideOpen=false"
                       class="sidebar-link {{ request()->routeIs('taller.nuevas-citas') ? 'active' : '' }}">Nuevas citas</a>
                    <a href="{{ route('taller.trabajo-pendiente') }}" @click="sideOpen=false"
                       class="sidebar-link {{ request()->routeIs('taller.trabajo-pendiente') || request()->routeIs('taller.detalle-cita') ? 'active' : '' }}">Trabajo pendiente</a>
                    <a href="{{ route('taller.historial') }}" @click="sideOpen=false"
                       class="sidebar-link {{ request()->routeIs('taller.historial') ? 'active' : '' }}">Historial</a>
                @else
                    <a href="{{ route('taller.crear') }}" @click="sideOpen=false"
                       class="sidebar-link {{ request()->routeIs('taller.crear') ? 'active' : '' }}">Hacer cita</a>
                    <a href="{{ route('taller.mis-citas') }}" @click="sideOpen=false"
                       class="sidebar-link {{ request()->routeIs('taller.mis-citas') ? 'active' : '' }}">Mis citas</a>
                @endif
            @endauth
        </div>
    </div>

    {{-- Sidebar escritorio --}}
    <aside class="hidden md:flex w-48 shrink-0 border-r border-white/10 px-4 pt-4 pb-2 flex-col justify-start gap-3">
        @auth
            @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                <a href="{{ route('taller.nuevas-citas') }}"
                   class="sidebar-link {{ request()->routeIs('taller.nuevas-citas') ? 'active' : '' }}">Nuevas citas</a>
                <a href="{{ route('taller.trabajo-pendiente') }}"
                   class="sidebar-link {{ request()->routeIs('taller.trabajo-pendiente') || request()->routeIs('taller.detalle-cita') ? 'active' : '' }}">Trabajo pendiente</a>
                <a href="{{ route('taller.historial') }}"
                   class="sidebar-link {{ request()->routeIs('taller.historial') ? 'active' : '' }}">Historial</a>
            @else
                <a href="{{ route('taller.crear') }}"
                   class="sidebar-link {{ request()->routeIs('taller.crear') ? 'active' : '' }}">Hacer cita</a>
                <a href="{{ route('taller.mis-citas') }}"
                   class="sidebar-link {{ request()->routeIs('taller.mis-citas') ? 'active' : '' }}">Mis citas</a>
            @endif
        @endauth
    </aside>

    {{-- Contenido --}}
    <main class="flex-1 px-4 sm:px-6 py-6 sm:py-8 max-w-5xl w-full">

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
