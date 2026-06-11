<?php

namespace Database\Seeders;

use App\Models\Anuncio;
use App\Models\CitaTaller;
use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\Order;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Datos de prueba para desarrollo y demos del TFG.
     * Todas las cuentas usan la contraseña "password".
     */
    public function run(): void
    {
        // ── Cuentas fijas con rol conocido ──────────────────────────────
        $admin = User::factory()->admin()->create([
            'name'  => 'Admin Demo',
            'email' => 'admin@alyx.test',
        ]);

        $mecanico = User::factory()->mecanico()->create([
            'name'  => 'Mecánico Demo',
            'email' => 'mecanico@alyx.test',
        ]);

        $cliente = User::factory()->create([
            'name'  => 'Cliente Demo',
            'email' => 'cliente@alyx.test',
        ]);

        // Usuario de prueba histórico (compatibilidad).
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ── Usuarios variados ───────────────────────────────────────────
        $usuarios   = User::factory(10)->create();
        $mecanicos  = User::factory(2)->mecanico()->create()->push($mecanico);
        $compradores = $usuarios->push($cliente);

        // ── Productos de merchandising ──────────────────────────────────
        $productos = Producto::factory(9)->create();

        // ── Anuncios de segunda mano ────────────────────────────────────
        // Disponibles
        Anuncio::factory(15)->recycle($compradores)->create();

        // Vendidos (servirán para generar valoraciones)
        $anunciosVendidos = Anuncio::factory(8)->vendido()->recycle($compradores)->create();

        // ── Valoraciones sobre los anuncios vendidos ────────────────────
        foreach ($anunciosVendidos as $anuncio) {
            // Un comprador distinto al vendedor valora la venta.
            $autor = $compradores->where('id', '!=', $anuncio->user_id)->random();

            Valoracion::factory()->create([
                'autor_id'    => $autor->id,
                'vendedor_id' => $anuncio->user_id,
                'anuncio_id'  => $anuncio->id,
            ]);
        }

        // ── Citas del taller en distintos estados ───────────────────────
        CitaTaller::factory(4)->pendiente()->recycle($compradores)->create();
        CitaTaller::factory(3)->aceptada($mecanico)->recycle($compradores)->create();
        CitaTaller::factory(2)->enProceso($mecanico)->recycle($compradores)->create();
        CitaTaller::factory(3)->finalizada($mecanico)->recycle($compradores)->create();
        CitaTaller::factory(5)->pagada($mecanico)->recycle($compradores)->create();

        // ── Pedidos de tienda con artículos del catálogo ────────────────
        foreach ($compradores->take(6) as $comprador) {
            $items = $productos->random(rand(1, 3))->map(fn ($p) => [
                'nombre'   => $p->nombre,
                'precio'   => (float) $p->precio,
                'imagen'   => $p->imagen,
                'cantidad' => rand(1, 2),
            ])->values()->all();

            $total = collect($items)->sum(fn ($i) => $i['precio'] * $i['cantidad']);

            Order::create([
                'user_id'              => $comprador->id,
                'total'                => $total,
                'status'               => fake()->randomElement(['pending', 'completed']),
                'payment_method'       => fake()->randomElement(['card', 'cash']),
                'shipping_address'     => fake()->streetAddress(),
                'shipping_city'        => fake()->city(),
                'shipping_postal_code' => fake()->postcode(),
                'shipping_phone'       => fake()->numerify('6########'),
                'items'                => $items,
            ]);
        }

        // ── Una conversación de chat de ejemplo ─────────────────────────
        $vendedor = $usuarios->first();
        if ($vendedor && $vendedor->id !== $cliente->id) {
            $conversacion = Conversacion::create([
                'comprador_id'      => $cliente->id,
                'vendedor_id'       => $vendedor->id,
                'ultimo_mensaje_at' => now(),
            ]);

            Mensaje::create([
                'conversacion_id' => $conversacion->id,
                'remitente_id'    => $cliente->id,
                'contenido'       => 'Hola, ¿sigue disponible el artículo?',
            ]);
            Mensaje::create([
                'conversacion_id' => $conversacion->id,
                'remitente_id'    => $vendedor->id,
                'contenido'       => '¡Hola! Sí, sigue disponible. ¿Te interesa?',
            ]);
        }
    }
}
