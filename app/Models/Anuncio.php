<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anuncio extends Model
{
    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'precio',
        'imagen',
        'categoria',
        'estado',
        'activo',
        'vendido',
    ];

    protected $casts = [
        'activo'  => 'boolean',
        'vendido' => 'boolean',
        'precio'  => 'decimal:2',
    ];


    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)->where('vendido', false);
    }


    public static function categorias(): array
    {
        return ['motos', 'cascos', 'ropa', 'accesorios', 'recambios', 'otro'];
    }

    public static function estados(): array
    {
        return [
            'nuevo'       => 'Nuevo',
            'bueno'       => 'Buen estado',
            'usado'       => 'Usado',
            'para-piezas' => 'Para piezas',
        ];
    }

    public function etiquetaEstado(): string
    {
        return self::estados()[$this->estado] ?? $this->estado;
    }
}
