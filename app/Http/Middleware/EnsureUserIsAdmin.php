<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Solo deja pasar a usuarios autenticados con rol de administrador.
 * Se usa con el alias 'admin' en las rutas (ver bootstrap/app.php).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->esAdmin()) {
            abort(403, 'Acceso reservado a administradores.');
        }

        return $next($request);
    }
}
