<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin – Usuarios · Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[90px] pb-20 px-6 lg:px-12">

    {{-- ── Cabecera ─────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto mb-10">
        <p class="text-xs tracking-[0.3em] text-yellow-500 mb-1 uppercase">Administración</p>
        <h1 class="text-4xl font-light tracking-[0.2em] uppercase">Gestión de Usuarios</h1>
    </div>

    {{-- ── Alertas ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-green-900/40 border border-green-600 text-green-400 px-6 py-4 rounded-lg text-sm tracking-wide">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto mb-6">
            <div class="bg-red-900/40 border border-red-600 text-red-400 px-6 py-4 rounded-lg text-sm tracking-wide">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto space-y-8">

        {{-- ── Tarjetas resumen ─────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Total usuarios',  'value' => $totales['total'],    'color' => 'border-gray-600',   'text' => 'text-white'],
                    ['label' => 'Administradores', 'value' => $totales['admin'],    'color' => 'border-yellow-500', 'text' => 'text-yellow-400'],
                    ['label' => 'Mecánicos',       'value' => $totales['mecanico'], 'color' => 'border-blue-500',   'text' => 'text-blue-400'],
                    ['label' => 'Clientes',        'value' => $totales['user'],     'color' => 'border-gray-600',   'text' => 'text-gray-300'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="bg-gray-900/60 border {{ $card['color'] }} rounded-xl p-6 flex flex-col gap-1">
                    <span class="text-xs tracking-widest text-gray-500 uppercase">{{ $card['label'] }}</span>
                    <span class="text-4xl font-light {{ $card['text'] }}">{{ $card['value'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- ── Filtros ──────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('admin.usuarios') }}"
              class="flex flex-col sm:flex-row gap-4">
            <input
                type="text"
                name="busqueda"
                value="{{ $busqueda }}"
                placeholder="Buscar por nombre o email…"
                class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-5 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all placeholder-gray-600"
            >
            <select name="rol"
                    class="bg-gray-900 border border-gray-700 rounded-lg px-5 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all">
                <option value="">Todos los roles</option>
                <option value="user"     {{ $rol === 'user'     ? 'selected' : '' }}>Cliente</option>
                <option value="mecanico" {{ $rol === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                <option value="admin"    {{ $rol === 'admin'    ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit"
                    class="bg-yellow-500 text-black px-8 py-3 rounded-lg text-sm tracking-widest font-medium hover:bg-yellow-400 transition-all">
                FILTRAR
            </button>
            @if($busqueda || $rol)
                <a href="{{ route('admin.usuarios') }}"
                   class="bg-gray-800 text-gray-300 px-6 py-3 rounded-lg text-sm tracking-widest hover:bg-gray-700 transition-all text-center">
                   LIMPIAR
                </a>
            @endif
        </form>

        {{-- ── Tabla ────────────────────────────────────────────── --}}
        <div class="bg-gray-900/50 border border-gray-800 rounded-xl overflow-hidden">

            {{-- Cabecera tabla --}}
            <div class="hidden lg:grid grid-cols-[60px_1fr_1fr_120px_160px_180px] gap-4 px-6 py-4 border-b border-gray-800 text-xs tracking-widest text-gray-500 uppercase">
                <span>ID</span>
                <span>Nombre</span>
                <span>Email</span>
                <span>Rol</span>
                <span>Registro</span>
                <span class="text-right">Acciones</span>
            </div>

            {{-- Filas --}}
            @forelse($usuarios as $usuario)
                <div class="grid grid-cols-1 lg:grid-cols-[60px_1fr_1fr_120px_160px_180px] gap-4 px-6 py-5
                            border-b border-gray-800/60 hover:bg-white/[0.03] transition-colors items-center">

                    {{-- ID --}}
                    <span class="text-gray-600 text-sm">#{{ $usuario->id }}</span>

                    {{-- Nombre + avatar --}}
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center
                                    text-yellow-500 text-sm font-medium flex-shrink-0">
                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                        </div>
                        <span class="text-white text-sm font-medium">{{ $usuario->name }}</span>
                    </div>

                    {{-- Email --}}
                    <span class="text-gray-400 text-sm truncate">{{ $usuario->email }}</span>

                    {{-- Badge de rol --}}
                    @php
                        $badgeClass = match($usuario->rol) {
                            'admin'    => 'bg-yellow-500/15 text-yellow-400 border-yellow-600/40',
                            'mecanico' => 'bg-blue-500/15 text-blue-400 border-blue-600/40',
                            default    => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                        };
                        $badgeLabel = match($usuario->rol) {
                            'admin'    => 'Admin',
                            'mecanico' => 'Mecánico',
                            default    => 'Cliente',
                        };
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full text-xs tracking-widest border {{ $badgeClass }} w-fit">
                        {{ $badgeLabel }}
                    </span>

                    {{-- Fecha --}}
                    <span class="text-gray-500 text-xs">
                        {{ $usuario->created_at->format('d/m/Y') }}<br>
                        <span class="text-gray-600">{{ $usuario->created_at->format('H:i') }}</span>
                    </span>

                    {{-- Acciones --}}
                    @if($usuario->id !== Auth::id())
                        <div class="flex items-center justify-end gap-2">

                            {{-- Cambiar rol --}}
                            <form method="POST"
                                  action="{{ route('admin.usuarios.rol', $usuario) }}"
                                  class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="rol"
                                        class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-xs
                                               focus:border-yellow-500 focus:outline-none transition-all">
                                    <option value="user"     {{ $usuario->rol === 'user'     ? 'selected' : '' }}>Cliente</option>
                                    <option value="mecanico" {{ $usuario->rol === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                                    <option value="admin"    {{ $usuario->rol === 'admin'    ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit"
                                        title="Guardar rol"
                                        class="bg-yellow-500/10 border border-yellow-600/40 text-yellow-400 px-3 py-2 rounded-lg
                                               text-xs hover:bg-yellow-500/25 transition-all">
                                    ✓
                                </button>
                            </form>

                            {{-- Eliminar --}}
                            <form method="POST"
                                  action="{{ route('admin.usuarios.eliminar', $usuario) }}"
                                  onsubmit="return confirm('¿Eliminar a {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Eliminar usuario"
                                        class="bg-red-500/10 border border-red-700/40 text-red-400 px-3 py-2 rounded-lg
                                               text-xs hover:bg-red-500/25 transition-all">
                                    ✕
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-right text-xs text-gray-700 italic pr-1">Tú</span>
                    @endif

                </div>
            @empty
                <div class="px-6 py-16 text-center text-gray-600 tracking-widest text-sm">
                    No se encontraron usuarios.
                </div>
            @endforelse
        </div>

        {{-- ── Paginación ───────────────────────────────────────── --}}
        @if($usuarios->hasPages())
            <div class="flex justify-center">
                {{ $usuarios->links() }}
            </div>
        @endif

    </div>
</main>

</body>
</html>
