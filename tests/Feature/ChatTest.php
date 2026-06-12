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

test('el chat pagina correctamente con más de 50 mensajes', function () {
    for ($i = 1; $i <= 60; $i++) {
        Mensaje::forceCreate([
            'conversacion_id' => $this->conversacion->id,
            'remitente_id'    => $this->comprador->id,
            'contenido'       => "Mensaje {$i}",
            'created_at'      => now()->addMinutes($i),
            'updated_at'      => now()->addMinutes($i),
        ]);
    }

    // Página 1: 50 mensajes y hay más páginas disponibles.
    $mensajes = $this->actingAs($this->comprador)
        ->get(route('chat.show', $this->conversacion->id))
        ->viewData('mensajes');

    expect($mensajes)->toHaveCount(50)
        ->and($mensajes->hasMorePages())->toBeTrue();

    // Página 2: los 10 mensajes restantes.
    $mensajesPag2 = $this->actingAs($this->comprador)
        ->get(route('chat.show', $this->conversacion->id) . '?page=2')
        ->viewData('mensajes');

    expect($mensajesPag2)->toHaveCount(10)
        ->and($mensajesPag2->hasMorePages())->toBeFalse();
});
