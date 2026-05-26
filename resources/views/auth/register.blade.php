<x-guest-layout>
    <div class="min-h-screen bg-black flex flex-col items-center justify-center pt-[60px]">

        <div class="bg-[rgb(30,30,30)]/90 text-center p-4 w-[35%] text-white mt-0 rounded-lg border border-gray-700/50">

            <h1 class="text-lg font-bold mb-2 uppercase tracking-widest">Crear cuenta</h1>
            <hr class="border-gray-700 mb-4">

            <form method="POST" action="{{ route('register') }}" class="flex flex-col items-center gap-2">
                @csrf

                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required maxlength="255"
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="text" name="name" placeholder="Nombre" value="{{ old('name') }}" required maxlength="20"
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="text" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required maxlength="20"
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="text" name="username" placeholder="Usuario" value="{{ old('username') }}" required maxlength="20"
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="password" name="password" placeholder="Contraseña" required
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="password" name="password_confirmation" placeholder="Confirmar contraseña"  required
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">
                <input type="number" name="telefono" placeholder="Teléfono (opcional)" value="{{ old('telefono') }}"
                    class="bg-black text-white w-[90%] h-[36px] px-4 border border-gray-700 rounded-lg outline-none placeholder-gray-500 text-xs">

                <button type="submit"
                        class="mt-2 w-[120px] h-[32px] text-white bg-[#c9b37e] hover:bg-[#e0c97a] text-[#1e1f29] font-medium rounded-[20px] cursor-pointer border-none transition-colors text-xs">
                    Regístrate
                </button>
            </form>

            @if ($errors->any())
                <p class="mt-4 text-red-400 text-xs">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            @endif
        </div>

        <div class="mt-3 text-white flex items-center gap-4">
            <span class="text-sm">¿Ya tienes cuenta?</span>
            <a href="{{ route('login') }}" class="text-white bg-[rgb(34,34,34)] px-3 py-1.5 no-underline hover:bg-black transition-colors rounded text-sm">
                Iniciar sesión
            </a>
        </div>

        <div class="mt-3">
            <a href="{{ url('/') }}" class="text-gray-400 no-underline hover:text-white transition-colors text-xs">
                Volver al inicio
            </a>
        </div>

    </div>
</x-guest-layout>
