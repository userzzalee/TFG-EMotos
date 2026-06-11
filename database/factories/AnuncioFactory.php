<?php

namespace Database\Factories;

use App\Models\Anuncio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anuncio>
 */
class AnuncioFactory extends Factory
{
    protected $model = Anuncio::class;

    public function definition(): array
    {
        $titulos = [
            'Casco integral AGV', 'Chaqueta de cuero Dainese', 'Guantes de verano',
            'Escape Akrapovic', 'Intercomunicador Cardo', 'Maleta lateral Givi',
            'Botas Alpinestars', 'Cubre depósito carbono', 'Pantalla ahumada',
            'Puños calefactables', 'Soporte de móvil', 'Kit de transmisión',
        ];

        return [
            'user_id'     => User::factory(),
            'titulo'      => fake()->randomElement($titulos) . ' ' . fake()->randomElement(['como nuevo', 'poco uso', 'oferta', 'urge vender']),
            'descripcion' => fake()->paragraph(),
            'precio'      => fake()->randomFloat(2, 15, 1200),
            'imagen'      => null,
            'categoria'   => fake()->randomElement(Anuncio::categorias()),
            'estado'      => fake()->randomElement(array_keys(Anuncio::estados())),
            'activo'      => true,
            'vendido'     => false,
        ];
    }

    /**
     * Anuncio marcado como vendido (necesario para poder valorar).
     */
    public function vendido(): static
    {
        return $this->state(fn (array $attributes) => ['vendido' => true]);
    }
}
