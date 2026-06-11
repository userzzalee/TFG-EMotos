<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anuncio extends Model
{
    use HasFactory;
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

    /**
     * Galería de imágenes del anuncio (feature 11).
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(AnuncioImagen::class)->orderBy('orden');
    }

    /**
     * Valoraciones recibidas a través de la venta de este anuncio (feature 9).
     */
    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class);
    }


    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)->where('vendido', false);
    }

    /**
     * Filtro por rango de precio (feature 10). Ignora valores nulos.
     */
    public function scopePrecioMin(Builder $query, $min): Builder
    {
        return $query->when($min !== null && $min !== '', fn ($q) => $q->where('precio', '>=', $min));
    }

    public function scopePrecioMax(Builder $query, $max): Builder
    {
        return $query->when($max !== null && $max !== '', fn ($q) => $q->where('precio', '<=', $max));
    }

    /**
     * Ordenación segura por precio o fecha (feature 10).
     * $orden admite: 'precio_asc', 'precio_desc', 'recientes' (por defecto).
     */
    public function scopeOrdenar(Builder $query, ?string $orden): Builder
    {
        return match ($orden) {
            'precio_asc'  => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            'antiguos'    => $query->oldest(),
            default       => $query->latest(),
        };
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

    /**
     * Opciones de ordenación disponibles para el desplegable (feature 10).
     */
    public static function ordenaciones(): array
    {
        return [
            'recientes'   => 'Más recientes',
            'antiguos'    => 'Más antiguos',
            'precio_asc'  => 'Precio: menor a mayor',
            'precio_desc' => 'Precio: mayor a menor',
        ];
    }

    public function etiquetaEstado(): string
    {
        return self::estados()[$this->estado] ?? $this->estado;
    }

    /**
     * Imagen de portada: la galería tiene prioridad; si no, el campo legacy.
     */
    public function imagenPortada(): ?string
    {
        if ($this->relationLoaded('imagenes') ? $this->imagenes->isNotEmpty() : $this->imagenes()->exists()) {
            return $this->imagenes->first()?->ruta ?? $this->imagenes()->first()?->ruta;
        }

        return $this->imagen;
    }
}
