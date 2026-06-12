@extends('taller.layout')

@section('content')

<div class="flex items-center gap-3 mb-5 sm:mb-6">
    <a href="{{ route('taller.trabajo-pendiente') }}" class="text-white/30 hover:text-white transition-colors text-sm">← Volver</a>
    <span class="text-white/20">/</span>
    <span class="text-sm text-white/50 truncate">{{ $cita->marca }} {{ $cita->modelo }}</span>
</div>

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 sm:mb-8 gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-widest uppercase text-white">
            {{ $cita->marca }} {{ $cita->modelo }}
        </h1>
        <p class="text-white/40 text-sm mt-1">Matrícula: {{ $cita->matricula }}</p>
    </div>
    <span class="self-start text-sm border rounded px-3 py-1.5 {{ $cita->colorEstado() }}">{{ $cita->etiquetaEstado() }}</span>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 sm:mb-8">

    <div class="info-card">
        <p class="info-label">Cliente</p>
        <p class="info-value">{{ $cita->usuario->name }}</p>
        <p class="text-xs text-white/30 break-all">{{ $cita->usuario->email }}</p>
        @if($cita->usuario->telefono)
            <p class="text-xs text-white/30">{{ $cita->usuario->telefono }}</p>
        @endif
    </div>

    <div class="info-card">
        <p class="info-label">Vehículo</p>
        <p class="info-value">{{ $cita->marca }} {{ $cita->modelo }}</p>
        <p class="text-xs text-white/40">{{ $cita->matricula }}</p>
    </div>

    <div class="info-card">
        <p class="info-label">Fecha de la cita</p>
        @if($cita->fecha_cita)
            <p class="info-value text-[#f0c36d]">{{ ucfirst($cita->fecha_cita->locale('es')->isoFormat('ddd D MMM')) }}</p>
            <p class="text-xs text-white/40">{{ $cita->fecha_cita->format('H:i') }} h</p>
        @else
            <p class="info-value text-white/40">Sin fecha</p>
        @endif
    </div>

    <div class="info-card">
        <p class="info-label">Solicitud</p>
        <p class="info-value">{{ $cita->created_at->format('d/m/Y') }}</p>
        <p class="text-xs text-white/40">{{ $cita->created_at->format('H:i') }} h</p>
    </div>

    <div class="info-card sm:col-span-2 lg:col-span-2">
        <p class="info-label">Descripción del problema</p>
        <p class="info-value text-white/80 leading-relaxed">{{ $cita->problema }}</p>
    </div>

    @if($cita->comentarios)
        <div class="info-card sm:col-span-2 lg:col-span-3">
            <p class="info-label">Comentarios del cliente</p>
            <p class="info-value text-white/60">{{ $cita->comentarios }}</p>
        </div>
    @endif

    @if($cita->fotos && count($cita->fotos))
        <div class="sm:col-span-2 lg:col-span-3">
            <p class="info-label mb-3">Fotos adjuntas</p>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                @foreach($cita->fotos as $foto)
                    <a href="{{ Storage::url($foto) }}" target="_blank">
                        <img src="{{ Storage::url($foto) }}" alt="foto"
                             class="w-20 h-20 sm:w-28 sm:h-28 object-cover rounded-lg border border-white/10 hover:border-[#f0c36d]/60 transition-colors">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Notas del taller --}}
<div class="border-t border-white/10 pt-6 sm:pt-8 mb-6 sm:mb-8">
    <h2 class="text-sm uppercase tracking-widest text-white/50 mb-4 sm:mb-5">Notas del taller</h2>
    <form action="{{ route('taller.comentar', $cita) }}" method="POST">
        @csrf
        <textarea name="comentario_mecanico" rows="4"
                  placeholder="Añade notas o comentarios sobre el trabajo…"
                  class="field-input resize-none mb-4">{{ old('comentario_mecanico', $cita->comentario_mecanico) }}</textarea>
        @error('comentario_mecanico') <p class="text-red-400 text-xs mb-3">{{ $message }}</p> @enderror
        <button type="submit"
                class="px-6 py-2.5 border border-[#f0c36d]/50 text-[#f0c36d] hover:bg-[#f0c36d]/10 text-xs font-bold uppercase tracking-widest rounded transition-colors">
            Guardar comentario
        </button>
    </form>
</div>

{{-- Finalizar --}}
@if(!$cita->esFinalizada())
    <div class="border-t border-white/10 pt-6 sm:pt-8">
        <h2 class="text-sm uppercase tracking-widest text-white/50 mb-4 sm:mb-5">Finalizar trabajo</h2>
        <form action="{{ route('taller.finalizar', $cita) }}" method="POST"
              class="flex flex-col sm:flex-row sm:items-end gap-4">
            @csrf
            <div>
                <label class="info-label mb-2 block">Coste final (€) *</label>
                <div class="relative">
                    <input type="number" name="coste" min="0" step="0.01"
                           value="{{ old('coste', $cita->coste) }}"
                           placeholder="0.00"
                           class="field-input w-full sm:w-40 pr-8 @error('coste') border-red-500 @enderror">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-white/30 text-sm">€</span>
                </div>
                @error('coste') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                    class="w-full sm:w-auto px-8 py-2.5 bg-green-500 hover:bg-green-400 text-black text-xs font-bold uppercase tracking-widest rounded transition-colors"
                    onclick="return confirm('¿Marcar esta cita como finalizada?')">
                Finalizar cita
            </button>
        </form>
    </div>
@else
    <div class="border-t border-white/10 pt-6">
        <p class="text-green-400 text-sm font-semibold">✓ Cita finalizada — coste: {{ number_format($cita->coste, 2) }} €</p>
        @if($cita->esPagada())
            <p class="text-white/40 text-xs mt-1">✓ Pagada por el cliente</p>
        @else
            <p class="text-white/40 text-xs mt-1">En espera de pago por parte del cliente.</p>
        @endif
    </div>
@endif

<style>
    .info-card  { background:#111; border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:14px 16px; }
    .info-label { font-size:10px; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:4px; }
    .info-value { font-size:14px; color:#e5e7eb; }
    .field-input { width:100%; background:#111; border:1px solid rgba(255,255,255,.15); border-radius:6px;
                   padding:10px 14px; color:#e5e7eb; font-size:14px; outline:none; transition:border .15s; }
    .field-input:focus { border-color:#f0c36d; }
</style>
@endsection
