<?php

namespace App\Support;

use App\Models\CitaTaller;
use Carbon\Carbon;

/**
 * Calcula los huecos (slots) disponibles del taller a partir de la configuración
 * de config/taller.php y de las citas ya reservadas.
 */
class AgendaTaller
{
    /**
     * Días con sus horas libres para pintar el selector del formulario.
     *
     * @return array<int, array{fecha: string, etiqueta: string, slots: array<int, string>}>
     */
    public static function diasDisponibles(): array
    {
        $diasVista = (int) config('taller.dias_vista', 14);
        $diasLab   = config('taller.dias_laborables', [1, 2, 3, 4, 5]);
        $capacidad = (int) config('taller.capacidad_slot', 1);
        $antelacion = (int) config('taller.antelacion_minima_horas', 2);

        $ahora = Carbon::now();
        $limiteReserva = $ahora->copy()->addHours($antelacion);
        $hasta = $ahora->copy()->addDays($diasVista)->endOfDay();

        // Ocupación actual: mapa 'Y-m-d H:i' => nº de citas en ese hueco.
        $ocupacion = CitaTaller::whereNotNull('fecha_cita')
            ->whereBetween('fecha_cita', [$ahora->copy()->startOfDay(), $hasta])
            ->get(['fecha_cita'])
            ->groupBy(fn ($c) => $c->fecha_cita->format('Y-m-d H:i'))
            ->map->count();

        $resultado = [];

        for ($i = 0; $i <= $diasVista; $i++) {
            $dia = $ahora->copy()->addDays($i)->startOfDay();

            if (! in_array($dia->dayOfWeek, $diasLab, true)) {
                continue;
            }

            $slots = [];
            foreach (self::slotsDeDia($dia) as $slot) {
                if ($slot->lessThan($limiteReserva)) {
                    continue; // demasiado pronto
                }

                $ocupados = $ocupacion[$slot->format('Y-m-d H:i')] ?? 0;
                if ($ocupados < $capacidad) {
                    $slots[] = $slot->format('H:i');
                }
            }

            if (! empty($slots)) {
                $resultado[] = [
                    'fecha'    => $dia->format('Y-m-d'),
                    'etiqueta' => ucfirst($dia->locale('es')->isoFormat('ddd D MMM')),
                    'slots'    => $slots,
                ];
            }
        }

        return $resultado;
    }

    /**
     * Todas las horas teóricas de un día según las franjas (sin filtrar ocupación).
     *
     * @return array<int, Carbon>
     */
    public static function slotsDeDia(Carbon $dia): array
    {
        $duracion = (int) config('taller.duracion_slot', 60);
        $slots = [];

        foreach (config('taller.franjas', []) as [$horaInicio, $horaFin]) {
            $inicio = $dia->copy()->setTimeFromTimeString($horaInicio);
            $fin    = $dia->copy()->setTimeFromTimeString($horaFin);

            for ($t = $inicio->copy(); $t->lessThan($fin); $t->addMinutes($duracion)) {
                $slots[] = $t->copy();
            }
        }

        return $slots;
    }

    /**
     * Valida que una fecha/hora sea un hueco real: laborable, dentro de franja,
     * con la antelación mínima y con capacidad libre. Se usa al guardar la cita.
     */
    public static function esSlotValido(Carbon $fechaHora): bool
    {
        $diasLab = config('taller.dias_laborables', [1, 2, 3, 4, 5]);
        if (! in_array($fechaHora->dayOfWeek, $diasLab, true)) {
            return false;
        }

        // ¿Coincide exactamente con una de las horas generadas para ese día?
        $coincide = collect(self::slotsDeDia($fechaHora->copy()->startOfDay()))
            ->contains(fn (Carbon $s) => $s->format('H:i') === $fechaHora->format('H:i'));
        if (! $coincide) {
            return false;
        }

        // Antelación mínima.
        $minimo = Carbon::now()->addHours((int) config('taller.antelacion_minima_horas', 2));
        if ($fechaHora->lessThan($minimo)) {
            return false;
        }

        // Capacidad del hueco.
        $capacidad = (int) config('taller.capacidad_slot', 1);
        $ocupados = CitaTaller::whereNotNull('fecha_cita')
            ->where('fecha_cita', $fechaHora->format('Y-m-d H:i:00'))
            ->count();

        return $ocupados < $capacidad;
    }
}
