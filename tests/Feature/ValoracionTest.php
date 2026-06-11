<?php

use App\Models\Anuncio;
use App\Models\User;

test('un comprador puede valorar al vendedor de un anuncio vendido', function () {
    $vendedor  = User::factory()->create();
    $comprador = User::factory()->create();
    $anuncio   = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);

    $response = $this->actingAs($comprador)->post(route('segundamano.valorar', $anuncio->id), [
        'puntuacion' => 5,
        'comentario' => 'Trato excelente, todo perfecto.',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('valoraciones', [
        'autor_id'    => $comprador->id,
        'vendedor_id' => $vendedor->id,
        'anuncio_id'  => $anuncio->id,
        'puntuacion'  => 5,
    ]);
});

test('la nota media del vendedor refleja sus valoraciones', function () {
    $vendedor = User::factory()->create();

    $a1 = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);
    $a2 = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);

    $this->actingAs(User::factory()->create())
        ->post(route('segundamano.valorar', $a1->id), ['puntuacion' => 4]);
    $this->actingAs(User::factory()->create())
        ->post(route('segundamano.valorar', $a2->id), ['puntuacion' => 2]);

    expect($vendedor->fresh()->notaMedia())->toBe(3.0)
        ->and($vendedor->totalValoraciones())->toBe(2);
});

test('no se puede valorar un anuncio que no está vendido', function () {
    $vendedor  = User::factory()->create();
    $comprador = User::factory()->create();
    $anuncio   = Anuncio::factory()->create(['user_id' => $vendedor->id]); // disponible

    $this->actingAs($comprador)->post(route('segundamano.valorar', $anuncio->id), [
        'puntuacion' => 5,
    ]);

    $this->assertDatabaseCount('valoraciones', 0);
});

test('el vendedor no puede valorarse a sí mismo', function () {
    $vendedor = User::factory()->create();
    $anuncio  = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);

    $this->actingAs($vendedor)->post(route('segundamano.valorar', $anuncio->id), [
        'puntuacion' => 5,
    ]);

    $this->assertDatabaseCount('valoraciones', 0);
});

test('un comprador no puede valorar el mismo anuncio dos veces', function () {
    $vendedor  = User::factory()->create();
    $comprador = User::factory()->create();
    $anuncio   = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);

    $this->actingAs($comprador)->post(route('segundamano.valorar', $anuncio->id), ['puntuacion' => 5]);
    $this->actingAs($comprador)->post(route('segundamano.valorar', $anuncio->id), ['puntuacion' => 1]);

    $this->assertDatabaseCount('valoraciones', 1);
});

test('la puntuación debe estar entre 1 y 5', function () {
    $vendedor  = User::factory()->create();
    $comprador = User::factory()->create();
    $anuncio   = Anuncio::factory()->vendido()->create(['user_id' => $vendedor->id]);

    $this->actingAs($comprador)
        ->post(route('segundamano.valorar', $anuncio->id), ['puntuacion' => 9])
        ->assertSessionHasErrors('puntuacion');
});
