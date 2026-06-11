<?php

use App\Models\Conversacion;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí se autoriza quién puede escuchar cada canal PRIVADO. Laravel llama
| a estas funciones desde la ruta /broadcasting/auth cuando el navegador
| intenta suscribirse. Si la función devuelve false, la suscripción se
| rechaza (un usuario no puede espiar conversaciones ajenas).
|
*/

// Canal personal de cada usuario (badge de no leídos en el navbar).
Broadcast::channel('usuario.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal por defecto de Laravel Notifications (campana del navbar en vivo).
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal de una conversación: solo comprador y vendedor de esa conversación.
Broadcast::channel('conversacion.{conversacionId}', function ($user, $conversacionId) {
    $conversacion = Conversacion::find($conversacionId);

    if (! $conversacion) {
        return false;
    }

    return $user->id === $conversacion->comprador_id
        || $user->id === $conversacion->vendedor_id;
});
