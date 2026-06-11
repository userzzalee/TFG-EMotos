<?php

namespace Database\Factories;

use App\Models\CitaTaller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CitaTaller>
 */
class CitaTallerFactory extends Factory
{
    protected $model = CitaTaller::class;

    public function definition(): array
    {
        $marcas = [
            'Honda'    => ['CBR 600', 'CB 500', 'Africa Twin'],
            'Yamaha'   => ['MT-07', 'R6', 'Tracer 900'],
            'Kawasaki' => ['Z900', 'Ninja 400', 'Versys 650'],
            'BMW'      => ['R 1250 GS', 'S 1000 RR', 'F 850 GS'],
        ];

        $marca  = fake()->randomElement(array_keys($marcas));
        $modelo = fake()->randomElement($marcas[$marca]);

        return [
            'user_id'             => User::factory(),
            'marca'               => $marca,
            'modelo'              => $modelo,
            'matricula'           => strtoupper(fake()->bothify('####???')),
            'problema'            => fake()->randomElement([
                'Ruido extraño en el motor al acelerar.',
                'La moto no arranca en frío.',
                'Revisión general y cambio de aceite.',
                'Frenos delanteros esponjosos.',
                'Cambio de neumáticos y alineación.',
            ]),
            'comentarios'         => fake()->optional()->sentence(),
            'fotos'               => null,
            'estado'              => 'pendiente',
            'comentario_mecanico' => null,
            'coste'               => null,
            'mecanico_id'         => null,
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => 'pendiente']);
    }

    public function aceptada(User $mecanico = null): static
    {
        return $this->state(fn () => [
            'estado'      => 'aceptada',
            'mecanico_id' => $mecanico?->id ?? User::factory()->mecanico(),
        ]);
    }

    public function enProceso(User $mecanico = null): static
    {
        return $this->state(fn () => [
            'estado'      => 'en_proceso',
            'mecanico_id' => $mecanico?->id ?? User::factory()->mecanico(),
        ]);
    }

    public function finalizada(User $mecanico = null): static
    {
        return $this->state(fn () => [
            'estado'              => 'finalizada',
            'mecanico_id'         => $mecanico?->id ?? User::factory()->mecanico(),
            'comentario_mecanico' => fake()->sentence(),
            'coste'               => fake()->randomFloat(2, 40, 600),
        ]);
    }

    public function pagada(User $mecanico = null): static
    {
        return $this->state(fn () => [
            'estado'              => 'pagada',
            'mecanico_id'         => $mecanico?->id ?? User::factory()->mecanico(),
            'comentario_mecanico' => fake()->sentence(),
            'coste'               => fake()->randomFloat(2, 40, 600),
        ]);
    }
}
