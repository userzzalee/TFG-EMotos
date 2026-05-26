<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Add a product to the cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1|max:10',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        
        if ($producto->stock < $request->cantidad) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$request->producto_id])) {
            $cart[$request->producto_id]['cantidad'] += $request->cantidad;
        } else {
            $cart[$request->producto_id] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'imagen' => $producto->imagen,
                'cantidad' => $request->cantidad,
                'categoria' => $producto->categoria,
            ];
        }

        Session::put('cart', $cart);

        return back()->with('success', 'Producto añadido al carrito correctamente.');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1|max:10',
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $producto = Producto::findOrFail($id);
            
            if ($producto->stock < $request->cantidad) {
                return back()->with('error', 'No hay suficiente stock disponible.');
            }

            $cart[$id]['cantidad'] = $request->cantidad;
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Carrito actualizado correctamente.');
    }

    /**
     * Remove an item from the cart
     */
    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        Session::forget('cart');

        return redirect()->route('cart.index')->with('success', 'Carrito vaciado correctamente.');
    }
}
