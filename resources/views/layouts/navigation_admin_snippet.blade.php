{{--
    AÑADE este bloque dentro del div "Controles derecha" de navigation.blade.php,
    justo antes del cierre @auth … @endauth, tras el enlace al Carrito.
    Solo aparece si el usuario tiene rol admin.
--}}

@if(Auth::user()->esAdmin())
    <a href="{{ route('admin.usuarios') }}"
       class="text-[#f0c36d] no-underline text-[12px] uppercase tracking-widest hover:text-yellow-300 transition-colors">
        Admin
    </a>
@endif
