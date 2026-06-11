<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    /**
     * Página completa con todas las notificaciones.
     */
    public function index(): View
    {
        $notificaciones = Auth::user()->notifications()->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Datos para el dropdown del navbar (se pide por AJAX al abrirlo).
     */
    public function recientes(): JsonResponse
    {
        $user = Auth::user();

        $notificaciones = $user->notifications()
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($n) => [
                'id'        => $n->id,
                'leida'     => $n->read_at !== null,
                'icono'     => $n->data['icono']   ?? '🔔',
                'titulo'    => $n->data['titulo']  ?? 'Notificación',
                'mensaje'   => $n->data['mensaje'] ?? '',
                'url'       => route('notificaciones.abrir', $n->id),
                'hace'      => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'no_leidas'      => $user->unreadNotifications()->count(),
            'notificaciones' => $notificaciones,
        ]);
    }

    /**
     * Marca una notificación como leída y redirige a su destino.
     */
    public function abrir(string $id): RedirectResponse
    {
        $notificacion = Auth::user()->notifications()->findOrFail($id);

        $notificacion->markAsRead();

        $destino = $notificacion->data['url'] ?? route('notificaciones.index');

        return redirect()->to($destino);
    }

    /**
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function leerTodas(): RedirectResponse|JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['ok' => true, 'no_leidas' => 0]);
        }

        return back();
    }
}
