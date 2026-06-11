<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';

    protected $fillable = [
        'autor_id',
        'vendedor_id',
        'anuncio_id',
        'puntuacion',
        'comentario',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    /**
     * Usuario que escribe la valoración (el comprador).
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Usuario valorado (el vendedor).
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    /**
     * Anuncio sobre el que se realizó la venta.
     */
    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Anuncio::class, 'anuncio_id');
    }
}
