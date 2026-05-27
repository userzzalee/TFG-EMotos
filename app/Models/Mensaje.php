<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'conversacion_id',
        'remitente_id',
        'contenido',
        'leido_at',
    ];

    protected $casts = [
        'leido_at' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function esPropio(int $userId): bool
    {
        return $this->remitente_id === $userId;
    }

    public function marcarLeido(): void
    {
        if (is_null($this->leido_at)) {
            $this->update(['leido_at' => now()]);
        }
    }
}
