<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de la agenda del taller
    |--------------------------------------------------------------------------
    |
    | Define cuándo se pueden pedir citas y cuántas caben en cada hueco. Al estar
    | aquí, ajustar el horario del taller no requiere tocar código.
    |
    */

    // Días laborables (formato Carbon dayOfWeek: 0=domingo … 6=sábado).
    // Por defecto, de lunes a viernes.
    'dias_laborables' => [1, 2, 3, 4, 5],

    // Franjas horarias de atención: [hora_inicio, hora_fin] en formato 24h.
    'franjas' => [
        ['09:00', '14:00'],
        ['16:00', '19:00'],
    ],

    // Duración de cada hueco (slot), en minutos.
    'duracion_slot' => 60,

    // Cuántas citas pueden coincidir en el mismo hueco (motos en paralelo).
    'capacidad_slot' => 2,

    // Cuántos días hacia adelante se ofrecen para reservar.
    'dias_vista' => 14,

    // Antelación mínima para reservar (horas). Evita citas "para dentro de 5 min".
    'antelacion_minima_horas' => 2,

];
