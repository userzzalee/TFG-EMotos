<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Panel de administración de usuarios.
 *
 * La protección es doble: el middleware 'admin' en las rutas ya rechaza a
 * cualquiera que no sea admin antes de llegar aquí, así que no se repite el
 * chequeo en cada método.
 */
class AdminController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('busqueda');
        $rol      = $request->input('rol');

        $usuarios = User::query()
            ->when($busqueda, fn($q) => $q->where(function ($q) use ($busqueda) {
                $q->where('name',  'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%");
            }))
            ->when($rol, fn($q) => $q->where('rol', $rol))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totales = [
            'total'    => User::count(),
            'admin'    => User::where('rol', 'admin')->count(),
            'mecanico' => User::where('rol', 'mecanico')->count(),
            'user'     => User::where('rol', 'user')->count(),
        ];

        return view('admin.usuarios', compact('usuarios', 'totales', 'busqueda', 'rol'));
    }

    public function actualizarRol(Request $request, User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $request->validate([
            'rol' => ['required', 'in:user,mecanico,admin'],
        ]);

        $usuario->update(['rol' => $request->rol]);

        return back()->with('success', "Rol de {$usuario->name} actualizado a {$request->rol}.");
    }

    public function eliminar(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta desde aquí.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return back()->with('success', "Usuario {$nombre} eliminado correctamente.");
    }
}
