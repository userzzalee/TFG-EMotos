<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color:#0a0a0a; color:#e5e7eb;">
@include('layouts.navigation')
<div class="pt-[75px] min-h-screen">
    <main class="px-6 py-8 max-w-5xl mx-auto">
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
</body>
</html>
