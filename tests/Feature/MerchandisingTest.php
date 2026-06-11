<?php

use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('cualquier usuario puede ver el listado de merchandising', function () {
    Producto::factory()->create(['activo' => true]);

    $this->get(route('merchandising'))
        ->assertOk();
});

test('un invitado no puede acceder al formulario de crear producto', function () {
    $this->get(route('merchandising.create'))
        ->assertRedirect(route('login'));
});

test('un usuario normal no puede acceder al formulario de crear producto', function () {
    $user = User::factory()->create(['rol' => 'user']);

    $this->actingAs($user)
        ->get(route('merchandising.create'))
        ->assertForbidden();
});

test('un admin puede crear un producto', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['rol' => 'admin']);

    $response = $this->actingAs($admin)->post(route('merchandising.store'), [
        'nombre'      => 'Camiseta AlyX Test',
        'descripcion' => 'Camiseta de prueba para tests',
        'precio'      => 29.99,
        'stock'       => 20,
        'categoria'   => 'ropa',
        'imagen'      => UploadedFile::fake()->image('camiseta.jpg'),
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('productos', [
        'nombre' => 'Camiseta AlyX Test',
        'precio' => 29.99,
        'stock'  => 20,
    ]);
});

test('crear producto requiere nombre, precio e imagen', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->post(route('merchandising.store'), [])
        ->assertSessionHasErrors(['nombre', 'precio', 'imagen']);
});

test('un admin puede editar un producto existente', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['rol' => 'admin']);
    $producto = Producto::factory()->create();

    $this->actingAs($admin)
        ->post(route('merchandising.update', $producto->id), [
            'nombre'      => 'Producto Editado',
            'descripcion' => 'Descripción actualizada',
            'precio'      => 49.99,
            'stock'       => 5,
            'categoria'   => 'accesorios',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('productos', [
        'id'     => $producto->id,
        'nombre' => 'Producto Editado',
        'precio' => 49.99,
    ]);
});
