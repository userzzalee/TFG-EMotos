@extends('layouts.general')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-base font-bold tracking-widest uppercase text-white mb-1">Notificaciones</h1>
        <p class="text-xs text-white/40">Avisos de tus citas de taller y mensajes.</p>
    </div>
    @if(Auth::user()->unreadNotifications()->count() > 0)
        <form action="{{ route('notificaciones.leer-todas') }}" method="POST">
            @csrf
            <button type="submit" class="text-[10px] uppercase tracking-widest text-white/40 hover:text-[#f0c36d] transition-colors">
                Marcar todas como leídas
            </button>
        </form>
    @endif
</div>

@if($notificaciones->isEmpty())
    <div class="px-4 py-6 rounded-[10px] border border-white/10 bg-[#111] text-center text-xs text-white/40 uppercase tracking-widest">
        No tienes notificaciones.
    </div>
@else
    <div class="flex flex-col gap-2">
        @foreach($notificaciones as $n)
            <a href="{{ route('notificaciones.abrir', $n->id) }}"
               class="flex items-start gap-3 bg-[#111] border rounded-[10px] px-4 py-3 no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5
                      {{ $n->read_at ? 'border-white/10 opacity-60' : 'border-[#f0c36d]/30' }}">
                <span class="text-lg leading-none mt-0.5">{{ $n->data['icono'] ?? '🔔' }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-200">{{ $n->data['titulo'] ?? 'Notificación' }}</p>
                    <p class="text-xs text-white/50 mt-0.5">{{ $n->data['mensaje'] ?? '' }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-white/25 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->read_at)
                    <span class="w-2 h-2 rounded-full bg-[#f0c36d] shrink-0 mt-1"></span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $notificaciones->links() }}
    </div>
@endif

@endsection
