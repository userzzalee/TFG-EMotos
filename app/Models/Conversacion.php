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
        'ultimo_mensaje_at',
    ];

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

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

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id')->orderBy('created_at');
    }

    public function ultimoMensaje(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id')->latest()->limit(1);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Devuelve el otro participante de la conversación (no el usuario actual).
     */
    public function otroParticipante(int $userId): User
    {
        return $this->comprador_id === $userId
            ? $this->vendedor
            : $this->comprador;
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
