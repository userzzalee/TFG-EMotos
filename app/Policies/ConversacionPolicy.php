<?php

namespace App\Policies;

use App\Models\Conversacion;
use App\Models\User;

class ConversacionPolicy
{
    /**
     * Solo los participantes (comprador o vendedor) pueden acceder a la conversación.
     */
    public function participar(User $user, Conversacion $conversacion): bool
    {
        return $conversacion->comprador_id === $user->id || $conversacion->vendedor_id === $user->id;
    }
}
