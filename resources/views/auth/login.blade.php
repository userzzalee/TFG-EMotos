<x-guest-layout>
    <div class="min-h-screen bg-black flex flex-col items-center justify-start pt-[70px]">

        <div class="bg-[rgb(17,17,17)]/70 text-center p-6 w-[40%] text-white mt-[50px] rounded-lg">

            <h1 class="text-xl font-bold mb-3 uppercase tracking-widest">Crear una nueva cuenta</h1>
            <hr class="border-gray-700 mb-6">

            <form method="POST" action="{{ route('register') }}" class="flex flex-col items-center gap-3">
                @csrf

                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required maxlength="255"
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="text" name="name" placeholder="Nombre" value="{{ old('name') }}"     required maxlength="20"
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="text" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required maxlength="20"
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="text" name="username" placeholder="Nombre de usuario" value="{{ old('username') }}" required maxlength="20"
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="password" name="password" placeholder="Contraseña" required
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="password" name="password_confirmation" placeholder="Confirmar contraseña"  required
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">
                <input type="number" name="telefono" placeholder="Teléfono (opcional)" value="{{ old('telefono') }}"
                    class="bg-black text-white w-[90%] h-[50px] px-5 border border-gray-700 rounded-lg outline-none placeholder-gray-500">

                <button type="submit"
                        class="mt-3 w-[150px] h-[40px] text-white bg-[#c9b37e] hover:bg-[#e0c97a] text-[#1e1f29] font-medium rounded-[30px] cursor-pointer border-none transition-colors">
                    Regístrate
                </button>
            </form>

            @if ($errors->any())
                <p class="mt-4 text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            @endif
        </div>

        <div class="mt-8 text-white flex items-center gap-4">
            <span>¿Ya estás registrado?</span>
            <a href="{{ route('login') }}"
               class="text-white bg-[rgb(34,34,34)] px-4 py-2 no-underline hover:bg-black transition-colors rounded">
                Iniciar sesión
            </a>
        </div>

    </div>
</x-guest-layout>
