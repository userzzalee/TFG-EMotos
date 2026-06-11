<?php

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;

beforeEach(function () {
    $this->comprador = User::factory()->create();
    $this->vendedor  = User::factory()->create();

    $this->conversacion = Conversacion::create([
        'comprador_id'      => $this->comprador->id,
        'vendedor_id'       => $this->vendedor->id,
        'ultimo_mensaje_at' => now(),
    ]);
});

test('un participante puede enviar un mensaje en su conversación', function () {
    $response = $this->actingAs($this->comprador)
        ->post(route('chat.enviar', $this->conversacion->id), [
            'contenido' => 'Hola, ¿sigue disponible?',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('mensajes', [
        'conversacion_id' => $this->conversacion->id,
        'remitente_id'    => $this->comprador->id,
        'contenido'       => 'Hola, ¿sigue disponible?',
    ]);
});

test('enviar un mensaje actualiza la marca de último mensaje', function () {
    $this->conversacion->update(['ultimo_mensaje_at' => now()->subDay()]);
    $antes = $this->conversacion->ultimo_mensaje_at;

    $this->actingAs($this->vendedor)
        ->post(route('chat.enviar', $this->conversacion->id), [
            'contenido' => 'Sí, disponible.',
        ]);

    expect($this->conversacion->fresh()->ultimo_mensaje_at->gt($antes))->toBeTrue();
});

test('un mensaje vacío es rechazado', function () {
    $this->actingAs($this->comprador)
        ->post(route('chat.enviar', $this->conversacion->id), ['contenido' => ''])
        ->assertSessionHasErrors('contenido');
});

test('un usuario ajeno no puede escribir en la conversación', function () {
    $intruso = User::factory()->create();

    $this->actingAs($intruso)
        ->post(route('chat.enviar', $this->conversacion->id), [
            'contenido' => 'No debería poder',
        ])
        ->assertForbidden();
});

test('abrir la conversación marca como leídos los mensajes del otro', function () {
    $mensaje = Mensaje::create([
        'conversacion_id' => $this->conversacion->id,
        'remitente_id'    => $this->vendedor->id,
        'contenido'       => 'Mensaje sin leer',
    ]);

    expect($mensaje->leido_at)->toBeNull();

    $this->actingAs($this->comprador)
        ->get(route('chat.show', $this->conversacion->id))
        ->assertOk();

    expect($mensaje->fresh()->leido_at)->not->toBeNull();
});

test('la primera página del chat muestra los mensajes más recientes en orden cronológico', function () {
    // Creamos 60 mensajes (más de una página de 50) con fechas crecientes.
    for ($i = 1; $i <= 60; $i++) {
        Mensaje::create([
            'conversacion_id' => $this->conversacion->id,
            'remitente_id'    => $this->comprador->id,
            'contenido'       => "Mensaje {$i}",
            'created_at'      => now()->addMinutes($i),
            'updated_at'      => now()->addMinutes($i),
        ]);
    }

    $mensajes = $this->actingAs($this->comprador)
        ->get(route('chat.show', $this->conversacion->id))
        ->viewData('mensajes');

    // La página 1 trae los 50 más recientes (del 11 al 60)...
    expect($mensajes)->toHaveCount(50);

    // ...y se pintan en orden cronológico: el primero de la página es anterior al último.
    $coleccion = $mensajes->getCollection();
    expect($coleccion->first()->contenido)->toBe('Mensaje 11')
        ->and($coleccion->last()->contenido)->toBe('Mensaje 60');

    // Quedan mensajes más antiguos en la siguiente página.
    expect($mensajes->hasMorePages())->toBeTrue();
});

test('la segunda página del chat contiene los mensajes más antiguos', function () {
    for ($i = 1; $i <= 60; $i++) {
        Mensaje::create([
            'conversacion_id' => $this->conversacion->id,
            'remitente_id'    => $this->comprador->id,
            'contenido'       => "Mensaje {$i}",
            'created_at'      => now()->addMinutes($i),
            'updated_at'      => now()->addMinutes($i),
        ]);
    }

    $mensajes = $this->actingAs($this->comprador)
        ->get(route('chat.show', $this->conversacion->id) . '?page=2')
        ->viewData('mensajes');

    // La página 2 trae los 10 más antiguos (del 1 al 10), en orden cronológico.
    $coleccion = $mensajes->getCollection();
    expect($coleccion)->toHaveCount(10)
        ->and($coleccion->first()->contenido)->toBe('Mensaje 1')
        ->and($coleccion->last()->contenido)->toBe('Mensaje 10');
});
