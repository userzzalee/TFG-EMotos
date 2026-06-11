<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Conversacion;
use App\Http\Requests\StoreAnuncioRequest;
use App\Http\Requests\UpdateAnuncioRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SegundaManoController extends Controller
{
    // Listado público de anuncios

    public function index(Request $request): View
    {
        $query = Anuncio::with('vendedor')->disponibles()->latest();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $anuncios   = $query->paginate(12)->withQueryString();
        $categorias = Anuncio::categorias();

        return view('segundamano.index', compact('anuncios', 'categorias'));
    }


    // Detalle de un anuncio

    public function show(Anuncio $anuncio): View
    {
        abort_if(!$anuncio->activo && !$anuncio->vendido, 404);

        $conversacionExistente = null;
        if (Auth::check() && Auth::id() !== $anuncio->user_id) {
            $conversacionExistente = Conversacion::where('comprador_id', Auth::id())
                ->where('vendedor_id', $anuncio->user_id)
                ->where('anuncio_id', $anuncio->id)
                ->first();
        }

        return view('segundamano.show', compact('anuncio', 'conversacionExistente'));
    }


    // Crear anuncio
    public function create(): View
    {
        $categorias = Anuncio::categorias();
        $estados    = Anuncio::estados();
        return view('segundamano.crear', compact('categorias', 'estados'));
    }

    public function store(StoreAnuncioRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('anuncios', 'public');
        }

        $data['user_id'] = Auth::id();

        Anuncio::create($data);

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio publicado correctamente.');
    }


    // Editar anuncio
    public function edit(Anuncio $anuncio): View
    {
        $this->authorize('update', $anuncio);
        $categorias = Anuncio::categorias();
        $estados    = Anuncio::estados();
        return view('segundamano.editar', compact('anuncio', 'categorias', 'estados'));
    }

    public function update(UpdateAnuncioRequest $request, Anuncio $anuncio): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($anuncio->imagen) Storage::disk('public')->delete($anuncio->imagen);
            $data['imagen'] = $request->file('imagen')->store('anuncios', 'public');
        }

        $data['vendido'] = $request->boolean('vendido');

        $anuncio->update($data);

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio actualizado correctamente.');
    }


    // Eliminar anuncio

    public function destroy(Anuncio $anuncio): RedirectResponse
    {
        $this->authorize('delete', $anuncio);

        if ($anuncio->imagen) Storage::disk('public')->delete($anuncio->imagen);
        $anuncio->delete();

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio eliminado.');
    }


    // Mis anuncios

    public function misAnuncios(): View
    {
        $anuncios = Anuncio::where('user_id', Auth::id())->latest()->get();
        return view('segundamano.mis-anuncios', compact('anuncios'));
    }

   
    // Contactar con el vendedor
    public function contactar(Anuncio $anuncio): RedirectResponse
    {
        $compradorId = Auth::id();
        $vendedorId  = $anuncio->user_id;

        if ($compradorId === $vendedorId) {
            return back()->with('error', 'No puedes contactar contigo mismo.');
        }

        $conversacion = Conversacion::firstOrCreate(
            [
                'comprador_id' => $compradorId,
                'vendedor_id'  => $vendedorId,
                'anuncio_id'   => $anuncio->id,
            ],
            ['ultimo_mensaje_at' => now()]
        );

        return redirect()->route('chat.show', $conversacion->id);
    }
}