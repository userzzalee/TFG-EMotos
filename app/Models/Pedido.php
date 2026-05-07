<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'configuracion_id',
        'numero_pedido',
        'estado',
        'precio_total',
        'fecha_confirmacion',
    ];

    protected function casts(): array
    {
        return [
            'precio_total' => 'decimal:2',
            'fecha_confirmacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function configuracion(): BelongsTo
    {
        return $this->belongsTo(Configuracion::class);
    }
}
