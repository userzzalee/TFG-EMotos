<?php

use App\Models\User;
use App\Support\AgendaTaller;
use Carbon\Carbon;

/**
 * Devuelve un slot de cita válido: el próximo lunes a las 09:00, que siempre
 * cae en día laborable, coincide con una franja y respeta la antelación mínima.
 */
function slotValido(): Carbon
{
    $slot = Carbon::now()->next(Carbon::MONDAY)->setTime(9, 0);

    // Si por antelación no fuese válido, saltamos a la semana siguiente.
    if (! AgendaTaller::esSlotValido($slot)) {
        $slot = $slot->addWeek();
    }

    return $slot;
}

test('un invitado no puede crear una cita de taller', function () {
    $this->get(route('taller.crear'))
        ->assertRedirect(route('login'));
});

test('un usuario autenticado puede crear una cita de taller', function () {
    $user = User::factory()->create();
    $slot = slotValido();

    $response = $this->actingAs($user)->post(route('taller.guardar'), [
        'marca'      => 'Honda',
        'modelo'     => 'CBR 600',
        'matricula'  => '1234 ABC',
        'fecha_cita' => $slot->format('Y-m-d H:i:s'),
        'problema'   => 'Revisión general y cambio de aceite.',
    ]);

    $response->assertRedirect(route('taller.mis-citas'));

    $this->assertDatabaseHas('citas_taller', [
        'user_id'   => $user->id,
        'marca'     => 'Honda',
        'modelo'    => 'CBR 600',
        'matricula' => '1234 ABC',
        'estado'    => 'pendiente',
    ]);
});

test('la cita exige marca, modelo, matrícula, fecha y problema', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('taller.guardar'), [])
        ->assertSessionHasErrors(['marca', 'modelo', 'matricula', 'fecha_cita', 'problema']);
});

test('una matrícula con formato inválido es rechazada', function () {
    $user = User::factory()->create();
    $slot = slotValido();

    $this->actingAs($user)
        ->post(route('taller.guardar'), [
            'marca'      => 'Yamaha',
            'modelo'     => 'MT-07',
            'matricula'  => 'INVALIDA',
            'fecha_cita' => $slot->format('Y-m-d H:i:s'),
            'problema'   => 'La moto no arranca en frío por las mañanas.',
        ])
        ->assertSessionHasErrors('matricula');
});

test('no se puede reservar una cita fuera del horario del taller', function () {
    $user = User::factory()->create();
    // Domingo (día no laborable) a las 03:00.
    $slot = Carbon::now()->next(Carbon::SUNDAY)->setTime(3, 0);

    $this->actingAs($user)
        ->post(route('taller.guardar'), [
            'marca'      => 'Kawasaki',
            'modelo'     => 'Z900',
            'matricula'  => '5678 DEF',
            'fecha_cita' => $slot->format('Y-m-d H:i:s'),
            'problema'   => 'Ruido extraño al acelerar en marcha.',
        ])
        ->assertSessionHasErrors('fecha_cita');
});
