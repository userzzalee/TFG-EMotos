@extends('taller.layout')

@section('content')

@if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())

<!-- Menu mecanico/admin -->
<div class="mb-4">
    <h1 class="text-base font-bold tracking-widest uppercase text-white mb-1">Panel del taller</h1>
    <p class="text-xs text-white/40">Gestiona las citas y el trabajo del taller.</p>
</div>

<div class="grid grid-cols-3 gap-3">

    <a href="{{ route('taller.nuevas-citas') }}"
       class="flex flex-col bg-[#111] border border-white/10 rounded-[10px] p-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group cursor-pointer">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2 transition-colors duration-200 bg-yellow-500/10 group-hover:bg-yellow-500/20">
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 mb-1">Nuevas citas</p>
        <p class="text-xs text-gray-500 leading-relaxed">Solicitudes pendientes de aceptar.</p>
        @if($nuevas > 0)
            <span class="mt-2 inline-block px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs rounded-full">
                {{ $nuevas }} pendiente{{ $nuevas > 1 ? 's' : '' }}
            </span>
        @endif
    </a>

    <a href="{{ route('taller.trabajo-pendiente') }}"
       class="flex flex-col bg-[#111] border border-white/10 rounded-[10px] p-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group cursor-pointer">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2 transition-colors duration-200 bg-orange-500/10 group-hover:bg-orange-500/20">
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 mb-1">Trabajo pendiente</p>
        <p class="text-xs text-gray-500 leading-relaxed">Citas aceptadas en curso.</p>
        @if($enproceso > 0)
            <span class="mt-2 inline-block px-2 py-0.5 bg-orange-500/20 text-orange-400 text-xs rounded-full">
                {{ $enproceso }} en curso
            </span>
        @endif
    </a>

    <a href="{{ route('taller.historial') }}"
       class="flex flex-col bg-[#111] border border-white/10 rounded-[10px] p-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group cursor-pointer">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2 transition-colors duration-200 bg-white/5 group-hover:bg-white/10">
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 mb-1">Historial</p>
        <p class="text-xs text-gray-500 leading-relaxed">Todos los trabajos completados.</p>
    </a>

</div>

@else

<!-- Menu usuario normal -->
<div class="mb-4">
    <h1 class="text-base font-bold tracking-widest uppercase text-white mb-1">Taller</h1>
    <p class="text-xs text-white/40">Gestiona tus citas de mantenimiento y reparación.</p>
</div>

<div class="grid grid-cols-2 gap-3 max-w-md">

    <a href="{{ route('taller.crear') }}"
       class="flex flex-col bg-[#111] border border-white/10 rounded-[10px] p-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group cursor-pointer">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2 transition-colors duration-200 bg-[#f0c36d]/10 group-hover:bg-[#f0c36d]/20">
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 mb-1">Hacer cita</p>
        <p class="text-xs text-gray-500 leading-relaxed">Solicita una revisión o reparación para tu moto.</p>
    </a>

    <a href="{{ route('taller.mis-citas') }}"
       class="flex flex-col bg-[#111] border border-white/10 rounded-[10px] p-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group cursor-pointer">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2 transition-colors duration-200 bg-white/5 group-hover:bg-white/10">
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 mb-1">Mis citas</p>
        <p class="text-xs text-gray-500 leading-relaxed">Consulta el estado de tus citas y realiza pagos.</p>
        @if($pendientes > 0)
            <span class="mt-2 inline-block px-2 py-0.5 bg-[#f0c36d]/20 text-[#f0c36d] text-xs rounded-full">
                {{ $pendientes }} activa{{ $pendientes > 1 ? 's' : '' }}
            </span>
        @endif
    </a>

</div>

@endif


@endsection
