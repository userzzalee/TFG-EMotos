<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnuncioImagen extends Model
{
    protected $table = 'anuncio_imagenes';

    protected $fillable = [
        'anuncio_id',
        'ruta',
        'orden',
    ];

    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Anuncio::class);
    }

    /**
     * URL pública de la imagen.
     */
    public function url(): string
    {
        return asset('storage/' . $this->ruta);
    }
}
