<?php

use App\Models\Anuncio;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('un invitado no puede acceder al formulario de crear anuncio', function () {
    $this->get(route('segundamano.crear'))
        ->assertRedirect(route('login'));
});

test('un usuario autenticado puede publicar un anuncio', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('segundamano.store'), [
        'titulo'      => 'Casco Shoei talla M',
        'descripcion' => 'Casco en perfecto estado, poco uso.',
        'precio'      => 150.00,
        'categoria'   => 'cascos',
        'estado'      => 'bueno',
    ]);

    $response->assertRedirect(route('segundamano.mis-anuncios'));

    $this->assertDatabaseHas('anuncios', [
        'titulo'  => 'Casco Shoei talla M',
        'user_id' => $user->id,
        'precio'  => 150.00,
    ]);
});

test('publicar un anuncio con galería de imágenes guarda las imágenes', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('segundamano.store'), [
        'titulo'   => 'Chaqueta de cuero',
        'precio'   => 80,
        'estado'   => 'usado',
        'imagenes' => [
            UploadedFile::fake()->image('foto1.jpg'),
            UploadedFile::fake()->image('foto2.jpg'),
        ],
    ]);

    $anuncio = Anuncio::firstWhere('titulo', 'Chaqueta de cuero');

    expect($anuncio)->not->toBeNull()
        ->and($anuncio->imagenes)->toHaveCount(2);
});

test('un anuncio requiere título, precio y estado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('segundamano.store'), [])
        ->assertSessionHasErrors(['titulo', 'precio', 'estado']);
});

test('solo el dueño puede editar su anuncio', function () {
    $dueño = User::factory()->create();
    $otro  = User::factory()->create();
    $anuncio = Anuncio::factory()->create(['user_id' => $dueño->id]);

    $this->actingAs($otro)
        ->get(route('segundamano.editar', $anuncio->id))
        ->assertForbidden();
});
