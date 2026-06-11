<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\Request;

class MerchandisingController extends Controller
{
    public function index(Request $request)
    {
        $busqueda  = $request->input('buscar');
        $precioMin = $request->input('precio_min');
        $precioMax = $request->input('precio_max');

        $query = Producto::where('activo', true);

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        if ($precioMin) {
            $query->where('precio', '>=', $precioMin);
        }

        if ($precioMax) {
            $query->where('precio', '<=', $precioMax);
        }

        $productos = $query->latest()->get();

        return view('merchandising', compact('productos'));
    }

    public function create()
    {
        return view('admin.crear-producto');
    }

    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();

        $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        $data['activo'] = true;

        Producto::create($data);

        return redirect()->route('merchandising')->with('success', 'Producto creado exitosamente');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('admin.editar-producto', compact('producto'));
    }

    public function update(UpdateProductoRequest $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        } else {
            unset($data['imagen']);
        }

        $producto->update($data);

        return redirect()->route('merchandising')->with('success', 'Producto actualizado exitosamente');
    }
}
