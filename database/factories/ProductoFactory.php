<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $nombres = [
            'Camiseta AlyX Racing', 'Sudadera Box Logo', 'Gorra bordada',
            'Llavero metálico', 'Taza térmica', 'Pegatinas pack x10',
            'Mochila técnica', 'Riñonera reflectante', 'Bandana tubular',
        ];

        return [
            'nombre'      => fake()->unique()->randomElement($nombres),
            'descripcion' => fake()->sentence(12),
            'precio'      => fake()->randomFloat(2, 5, 90),
            // Campo obligatorio en la tabla; usamos un placeholder.
            'imagen'      => 'productos/placeholder.png',
            'stock'       => fake()->numberBetween(0, 50),
            'categoria'   => fake()->randomElement(['ropa', 'accesorios', 'regalos']),
            'activo'      => true,
        ];
    }
}
