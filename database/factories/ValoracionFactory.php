<?php

namespace Database\Factories;

use App\Models\Anuncio;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Valoracion>
 */
class ValoracionFactory extends Factory
{
    protected $model = Valoracion::class;

    public function definition(): array
    {
        return [
            'autor_id'    => User::factory(),
            'vendedor_id' => User::factory(),
            'anuncio_id'  => Anuncio::factory(),
            'puntuacion'  => fake()->numberBetween(3, 5),
            'comentario'  => fake()->optional(0.8)->randomElement([
                'Trato excelente y envío rápido. Todo perfecto.',
                'El producto estaba como en las fotos. Muy recomendable.',
                'Buena comunicación, repetiría sin duda.',
                'Algo lento respondiendo pero todo correcto al final.',
                'Vendedor serio y honesto. Genial.',
            ]),
        ];
    }
}
