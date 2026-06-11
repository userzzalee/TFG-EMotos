<?php

namespace App\Policies;

use App\Models\CitaTaller;
use App\Models\User;

/**
 * Autorización de las citas de taller. Centraliza quién puede pagar, gestionar
 * (zona del mecánico) y atender (el mecánico asignado a una cita concreta).
 *
 * Laravel la descubre por convención: App\Models\CitaTaller → CitaTallerPolicy.
 */
class CitaTallerPolicy
{
    /**
     * Pagar una cita: solo su dueño (el cliente).
     */
    public function pagar(User $user, CitaTaller $cita): bool
    {
        return $user->id === $cita->user_id;
    }

    /**
     * Acceder a la zona de mecánico (listados, detalle, aceptar):
     * cualquier mecánico o admin.
     *
     * El segundo parámetro puede ser la clase (cuando no hay instancia concreta)
     * o un modelo; aquí solo importa el rol.
     */
    public function gestionar(User $user, CitaTaller|string $cita = CitaTaller::class): bool
    {
        return $user->esMecanico() || $user->esAdmin();
    }

    /**
     * Atender una cita concreta (comentar / finalizar): debe ser el mecánico
     * (o admin) asignado a esa cita.
     */
    public function atender(User $user, CitaTaller $cita): bool
    {
        return ($user->esMecanico() || $user->esAdmin())
            && $cita->mecanico_id === $user->id;
    }
}
