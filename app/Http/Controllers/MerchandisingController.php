<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class MerchandisingController extends Controller
{
    public function index()
    {
        $productos = Producto::where('activo', true)->latest()->get();
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
}
