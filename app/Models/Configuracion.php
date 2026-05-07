<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'moto_id',
        'nombre',
        'precio_total',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_total' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moto(): BelongsTo
    {
        return $this->belongsTo(Moto::class);
    }

    public function opciones(): BelongsToMany
    {
        return $this->belongsToMany(Opcion::class, 'configuracion_opcion');
    }

    public function pedido(): HasOne
    {
        return $this->hasOne(Pedido::class);
    }
}
