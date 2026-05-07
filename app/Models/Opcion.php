<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Opcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'grupo_opcion_id',
        'nombre',
        'descripcion',
        'precio_extra',
        'imagen',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'precio_extra' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }

    public function grupoOpcion(): BelongsTo
    {
        return $this->belongsTo(GrupoOpcion::class);
    }

    public function configuraciones(): BelongsToMany
    {
        return $this->belongsToMany(Configuracion::class, 'configuracion_opcion');
    }
}
