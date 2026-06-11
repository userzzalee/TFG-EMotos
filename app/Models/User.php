<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

#[Fillable(['name', 'email', 'password', 'telefono', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Billable, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function configuraciones(): HasMany
    {
        return $this->hasMany(Configuracion::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Anuncios publicados por el usuario.
     */
    public function anuncios(): HasMany
    {
        return $this->hasMany(Anuncio::class);
    }

    /**
     * Valoraciones recibidas como vendedor (feature 9).
     */
    public function valoracionesRecibidas(): HasMany
    {
        return $this->hasMany(Valoracion::class, 'vendedor_id');
    }

    /**
     * Valoraciones escritas como comprador (feature 9).
     */
    public function valoracionesEmitidas(): HasMany
    {
        return $this->hasMany(Valoracion::class, 'autor_id');
    }

    /**
     * Nota media (1-5) como vendedor. Null si no tiene valoraciones.
     */
    public function notaMedia(): ?float
    {
        $media = $this->valoracionesRecibidas()->avg('puntuacion');

        return $media !== null ? round((float) $media, 1) : null;
    }

    /**
     * Número total de valoraciones recibidas.
     */
    public function totalValoraciones(): int
    {
        return $this->valoracionesRecibidas()->count();
    }

    public function esMecanico(): bool
    {
        return $this->rol === 'mecanico';
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }
}
