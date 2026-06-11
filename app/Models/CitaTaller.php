<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitaTaller extends Model
{
    use HasFactory;

    protected $table = 'citas_taller';

    protected $fillable = [
        'user_id',
        'marca',
        'modelo',
        'matricula',
        'fecha_cita',
        'problema',
        'comentarios',
        'fotos',
        'estado',
        'comentario_mecanico',
        'coste',
        'mecanico_id',
    ];

    protected function casts(): array
    {
        return [
            'fotos'      => 'array',
            'coste'      => 'decimal:2',
            'fecha_cita' => 'datetime',
        ];
    }


    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function esPendiente(): bool    { return $this->estado === 'pendiente'; }
    public function esAceptada(): bool     { return $this->estado === 'aceptada'; }
    public function esEnProceso(): bool    { return $this->estado === 'en_proceso'; }
    public function esFinalizada(): bool   { return $this->estado === 'finalizada'; }
    public function esPagada(): bool       { return $this->estado === 'pagada'; }

    /**
     * Fecha de la cita en formato legible, p. ej. "Lun 12 jun · 09:00".
     * Devuelve null si la cita no tiene fecha asignada (citas antiguas).
     */
    public function fechaCitaLegible(): ?string
    {
        if (! $this->fecha_cita) {
            return null;
        }

        return ucfirst($this->fecha_cita->locale('es')->isoFormat('ddd D MMM')) . ' · ' . $this->fecha_cita->format('H:i');
    }

    public function etiquetaEstado(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'Pendiente',
            'aceptada'   => 'Aceptada',
            'en_proceso' => 'En proceso',
            'finalizada' => 'Finalizada',
            'pagada'     => 'Pagada',
            default      => ucfirst($this->estado),
        };
    }

    public function colorEstado(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'text-yellow-400 border-yellow-400',
            'aceptada'   => 'text-blue-400 border-blue-400',
            'en_proceso' => 'text-orange-400 border-orange-400',
            'finalizada' => 'text-green-400 border-green-400',
            'pagada'     => 'text-gray-400 border-gray-400',
            default      => 'text-white border-white',
        };
    }
}
