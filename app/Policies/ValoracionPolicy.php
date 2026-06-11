<?php

namespace App\Policies;

use App\Models\Anuncio;
use App\Models\User;

/**
 * Reglas de negocio para valorar a un vendedor tras una venta.
 */
class ValoracionPolicy
{
    /**
     * Un usuario puede valorar al vendedor de un anuncio si:
     *  - No es el propio vendedor.
     *  - El anuncio está marcado como vendido.
     *  - No lo ha valorado ya antes.
     */
    public function crear(User $user, Anuncio $anuncio): bool
    {
        if ($user->id === $anuncio->user_id) {
            return false;
        }

        if (! $anuncio->vendido) {
            return false;
        }

        return ! $anuncio->valoraciones()
            ->where('autor_id', $user->id)
            ->exists();
    }
}
