<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'precio_base',
        'imagen_principal',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }

    public function gruposOpciones(): HasMany
    {
        return $this->hasMany(GrupoOpcion::class);
    }

    public function configuraciones(): HasMany
    {
        return $this->hasMany(Configuracion::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
