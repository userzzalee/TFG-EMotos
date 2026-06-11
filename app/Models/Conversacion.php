<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversacion extends Model
{
    protected $table = 'conversaciones';

    protected $fillable = [
        'comprador_id',
        'vendedor_id',
        'producto_id',
        'anuncio_id',
        'ultimo_mensaje_at',
    ];

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime',
    ];


    public function comprador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Anuncio::class, 'anuncio_id');
    }

    /**
     * Título del artículo del que trata la conversación, sea un anuncio de
     * segunda mano o un producto del merchandising.
     */
    public function tituloArticulo(): ?string
    {
        if ($this->anuncio_id) {
            return $this->anuncio?->titulo;
        }

        if ($this->producto_id) {
            return $this->producto?->nombre;
        }

        return null;
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id')->orderBy('created_at');
    }

    public function ultimoMensaje(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id')->latest()->limit(1);
    }



    /**
     * Devuelve el otro participante de la conversación (no el usuario actual).
     */
    public function otroParticipante(int $userId): User
    {
        return $this->comprador_id === $userId 
        ? $this->vendedor: $this->comprador;  // ture = vendedor , false = comprador
    }

    /**
     * Mensajes no leídos para un usuario concreto.
     */
    public function mensajesNoLeidos(int $userId): int
    {
        return $this->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->count();
    }
}
