<?php

namespace App\Policies;

use App\Models\Anuncio;
use App\Models\User;

/**
 * Autorización de anuncios de segunda mano. Laravel descubre esta policy por
 * convención (App\Models\Anuncio → App\Policies\AnuncioPolicy).
 */
class AnuncioPolicy
{
    /**
     * Solo el dueño del anuncio puede editarlo.
     */
    public function update(User $user, Anuncio $anuncio): bool
    {
        return $user->id === $anuncio->user_id;
    }

    /**
     * Solo el dueño (o un admin) puede borrarlo.
     */
    public function delete(User $user, Anuncio $anuncio): bool
    {
        return $user->id === $anuncio->user_id || $user->esAdmin();
    }
}
