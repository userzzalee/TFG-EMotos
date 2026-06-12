<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Conversacion;
use App\Http\Requests\StoreAnuncioRequest;
use App\Http\Requests\UpdateAnuncioRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SegundaManoController extends Controller
{
    // Listado público de anuncios
    public function index(Request $request): View
    {
        $query = Anuncio::with('vendedor')->disponibles();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('buscar')) {
            $term = mb_strtolower($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(descripcion) LIKE ?', ["%{$term}%"]);
            });
        }

        // Filtros de rango de precio + ordenación (feature 10).
        $query->precioMin($request->input('precio_min'))
              ->precioMax($request->input('precio_max'))
              ->ordenar($request->input('orden'));

        $anuncios     = $query->paginate(12)->withQueryString();
        $categorias   = Anuncio::categorias();
        $ordenaciones = Anuncio::ordenaciones();

        return view('segundamano.index', compact('anuncios', 'categorias', 'ordenaciones'));
    }


    // Detalle de un anuncio
    public function show(Anuncio $anuncio): View
    {
        abort_if(!$anuncio->activo && !$anuncio->vendido, 404);

        $anuncio->load(['imagenes', 'valoraciones.autor']);

        $conversacionExistente = null;
        if (Auth::check() && Auth::id() !== $anuncio->user_id) {
            $conversacionExistente = Conversacion::where('comprador_id', Auth::id())
                ->where('vendedor_id', $anuncio->user_id)
                ->where('anuncio_id', $anuncio->id)
                ->first();
        }

        // ¿Puede el usuario actual valorar al vendedor? (feature 9)
        $puedeValorar = Auth::check() && Gate::allows('valorar-anuncio', $anuncio);

        return view('segundamano.show', compact('anuncio', 'conversacionExistente', 'puedeValorar'));
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

        // No persistimos el array de galería en la tabla anuncios.
        unset($data['imagenes']);

        $anuncio = Anuncio::create($data);

        // Galería de imágenes (feature 11).
        $this->guardarGaleria($request, $anuncio);

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio publicado correctamente.');
    }


    // Editar anuncio
    public function edit(Anuncio $anuncio): View
    {
        $this->authorize('update', $anuncio);
        $anuncio->load('imagenes');
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

        unset($data['imagenes'], $data['eliminar_imagenes']);

        $anuncio->update($data);

        // Eliminar imágenes marcadas (feature 11).
        if ($request->filled('eliminar_imagenes')) {
            $imagenes = $anuncio->imagenes()
                ->whereIn('id', $request->input('eliminar_imagenes'))
                ->get();

            foreach ($imagenes as $img) {
                Storage::disk('public')->delete($img->ruta);
                $img->delete();
            }
        }

        // Añadir nuevas imágenes a la galería.
        $this->guardarGaleria($request, $anuncio);

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio actualizado correctamente.');
    }


    // Eliminar anuncio
    public function destroy(Anuncio $anuncio): RedirectResponse
    {
        $this->authorize('delete', $anuncio);

        if ($anuncio->imagen) Storage::disk('public')->delete($anuncio->imagen);

        // Borrar también los ficheros de la galería (los registros caen por cascade).
        foreach ($anuncio->imagenes as $img) {
            Storage::disk('public')->delete($img->ruta);
        }

        $anuncio->delete();

        return redirect()->route('segundamano.mis-anuncios')
                         ->with('success', 'Anuncio eliminado.');
    }


    // Mis anuncios
    public function misAnuncios(): View
    {
        $anuncios = Anuncio::where('user_id', Auth::id())
            ->with('imagenes')
            ->latest()
            ->get();
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


    /**
     * Guarda las imágenes subidas en la galería del anuncio (feature 11).
     */
    private function guardarGaleria(\Illuminate\Http\Request $request, Anuncio $anuncio): void
    {
        if (! $request->hasFile('imagenes')) {
            return;
        }

        // Empezamos a partir del orden máximo actual para no pisar las existentes.
        $orden = (int) $anuncio->imagenes()->max('orden');

        foreach ($request->file('imagenes') as $archivo) {
            $ruta = $archivo->store('anuncios', 'public');
            $anuncio->imagenes()->create([
                'ruta'  => $ruta,
                'orden' => ++$orden,
            ]);
        }

        // Si el anuncio no tenía imagen de portada, usamos la primera de la galería.
        if (! $anuncio->imagen) {
            $primera = $anuncio->imagenes()->orderBy('orden')->first();
            if ($primera) {
                $anuncio->update(['imagen' => $primera->ruta]);
            }
        }
    }
}
