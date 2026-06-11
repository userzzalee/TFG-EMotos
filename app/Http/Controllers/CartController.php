<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddConfigurationRequest;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
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
    public function add(AddToCartRequest $request)
    {
        $request->validated();

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
    public function update(UpdateCartRequest $request, $id)
    {
        $request->validated();

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            // Only check stock if it's a regular product (not a custom configuration)
            if (!str_starts_with($id, 'config_')) {
                $producto = Producto::findOrFail($id);
                
                if ($producto->stock < $request->cantidad) {
                    return back()->with('error', 'No hay suficiente stock disponible.');
                }
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

    /**
     * Add a custom motorcycle configuration to the cart
     */
    public function addConfiguration(AddConfigurationRequest $request)
    {
        $request->validated();

        $nombresColores = [
            'gris' => 'Gris',
            'dorado' => 'Dorado',
            'rojo' => 'Rojo'
        ];

        $nombresModelos = [
            'enduro' => 'Enduro',
            'trail' => 'Trail'
        ];

        $cart = Session::get('cart', []);
        
        // Generate a unique ID for the configuration
        $configId = 'config_' . time();
        
        $imagenMap = [
            'enduro' => [
                '40' => ['gris' => 'images/40hpgrispng.png', 'dorado' => 'images/40hpamarillapng.png', 'rojo' => 'images/40hprojopng.png'],
                '80' => ['gris' => 'images/80hpgrispng.png', 'dorado' => 'images/80hpamarillapng.png', 'rojo' => 'images/80hprojapng.png']
            ],
            'trail' => [
                '40' => ['gris' => 'images/trail80hpnegra.png', 'dorado' => 'images/80hptrailpng.png', 'rojo' => 'images/trail80hproja.png'],
                '80' => ['gris' => 'images/trail80hpnegra.png', 'dorado' => 'images/80hptrailpng.png', 'rojo' => 'images/trail80hproja.png']
            ]
        ];
        
        $cart[$configId] = [
            'id' => $configId,
            'nombre' => 'Moto ' . $nombresModelos[$request->modelo] . ' ' . $request->motor . ' HP', 'precio' => $request->precio,
            'imagen' => $imagenMap[$request->modelo][$request->motor][$request->color],
            'cantidad' => 1,
            'categoria' => 'Configuración',
            'modelo' => $request->modelo,
            'color' => $nombresColores[$request->color],
            'motor' => $request->motor . 'HP'
        ];

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Configuración añadida al carrito correctamente.');
    }
}
