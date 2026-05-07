<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoOpcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'moto_id',
        'nombre',
        'obligatorio',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'obligatorio' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function moto(): BelongsTo
    {
        return $this->belongsTo(Moto::class);
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(Opcion::class);
    }
}
