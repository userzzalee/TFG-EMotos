@extends('taller.layout')

@section('content')

@if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())

{{-- ══════════════════════════════════════════════
     MENÚ MECÁNICO / ADMIN
══════════════════════════════════════════════ --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Panel del taller</h1>
    <p class="text-sm text-white/40">Gestiona las citas y el trabajo del taller.</p>
</div>

<div class="grid grid-cols-3 gap-5">

    <a href="{{ route('taller.nuevas-citas') }}"
       class="taller-card group">
        <div class="card-icon bg-yellow-500/10 group-hover:bg-yellow-500/20">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p class="card-title">Nuevas citas</p>
        <p class="card-desc">Solicitudes pendientes de aceptar.</p>
        @php $nuevas = \App\Models\CitaTaller::where('estado','pendiente')->count(); @endphp
        @if($nuevas > 0)
            <span class="mt-3 inline-block px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs rounded-full">
                {{ $nuevas }} pendiente{{ $nuevas > 1 ? 's' : '' }}
            </span>
        @endif
    </a>

    <a href="{{ route('taller.trabajo-pendiente') }}"
       class="taller-card group">
        <div class="card-icon bg-orange-500/10 group-hover:bg-orange-500/20">
            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="card-title">Trabajo pendiente</p>
        <p class="card-desc">Citas aceptadas en curso.</p>
        @php $enproceso = \App\Models\CitaTaller::where('mecanico_id', Auth::id())->whereIn('estado',['aceptada','en_proceso'])->count(); @endphp
        @if($enproceso > 0)
            <span class="mt-3 inline-block px-2 py-0.5 bg-orange-500/20 text-orange-400 text-xs rounded-full">
                {{ $enproceso }} en curso
            </span>
        @endif
    </a>

    <a href="{{ route('taller.historial') }}"
       class="taller-card group">
        <div class="card-icon bg-white/5 group-hover:bg-white/10">
            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <p class="card-title">Historial</p>
        <p class="card-desc">Todos los trabajos completados.</p>
    </a>

</div>

@else

{{-- ══════════════════════════════════════════════
     MENÚ USUARIO NORMAL
══════════════════════════════════════════════ --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Taller</h1>
    <p class="text-sm text-white/40">Gestiona tus citas de mantenimiento y reparación.</p>
</div>

<div class="grid grid-cols-2 gap-5 max-w-xl">

    <a href="{{ route('taller.crear') }}"
       class="taller-card group">
        <div class="card-icon bg-[#f0c36d]/10 group-hover:bg-[#f0c36d]/20">
            <svg class="w-6 h-6 text-[#f0c36d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <p class="card-title">Hacer cita</p>
        <p class="card-desc">Solicita una revisión o reparación para tu moto.</p>
    </a>

    <a href="{{ route('taller.mis-citas') }}"
       class="taller-card group">
        <div class="card-icon bg-white/5 group-hover:bg-white/10">
            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="card-title">Mis citas</p>
        <p class="card-desc">Consulta el estado de tus citas y realiza pagos.</p>
        @php $pendientes = \App\Models\CitaTaller::where('user_id', Auth::id())->whereIn('estado',['pendiente','aceptada','en_proceso'])->count(); @endphp
        @if($pendientes > 0)
            <span class="mt-3 inline-block px-2 py-0.5 bg-[#f0c36d]/20 text-[#f0c36d] text-xs rounded-full">
                {{ $pendientes }} activa{{ $pendientes > 1 ? 's' : '' }}
            </span>
        @endif
    </a>

</div>

@endif

<style>
    .taller-card {
        display: flex;
        flex-direction: column;
        background: #111;
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 14px;
        padding: 24px;
        text-decoration: none;
        transition: border-color .2s, transform .2s;
    }
    .taller-card:hover {
        border-color: rgba(240,195,109,.35);
        transform: translateY(-2px);
    }
    .card-icon {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
        transition: background .2s;
    }
    .card-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #e5e7eb;
        margin-bottom: 6px;
    }
    .card-desc { font-size: 13px; color: #6b7280; line-height: 1.5; }
</style>

@endsection
