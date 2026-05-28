<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class MerchandisingController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('buscar');
        $precioMin = $request->input('precio_min');
        $precioMax = $request->input('precio_max');

        $query = Producto::where('activo', true);

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
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

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:100',
        ]);

        $imagenPath = $request->file('imagen')->store('productos', 'public');

        Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'imagen' => $imagenPath,
            'stock' => $request->stock,
            'categoria' => $request->categoria,
            'activo' => true,
        ]);

        return redirect()->route('merchandising')->with('success', 'Producto creado exitosamente');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('admin.editar-producto', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:100',
        ]);

        $producto = Producto::findOrFail($id);

        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagenPath;
        }

        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->categoria = $request->categoria;
        $producto->save();

        return redirect()->route('merchandising')->with('success', 'Producto actualizado exitosamente');
    }
}
