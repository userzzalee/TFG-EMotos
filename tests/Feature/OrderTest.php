<?php

use App\Models\Order;
use App\Models\Producto;
use App\Models\User;

test('un invitado no puede acceder al checkout', function () {
    $this->get(route('order.checkout'))
        ->assertRedirect(route('login'));
});

test('checkout con carrito vacío redirige al carrito', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('order.checkout'))
        ->assertRedirect(route('cart.index'));
});

test('un usuario puede crear un pedido con datos válidos', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10, 'precio' => 25.00]);

    // Simular carrito en sesión
    $this->actingAs($user)
        ->withSession(['cart' => [
            $producto->id => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => 25.00,
                'imagen' => $producto->imagen,
                'cantidad' => 2,
                'categoria' => $producto->categoria,
            ],
        ]])
        ->post(route('order.store'), [
            'shipping_address' => 'Calle Mayor 15, 3ºA',
            'shipping_city' => 'Madrid',
            'shipping_postal_code' => '28001',
            'shipping_phone' => '612345678',
            'payment_method' => 'card',
        ]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'total' => 50.00,
        'status' => 'pending',
        'shipping_city' => 'Madrid',
    ]);
});

test('crear pedido requiere datos de envío válidos', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['cart' => ['dummy' => ['precio' => 10, 'cantidad' => 1]]])
        ->post(route('order.store'), [])
        ->assertSessionHasErrors([
            'shipping_address',
            'shipping_city',
            'shipping_postal_code',
            'shipping_phone',
            'payment_method',
        ]);
});

test('el código postal debe tener 5 dígitos', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['cart' => ['dummy' => ['precio' => 10, 'cantidad' => 1]]])
        ->post(route('order.store'), [
            'shipping_address' => 'Calle Mayor 15, 3ºA',
            'shipping_city' => 'Madrid',
            'shipping_postal_code' => '123',
            'shipping_phone' => '612345678',
            'payment_method' => 'card',
        ])
        ->assertSessionHasErrors('shipping_postal_code');
});

test('el teléfono debe tener formato español', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['cart' => ['dummy' => ['precio' => 10, 'cantidad' => 1]]])
        ->post(route('order.store'), [
            'shipping_address' => 'Calle Mayor 15, 3ºA',
            'shipping_city' => 'Madrid',
            'shipping_postal_code' => '28001',
            'shipping_phone' => '12345',
            'payment_method' => 'card',
        ])
        ->assertSessionHasErrors('shipping_phone');
});

test('solo el dueño puede ver su pedido', function () {
    $dueño = User::factory()->create();
    $otro = User::factory()->create();

    $order = Order::create([
        'user_id' => $dueño->id,
        'total' => 50.00,
        'status' => 'pending',
        'payment_method' => 'card',
        'shipping_address' => 'Calle Test 1',
        'shipping_city' => 'Madrid',
        'shipping_postal_code' => '28001',
        'shipping_phone' => '612345678',
        'items' => [],
    ]);

    $this->actingAs($otro)
        ->get(route('order.show', $order->id))
        ->assertForbidden();
});

test('el dueño puede ver su pedido', function () {
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'total' => 50.00,
        'status' => 'pending',
        'payment_method' => 'card',
        'shipping_address' => 'Calle Test 1',
        'shipping_city' => 'Madrid',
        'shipping_postal_code' => '28001',
        'shipping_phone' => '612345678',
        'items' => [],
    ]);

    $this->actingAs($user)
        ->get(route('order.show', $order->id))
        ->assertOk();
});

test('un usuario puede ver su historial de pedidos', function () {
    $user = User::factory()->create();

    Order::create([
        'user_id' => $user->id,
        'total' => 30.00,
        'status' => 'pending',
        'payment_method' => 'cash',
        'shipping_address' => 'Calle Test 1',
        'shipping_city' => 'Sevilla',
        'shipping_postal_code' => '41001',
        'shipping_phone' => '612345678',
        'items' => [],
    ]);

    $this->actingAs($user)
        ->get(route('order.index'))
        ->assertOk();
});
