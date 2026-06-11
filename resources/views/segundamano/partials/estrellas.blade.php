{{--
    Muestra una puntuación en estrellas (solo lectura).
    Parámetros:
      $nota  → float|int (0-5)
      $size  → clases tailwind de tamaño (opcional, por defecto text-sm)
--}}
@php
    $nota = (float) ($nota ?? 0);
    $size = $size ?? 'text-sm';
    $llenas = (int) floor($nota);
    $media  = ($nota - $llenas) >= 0.5;
@endphp
<span class="inline-flex items-center {{ $size }} leading-none tracking-tight" aria-label="{{ number_format($nota, 1) }} de 5">
    @for($i = 1; $i <= 5; $i++)
        @if($i <= $llenas)
            <span class="text-yellow-500">★</span>
        @elseif($i === $llenas + 1 && $media)
            <span class="text-yellow-500">⯨</span>
        @else
            <span class="text-gray-700">★</span>
        @endif
    @endfor
</span>
