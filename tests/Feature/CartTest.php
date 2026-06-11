<?php

use App\Models\Producto;
use App\Models\User;

test('un usuario puede añadir un producto al carrito', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10]);

    $response = $this->actingAs($user)->post(route('cart.add'), [
        'producto_id' => $producto->id,
        'cantidad' => 2,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $cart = session('cart');
    expect($cart)->toHaveKey((string) $producto->id)
        ->and($cart[$producto->id]['cantidad'])->toBe(2);
});

test('no se puede añadir más cantidad que el stock disponible', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 1]);

    $this->actingAs($user)->post(route('cart.add'), [
        'producto_id' => $producto->id,
        'cantidad' => 5,
    ])->assertSessionHas('error');
});

test('se puede actualizar la cantidad de un producto en el carrito', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10]);

    // Añadir primero
    $this->actingAs($user)->post(route('cart.add'), [
        'producto_id' => $producto->id,
        'cantidad' => 1,
    ]);

    // Actualizar
    $this->actingAs($user)->post(route('cart.update', $producto->id), [
        'cantidad' => 5,
    ]);

    $cart = session('cart');
    expect($cart[$producto->id]['cantidad'])->toBe(5);
});

test('se puede eliminar un producto del carrito', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10]);

    $this->actingAs($user)->post(route('cart.add'), [
        'producto_id' => $producto->id,
        'cantidad' => 1,
    ]);

    $this->actingAs($user)->post(route('cart.remove', $producto->id));

    $cart = session('cart');
    expect($cart)->not->toHaveKey((string) $producto->id);
});

test('se puede vaciar el carrito completo', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10]);

    $this->actingAs($user)->post(route('cart.add'), [
        'producto_id' => $producto->id,
        'cantidad' => 1,
    ]);

    $this->actingAs($user)->post(route('cart.clear'));

    $cart = session('cart');
    expect($cart)->toBeNull();
});

test('añadir al carrito requiere producto_id y cantidad válidos', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('cart.add'), [])
        ->assertSessionHasErrors(['producto_id', 'cantidad']);
});

test('la cantidad debe ser un entero entre 1 y 10', function () {
    $user = User::factory()->create();
    $producto = Producto::factory()->create(['stock' => 10]);

    $this->actingAs($user)
        ->post(route('cart.add'), [
            'producto_id' => $producto->id,
            'cantidad' => 15,
        ])
        ->assertSessionHasErrors('cantidad');
});
