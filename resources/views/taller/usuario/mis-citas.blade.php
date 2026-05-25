@extends('taller.layout')

@section('content')

<h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Mis citas</h1>
<p class="text-sm text-white/40 mb-8">Estado de todas tus citas en el taller.</p>

{{-- ── Citas en curso ──────────────────────────────────────────────────────── --}}
<section class="mb-10">
    <h2 class="section-title">Citas pendientes / en curso</h2>

    @forelse($pendientes as $cita)
        <div class="cita-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-white font-semibold">{{ $cita->marca }} {{ $cita->modelo }}
                        <span class="text-white/40 font-normal text-sm ml-2">{{ $cita->matricula }}</span>
                    </p>
                    <p class="text-sm text-white/50 mt-1">{{ Str::limit($cita->problema, 120) }}</p>
                </div>
                <span class="shrink-0 text-xs border rounded px-2 py-1 {{ $cita->colorEstado() }}">
                    {{ $cita->etiquetaEstado() }}
                </span>
            </div>
            <p class="text-xs text-white/30 mt-3">Solicitada {{ $cita->created_at->diffForHumans() }}</p>
        </div>
    @empty
        <p class="text-sm text-white/30 italic">No tienes citas activas.</p>
    @endforelse
</section>

{{-- ── Finalizadas (pendientes de pago) ───────────────────────────────────── --}}
<section class="mb-10">
    <h2 class="section-title">Finalizadas — pendientes de pago</h2>

    @forelse($finalizadas as $cita)
        <div class="cita-card border-green-500/25">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-white font-semibold">{{ $cita->marca }} {{ $cita->modelo }}
                        <span class="text-white/40 font-normal text-sm ml-2">{{ $cita->matricula }}</span>
                    </p>
                    @if($cita->comentario_mecanico)
                        <p class="text-sm text-white/50 mt-1">
                            <span class="text-white/30">Mecánico:</span> {{ $cita->comentario_mecanico }}
                        </p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[#f0c36d] font-bold text-lg">{{ number_format($cita->coste, 2) }} €</p>
                    <span class="text-xs border rounded px-2 py-1 {{ $cita->colorEstado() }}">{{ $cita->etiquetaEstado() }}</span>
                </div>
            </div>

            <form action="{{ route('taller.pagar', $cita) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit"
                        class="px-6 py-2 bg-green-500 hover:bg-green-400 text-black text-xs font-bold
                               uppercase tracking-widest rounded transition-colors"
                        onclick="return confirm('¿Confirmar pago de {{ number_format($cita->coste, 2) }} €?')">
                    Pagar ahora
                </button>
            </form>
        </div>
    @empty
        <p class="text-sm text-white/30 italic">No hay citas pendientes de pago.</p>
    @endforelse
</section>

{{-- ── Historial (pagadas) ─────────────────────────────────────────────────── --}}
<section>
    <h2 class="section-title">Historial</h2>

    @forelse($pagadas as $cita)
        <div class="cita-card opacity-60">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-white font-semibold">{{ $cita->marca }} {{ $cita->modelo }}
                        <span class="text-white/40 font-normal text-sm ml-2">{{ $cita->matricula }}</span>
                    </p>
                    <p class="text-xs text-white/30 mt-1">Completada {{ $cita->updated_at->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-white/60 font-bold">{{ number_format($cita->coste, 2) }} €</p>
                    <span class="text-xs border rounded px-2 py-1 {{ $cita->colorEstado() }}">{{ $cita->etiquetaEstado() }}</span>
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-white/30 italic">Sin historial aún.</p>
    @endforelse
</section>

<style>
    .section-title { font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#6b7280; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid rgba(255,255,255,.07); }
    .cita-card { background:#111; border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:18px 20px; margin-bottom:12px; }
</style>
@endsection
