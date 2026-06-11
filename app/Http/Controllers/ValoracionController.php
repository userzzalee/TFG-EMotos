<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValoracionRequest;
use App\Models\Anuncio;
use App\Models\Valoracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ValoracionController extends Controller
{
    /**
     * Registra la valoración de un comprador hacia el vendedor de un anuncio.
     */
    public function store(StoreValoracionRequest $request, Anuncio $anuncio): RedirectResponse
    {
        // La policy comprueba que el anuncio esté vendido, que no sea el propio
        // vendedor y que no haya valorado ya.
        if (Gate::denies('valorar-anuncio', $anuncio)) {
            return back()->with('error', 'No puedes valorar este anuncio.');
        }

        Valoracion::create([
            'autor_id'    => Auth::id(),
            'vendedor_id' => $anuncio->user_id,
            'anuncio_id'  => $anuncio->id,
            'puntuacion'  => $request->integer('puntuacion'),
            'comentario'  => $request->input('comentario'),
        ]);

        return back()->with('success', 'Gracias por valorar al vendedor.');
    }
}
